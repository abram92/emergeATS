<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ContactFieldType;

use App\ClientContact;
use App\Client;

use DB;

use App\Http\Traits\ContactFieldTrait;
use App\Http\Traits\LongFullTextTrait;

//use Hash;
//use Arr;

class ClientContactController extends Controller  //UserController
{
	use ContactFieldTrait;
	use LongFullTexttrait;
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($clientid)
    {
        //
//		parent::create();
		$contact_types = ContactFieldType::all();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
		$client = Client::findOrFail($clientid);  
        return view('clientcontacts.create',compact('client', 'contact_types'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
//		parent::store($request);

		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
 
		$this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
			'client_id' => 'required|exists:clients,id',			
            'position' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'contacts.*.data' => 'required'
        ]);

        $input = $request->all();
		
		$success = false;
		DB::beginTransaction();
		try {
		
			$clientcontact = ClientContact::create($input);
			if ($clientcontact) {
				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $clientcontact;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}

				$longtext = [];
				if ($request->input('comments') !== null){
					$longtext['comments'] = ['relation'=>'comments', 'value'=>$request->input('comments')];
				}
				if (!empty($longtext)) {
					$this->longtextfullmodel = $clientcontact;
					$this->updateOrCreateLongFullTextFields($longtext);
				}
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.show', $request->input('client_id'))
                        ->with('success_message','Client Contact created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Client Contact not created');
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
        //
        $clientcontact = ClientContact::with('client')->find($id);
        $icons = ContactFieldType::pluck('fontawesome_icon', 'id')->all();
		$contacts = $clientcontact->contactfields()->get()->sortBy('contact_field_type_id')->sortBy('contact_field_type_id');
		
		
        return view('clientcontacts.show',compact('clientcontact', 'contacts'));
		
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $clientcontact = ClientContact::with('client')->find($id);
		$client = $clientcontact->client;
		$contact_types = ContactFieldType::all();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
		$contacts = $clientcontact->contactfields()->get()->sortBy('contact_field_type_id')->sortBy('contact_field_type_id');
		$comments = $clientcontact->comments();

        return view('clientcontacts.edit',compact('clientcontact', 'client', 'contact_types', 'contacts', 'comments'));
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
        //
		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
 
		$this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
			'client_id' => 'required|exists:clients,id',			
            'position' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'contacts.*.data' => 'required'
        ]);

		$input = $request->input();
		$success = false;
		DB::beginTransaction();
		try {
			$clientcontact = ClientContact::find($id);
		
			if ($clientcontact->update($input)){

				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $clientcontact;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}
		
				$longtext = [];
				if ($request->input('comments') !== null){
					$longtext['comments'] = ['relation'=>'comments', 'value'=>$request->input('comments')];
				}
				if (!empty($longtext)) {
					$this->longtextfullmodel = $clientcontact;
					$this->updateOrCreateLongFullTextFields($longtext);
				}
				$success = true;
			}
		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.show', $request->input('client_id'))
                        ->with('success_message','Client Contact updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Client Contact not updated');
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
        //
		parent::destroy($id);
    }
}
