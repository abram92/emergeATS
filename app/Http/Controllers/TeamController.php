<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Team;

use App\Http\Traits\LookupListTrait;

class TeamController extends Controller
{
	use LookupListTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		$queryFilter = [];
		$q = $request->get('q');

		$srchid = $request->get('srch');
		if ($srchid) {
			$q = $this->decryptFilter($srchid);
		}
		
		$allconsultants = $this->getConsultants(); 
		
        $result = Team::with('leaders')->with('members');

		if (isset($q['team']) && ($q['team'])) {
			 $result->where('description', 'ILIKE', '%'.$q['description'].'%');
			 $queryFilter['Desciption'] = $q['team'];
		}

		if (isset($q['leaders']) && (!empty($q['leaders']))) {
			$r = $q['leaders'];
			$queryFilter['Leaders'] = $allconsultants->only($r)->implode(', ');						
			$result->WhereHas("leaders", function ($q) use ($r) {
					$q->whereIn('user_id', $r); //$q['roles']);
    });
		}

		if (isset($q['members']) && (!empty($q['members']))) {
			$r = $q['members'];
			$queryFilter['Members'] = $allconsultants->only($r)->implode(', ');						
			$result->WhereHas("members", function ($q) use ($r) {
					$q->whereIn('user_id', $r); //$q['roles']);
    });
		}
        $data = $result->orderBy('description','ASC')->paginate(25);
//		$query = $q ? ['q'=>$q] : [];

		$query = $q ? ['srch'=>$this->encryptFilter($q)] : [];

        return view('admin.teams.index',compact('data', 'allconsultants', 'queryFilter', 'q'))
            ->withQuery($query); //('i', ($request->input('page', 1) - 1) * 25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		$teamleaders = $this->getTeamLeaders(); 
		$consultants = $this->getConsultants(); 
		
        return view('admin.teams.create',compact('consultants', 'teamleaders'));
		
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
        $this->validate($request, [
            'description' => 'required|iunique:teams,description',
			'colour_hex' => 'nullable|regex:/^#([A-Fa-f0-9]{6})$/',
			'leaders' => 'required|exists:users,id',
			'members' => 'required|exists:users,id'
        ]);


        $input = $request->all();
		
		$success = false;
		DB::beginTransaction();
		try {

			$team = Team::create($input);
			if ($team) {
				$leaderData = $request->input('leaders');
				$team->leaders()->sync($leaderData);

				$memberData = $request->input('members');
				$team->members()->sync($memberData);
		
				$success = true;
			}
		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.teams.index')
                        ->with('success_message','Team created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Team not created');
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
        $team = Team::with('leaders')->with('members')->find($id);
		
        return view('admin.teams.show',compact('team'));
		
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
		$consultants = $this->getConsultants();
		$teamleaders = $this->getTeamLeaders(); 
        $team = Team::find($id);
		$leaders = $team->leaders()->pluck('user_id', 'user_id');
		$members = $team->members()->pluck('user_id', 'user_id');

        return view('admin.teams.edit',compact('team', 'consultants', 'teamleaders', 'leaders', 'members'));
		
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
        $this->validate($request, [
            'description' => 'required|iunique:teams,description,'.$id,
			'colour_hex' => 'nullable|regex:/^#([A-Fa-f0-9]{6})$/',
			'leaders' => 'required|exists:users,id',
			'members' => 'required|exists:users,id'
        ]);

        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$team = Team::find($id);
			
			
			if ($team->update($input)) {
				$leaderData = $request->input('leaders');
				$team->leaders()->sync($leaderData);

				$memberData = $request->input('members');
				$team->members()->sync($memberData);
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.teams.index')
                        ->with('success_message','Team updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Team not updated');
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
        Team::find($id)->delete();
        return redirect()->route('admin.teams.index')
                        ->with('success_message','Team deleted successfully');
		
    }
	
	
	
}
