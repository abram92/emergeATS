<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

use App\EventType;
use App\User;
use App\Team;
use App\CandidateStatus;
use App\EeStatus;
use App\Gender;
use App\CandidateRating;
use App\CandidateLevel;
use App\CandidateAvailability;
use App\SalaryCategory;
use App\Location;
use App\JobTitle;

use App\ClientStatus;

use App\JobStatus;

use App\JobApplicationStatus;

use App\ContactFieldType;
use App\ContactField;

trait LookupListTrait
{
	
	
	protected function getAlertTypes() 
	{
		return collect([ 'J_'.\Config::get('constants.static_status.job_hot_lead') => 'Hot Leads', 
		                 'J_'.\Config::get('constants.static_status.job_active') => 'Active Jobs', 
						 'C_'.\Config::get('constants.static_status.candidate_active')=>'Active Candidates', 
						 'C_'.\Config::get('constants.static_status.candidate_inprocess')=>'In Process Candidate']);
//		return collect([ 'J_126' => 'Hot Leads', 'J_2' => 'Active Jobs', 'C_2'=>'Active Candidates', 'C_127'=>'In Process Candidate']);
	}

	protected function getAlertLevels() 
	{
		return collect([ 1 => 'Level 1', 2 => 'Level 2', 3=>'Level 3']);
	}
	
	protected function getCandidateStatuses() 
	{
		return CandidateStatus::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id');
//		return CandidateStatus::all()->sortBy('sort_seq')->map->only('colour_hex', 'description','id', 'sort_seq');		
//		return CandidateStatus::orderBy('sort_seq', 'ASC')->pluck('description','id');
	}

	protected function getClientStatuses() 
	{
		return ClientStatus::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id');  //->pluck('description','id');
	}

	protected function getJobStatuses() 
	{
		return JobStatus::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}

	protected function getJobApplicationStatuses($excludeUnlink=false) 
	{
		if ($excludeUnlink)
		return JobApplicationStatus::whereNotIn('system_code', ['UNLINK'])->orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');	
		return JobApplicationStatus::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}

	protected function getConsultants() 
	{
		return User::role('Consultant')->get()->pluck('fullname_username', 'id');
	}

	protected function getTeamLeaders() 
	{
		return User::role('Team Lead')->get()->pluck('fullname_username', 'id');
	}
	
	protected function getTeamMembers($team_id = null, $teamleader_id = null) 
	{
		$query = Team::with('members')
				->when($team_id, function($q1) use ($team_id) {
						return $q1->where('id', $team_id);
					})
				->when($teamleader_id, function($q1) use ($teamleader_id) {
						return $q1->whereHas("leaders", function ($q2) use ($teamleader_id) {
									$q2->where('user_id', $teamleader_id); 
						});				})->get();						
	if (empty($query))
		return [];
	$allconsultants = collect([]);
	foreach ($query as $team) {
		$allconsultants = $allconsultants->merge($team->members);
	}
	return $allconsultants->sortBy('lastname')->pluck('fullname_username', 'id');
	}
	
	protected function getUserInitials($ids) 
	{
		return User::whereIn('id', $ids)->get()->pluck('initials', 'id');
	}	
	
	protected function getEeStatuses()
	{
		return EeStatus::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}
	
	protected function getCandidateRatings()
	{
		return CandidateRating::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}
	
	protected function getCandidateLevels()
	{
		return CandidateLevel::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}

	protected function getGenders()
	{
		return Gender::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id', 'sort_seq'); //->pluck('description','id');
	}
	
	protected function getCandidateAvailabilities()
	{
		return CandidateAvailability::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id', 'sort_seq'); //->pluck('description','id');
	}
	
	protected function getSalaryCategories()
	{
		return SalaryCategory::orderBy('sort_seq', 'ASC')->get()->map->only('colour_hex', 'description','id'); //->pluck('description','id');
	}
	
	protected function getLocations()
	{
		return Location::orderBy('description', 'ASC')->pluck('description','id');
	}
	
	protected function getJobTitles()
	{
		return JobTitle::orderBy('description', 'ASC')->pluck('description','id');
	}
	
	protected function getContactFieldTypes()
	{
		return ContactFieldType::all();
	}
	
	protected function getContactFieldTypeIcons()
	{
		return ContactFieldType::pluck('fontawesome_icon', 'id')->all();
	}

	protected function getDraggableEventTypes()
	{
//		return EventType::pluck('description', 'id')->all();
		return EventType::orderBy('sort_seq', 'ASC')->orderBy('description', 'ASC')->get()->map->only('colour_hex', 'description','id');
	}
	
	protected function getEmailSenderProperties($user)
	{
		$from_name = $user->listname;
		$from_address = $user->email;

		return [['email'=>$from_address, 'name'=>$from_name], html_entity_decode($user->emailsignature)];
	}
	
}
