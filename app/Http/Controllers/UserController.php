<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use App\ContactFieldType;
use App\ContactField;
use DB;
use Hash;
use Arr;
use Auth;

use App\Http\Traits\ContactFieldTrait;
use App\Http\Traits\SearchTrait;

class UserController extends Controller
{
	use ContactFieldTrait, SearchTrait;
	
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$queryFilter = [];
		$q = $request->get('q');
		
		$srchid = $request->get('srch');
		if ($srchid) {
			$q = $this->decryptFilter($srchid);
		}
		
		$roles = Role::pluck('colour_hex','name')->all();

        $result = User::whereNull('userable_type');

		if (isset($q['username']) && ($q['username'])) {
			 $result->where('username', 'ILIKE', '%'.$q['username'].'%');
			 $queryFilter['Username'] = $q['username'];
		}

		if (isset($q['name']) && ($q['name'])) {
			 $result->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $q['name']).'%');
			 $queryFilter['Name'] = $q['name'];			 
		}
		if (isset($q['roles']) && (!empty($q['roles']))) {
			$r = $q['roles'];
			$queryFilter['Roles'] = $r;						
			$result->WhereHas("roles", function ($q) use ($r) {
					$q->whereIn('name', $r); //$q['roles']);
    });
		}

		$data = $result->orderBy('username','ASC')->paginate(25);
  
		$query = $q ? ['srch'=>$this->encryptFilter($q)] : [];
		
//        return view('users.index',compact('data', 'roles', 'q', 'queryFilter'))
//            ->with('i', ($request->input('page', 1) - 1) * 25)->withQuery($query);
		return view('users.index',compact('data', 'roles', 'q', 'queryFilter'))->withQuery($query);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
 //       $roles = Role::pluck('name','name')->all();
        $roles = Role::pluck('colour_hex','name')->all();
		  
		$contact_types = ContactFieldType::all();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
		  
        return view('users.create',compact('roles', 'contact_types'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	
		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'emailfields' => 'required|uniqueemail:users,email',
            'roles' => 'required',
			'contacts.*.data' => 'required',
            'password' => 'required|same:confirm-password'
        ], 
		['emailfields.required' => 'At least one email address is required',
		  'roles.required' => 'At least one role must be assigned']);

        $input = $request->all();

		$input['email'] = implode(',', $input['emailfields']);
        $input['password'] = Hash::make($input['password']);

//$input['firstname'] = $input['name'];
//$input['lastname'] = $input['name'];
        $input['username'] = $this->createUsername($input);

        $input['is_active'] = '1';

		if (strip_tags($input['emailsignature']) == '')
			$input['emailsignature'] = null;
		
		$success = false;
		DB::beginTransaction();
		try {

			$user = User::create($input);
			if ($user) {
				$user->assignRole($request->input('roles'));

				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $user;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}
				$success = true;
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('users.index')
                        ->with('success_message','User created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','User not created');
		}					
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
		$roles = Role::pluck('colour_hex','name')->all();
        $icons = ContactFieldType::pluck('fontawesome_icon', 'id')->all();
		$contacts = $user->contactfields()->get()->sortBy('contact_field_type_id')->sortBy('contact_field_type_id');
		
		
        return view('users.show',compact('user', 'roles', 'contacts'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
//        $roles = Role::pluck('name','name')->all();
        $roles = Role::pluck('colour_hex','name')->all();
		
		$contact_types = ContactFieldType::all();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
		$contacts = $user->contactfields()->get()->sortBy('contact_field_type_id')->sortBy('contact_field_type_id');
		
        $userRole = $user->roles->pluck('name','name')->all();
 //       $roles = Role::pluck('colour_hex','name')->all();
 //       $userRole = $user->roles->pluck('colour_hex','name')->all();


        return view('users.edit',compact('user','roles','userRole', 'contact_types', 'contacts'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		
		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
		
		$rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'emailfields' => 'required',
			'contacts.*.data' => 'required',
            'password' => 'same:confirm-password'
        ];		
//		'emailfields' => 'required|uniqueemail:users,email,'.$id,
		
		$isAdmin = (Auth::user()->hasRole('Admin'));
		if ($isAdmin) {
            $rules['roles'] = 'required';
			$route = 'users.index';
        } else {
			$route = 'home';
		}
		
        $this->validate($request, $rules, 
		['emailfields.required' => 'At least one email address is required',
		  'roles.required' => 'At least one role must be assigned']);
		  

        $input = $request->all();

		$input['email'] = implode(',', $input['emailfields']);
		
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
		
		if (strip_tags($input['emailsignature']) == '')
			$input['emailsignature'] = null;
		$success = false;
		DB::beginTransaction();
		try {

			$user = User::find($id);
			$user->update($input);
			
			if ($isAdmin) {
				DB::table('model_has_roles')->where('model_id',$id)->delete();
				$user->assignRole($request->input('roles'));
			}
			
			$contactData = $request->input('contacts');
			if (!empty($contactData)) {
				$this->contactfieldmodel = $user;
				$this->updateOrCreateContactFields($request->input('contacts'));
			}
			$success = true;
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route($route)
                        ->with('success_message','User updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','User not updated');
		}					
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success_message','User deleted successfully');
    }
	

	
	private function createUsername($input)
	{
		$firstname = preg_replace('/[^a-zA-Z]/', '', normalizer_normalize($input['firstname']));
		$lastname = substr(normalizer_normalize($input['lastname']), 0, 1); 
		$newusername = strtolower($firstname.$lastname);
		$currentusernum = 0;
		$u = User::where('username', 'ilike', $newusername.'%')->orderBy('username', 'DESC')->first();
		if ($u)
			$currentusernum = (int)substr($u->username, strlen($newusername));

		return sprintf('%s%03d', $newusername, $currentusernum+1);
	}
	
}