<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use DB;
use App\JobApplication;
use App\JobApplicationStatus;
use App\JobAd;
use App\Candidate;
use App\User;
use App\Client;

use App\CalendarEvent;
use App\StaticWorkAlert;

use Carbon\Carbon;

use App\Http\Traits\LookupListTrait;
use App\Http\Traits\SearchTrait;

use \Illuminate\Database\Eloquent\Relations\MorphTo;

class ReportController extends ReportOutputController {

	use LookupListTrait, SearchTrait;

	public function __construct()
	{
		Carbon::setToStringFormat('d/m/y');
	}

//////
//////    USERS
//////
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function useractivity(Request $request)
    {
        //
		$queryFilter = [];
		
		$allconsultants = $this->getConsultants();

		$q = $request->get('q');

		$q_upload_from = $request->get('q_upload_from');
		$q_upload_to = $request->get('q_upload_to');
		
		if($q_upload_from)
			$q['upload_from'] = $q_upload_from;
		if($q_upload_to)
			$q['upload_to'] = $q_upload_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				


		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {
		
				
        $result = User::withCount(['candidatesEdited' => function ($query) use ($q_upload_from, $q_upload_to) {
											$query->select(DB::raw('count(distinct(auditable_id))'));
											if (isset($q_upload_from)) {
												$query->where('created_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('created_at', '<=', $q_upload_to); 
											}
//											$query->get();											
											
										}, 
									'candidatesLoaded' => function ($query) use ($q_upload_from, $q_upload_to) {
										$query->select(DB::raw('count(distinct(auditable_id))'));
										if (isset($q_upload_from)) {
												$query->where('created_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('created_at', '<=', $q_upload_to); 
											}									
//											$query->get();
																					}, 
									'candidatesActive' => function ($query) use ($q_upload_from, $q_upload_to) {
										$query->select(DB::raw('count(distinct(auditable_id))'));
										if (isset($q_upload_from)) {
												$query->where('created_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('created_at', '<=', $q_upload_to); 
											}				
//											$query->get();
																					}, 
									'cvSent' => function ($query) use ($q_upload_from, $q_upload_to) {
											if (isset($q_upload_from)) {
												$query->where('created_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('created_at', '<=', $q_upload_to); 
											}				
//											$query->get();
																					}, 										
									'jobsActive' => function ($query) use ($q_upload_from, $q_upload_to) {
										$query->select(DB::raw('count(distinct(auditable_id))'));
										if (isset($q_upload_from)) {
												$query->where('updated_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('updated_at', '<=', $q_upload_to); 
											}
//											$query->get();
																					}, 										
									'jobsTotal' => function ($query) use ($q_upload_from, $q_upload_to) {
										$query->select(DB::raw('count(distinct(auditable_id))'));
										if (isset($q_upload_from)) {
												$query->where('updated_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('updated_at', '<=', $q_upload_to); 
											}				
//											$query->get();
																					}, 	
									'clientsLoaded' => function ($query) use ($q_upload_from, $q_upload_to) {
										$query->select(DB::raw('count(distinct(auditable_id))'));
										if (isset($q_upload_from)) {
												$query->where('updated_at', '>=', $q_upload_from); 
											}
											if (isset($q_upload_to)) {
												$query->where('updated_at', '<=', $q_upload_to); 
											}				
//											$query->get();
										}, 																				
								]);

		if (isset($q['jobref']) && ($q['jobref'])) {
			 $result->where('jobref', 'ILIKE', '%'.$q['jobref'].'%');
				$queryFilter['Job Ref'] = $q['jobref'];			 
		}

		if (isset($q['consultants']) && (!empty($q['consultants']))) {
			$result->whereIn('id', $q['consultants']); 
				$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');			
		}
		
		
		if (isset($q_upload_from)) {
				$queryFilter['From'] = $q_upload_from;			
		}
		if (isset($q_upload_to)) {
				$queryFilter['To'] = $q_upload_to;			
		}
		// Limit to consultants
		$result->whereHas('roles', function ($query) {
			return $query->where('name', 'Consultant');
		});
        $data = $result->get();

		}
		if ($isExport) {
			$fileName = 'user_activity'.date('Ymd-hi').'.csv';
			$output = [];
			$output['filter'] = $queryFilter;
			$output['columns'] = ['Consultant', 'Candidates Edited', 'Candidates Loaded', 'Candidates Active', 'CVs Sent', 'Jobs Active', 'Total Jobs', 'Clients Loaded'];
			foreach ($data as $user) {
				$output['results'][] = [$user->fullname_username,
										$user->candidates_edited_count,
										$user->candidates_loaded_count,
										$user->candidates_active_count,
										$user->cv_sent_count,
										$user->jobs_active_count,
										$user->jobs_total_count,
										$user->clients_loaded_count];
			}
			return $this->exportCsv($fileName, $output);
		}
        return view('reports.useractivityfilter',compact('data', 'allconsultants', 'q', 'queryFilter'));
 //           ->with('i', ($request->input('page', 1) - 1) * 25);
		
    }
	
	
    public function cvsent(Request $request)
	{
		
		$queryFilter = [];

		$allconsultants = $this->getConsultants();

		$q = $request->get('q');

		$q_sent_from = $request->get('q_sent_from');
		$q_sent_to = $request->get('q_sent_to');
		
		if($q_sent_from)
			$q['sent_from'] = $q_sent_from;
		if($q_sent_to)
			$q['sent_to'] = $q_sent_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				



		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {

		$consultants = [];
		$candidates = [];
		$clients = [];

		if (isset($q['consultants']) && (!empty($q['consultants']))) {		
			$consultants = $q['consultants'];
			$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(PHP_EOL);			
		}
		if (isset($q['candidatenames']) && (!empty($q['candidatenames']))) {		
			$candidates = explode("\r\n", $q['candidatenames']);
			$queryFilter['Candidates'] = $candidates;			
		}
		if (isset($q['clientnames']) && (!empty($q['clientnames']))) {				
			$clients = explode("\r\n", $q['clientnames']);
			$queryFilter['Clients'] = $clients;			
		}		
		
        $result = CalendarEvent::where('type_id', 7)->with('owner')->with('clients')->with('candidates')->with('clientcontacts')->with('jobs');
//											$query->get();

		if (isset($q_sent_from)) {
			$result->where('created_at', '>=', $q_sent_from); 
			$queryFilter['From'] = $q_sent_from;			
		}
		
		if (isset($q_sent_to)) {
			$result->where('created_at', '<=', $q_sent_to); 
			$queryFilter['To'] = $q_sent_to;			
		}	

//        $data = $result->orderBy('activated_at','DESC')->paginate(25);

		$result->whereHas("owner", function ($query) use ($consultants) {
			if (isset($consultants) && (!empty($consultants)))
				$query->whereIn('id', $consultants); 

			// Limit to consultants
			$query->whereHas('roles', function ($query1) {
				return $query1->where('name', 'Consultant');
			});			
		});
							
		if (isset($clients) && (!empty($clients))) {
			$result->where(function ($query) use ($clients) {
				$query->whereHas("clients", function ($q) use ($clients) {

					$q->where(function ($q1) use ($clients) {
					// Everything within this closure will be grouped together
						$q1->where('name', 'ilike',  '%'.$clients[0].'%');
						foreach ($clients as $k => $v) {
							if ($k > 0)
								$q1->orWhere('name', 'ilike', '%'.$v.'%');
						}
					});
				});													
				$query->orWhereHas("jobs.client", function ($q) use ($clients) {

					$q->where(function ($q1) use ($clients) {
					// Everything within this closure will be grouped together
						$q1->where('name', 'ilike',  '%'.$clients[0].'%');
						foreach ($clients as $k => $v) {
							if ($k > 0)
								$q1->orWhere('name', 'ilike', '%'.$v.'%');
						}
					});
				});	
			});								
		}		

		if (isset($candidates) && (!empty($candidates))) {

			$result->whereHas("candidates.user", function ($q2) use ($candidates) {
			//										$q1->whereHas(['user' => function ($q2) use ($candidates) {
													
				$q2->where(function ($q3) use ($candidates) {
				// Everything within this closure will be grouped together
					$q3->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $candidates[0]).'%');
					foreach ($candidates as $k => $v) {
						if ($k > 0)
							$q3->orWhere(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $v).'%');
					}
				});
						//							}]);
			});
		}							
							
		$data = $result->orderBy('created_at','ASC')->get();

/*		$data = collect([]);
		DB::query()->fromSub($result, 'alias')->orderBy('created_at','ASC')->chunk(1000, function ($chunk) use (&$data) {
			
			dd($chunk);
			$data = $data->concat($chunk);
    // Do something
});  */

		if ($isExport) {
			$fileName = 'cv_sent'.date('Ymd-hi').'.csv';
			$output = [];
			$output['filter'] = $queryFilter;
			$output['columns'] = ['Create Date', 'Consultant', 'Candidate', 'Client', 'Contact'];
			foreach ($data as $email) {
				$candidates = $email->candidates->pluck('user')->pluck('listname')->implode(PHP_EOL);
				if(!$email->clients->isEmpty()) 
					$clients = $email->clients->pluck('name')->implode(PHP_EOL);
				else
					$clients = $email->jobs->pluck('client')->pluck('name')->implode(PHP_EOL);

				$contacts = $email->clientcontacts->pluck('listname')->implode(PHP_EOL);
				
				$output['results'][] = [\Carbon\Carbon::parse($email->created_at)->format('Y-m-d H:i'),
										$email->owner->listname,
										$candidates,
										$clients,
										$contacts];
			}
			return $this->exportCsv($fileName, $output);
		}
		
		}

        return view('reports.cvsentfilter',compact('data', 'allconsultants', 'q', 'queryFilter'));
		
	}
	

    public function staticalerts(Request $request)
	{

		$queryFilter = [];

		$allconsultants = $this->getConsultants();

		$allalerttypes = $this->getAlertTypes();
		$allalertlevels = $this->getAlertLevels();

		$q = $request->get('q');

		$q_sent_from = $request->get('q_sent_from');
		$q_sent_to = $request->get('q_sent_to');
		
		if($q_sent_from)
			$q['sent_from'] = $q_sent_from;
		if($q_sent_to)
			$q['sent_to'] = $q_sent_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				

				
		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {	

			$consultants = [];
			$alertlevels = [];
			$alerttypes = [];

			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$consultants = $q['consultants'];
				$queryFilter['Consultants'] = $allconsultants->only($consultants)->implode(', ');			
			}
			if (isset($q['alertlevels']) && (!empty($q['alertlevels']))) {		
				$alertlevels = $q['alertlevels'];
				$queryFilter['Alert Levels'] = $allalertlevels->only($alertlevels)->implode(', ');			
			}
			if (isset($q['alerttypes']) && (!empty($q['alerttypes']))) {				
				$alerttypes = $q['alerttypes'];
				$queryFilter['Alert Types'] = $allalerttypes->only($alerttypes)->implode(', ');			
			}		
		

			$candidatestatuses = $jobstatuses = [];
			foreach($alerttypes as $v) {
				$s = explode('_', $v);
				switch($s[0]) {
					case 'J' : $jobstatuses[] = $s[1];
						break;
					case 'C' : $candidatestatuses[] = $s[1];
						break;
					default : ;
				}						
			}
//											$query->get();
			$result = StaticWorkAlert::with(['consultant'])->with(['candidates' => function ($q2) use ($candidatestatuses, $jobstatuses, $alertlevels) {
											
					if (!empty($alertlevels))									
						$q2->whereIn('alert_level', $alertlevels); 

					if (!empty($candidatestatuses))													
						$q2->whereIn('static_work_alert_candidates.status_id', $candidatestatuses); 						
					elseif (!empty($jobstatuses))						
					    $q2->whereIn('static_work_alert_candidates.status_id', [-1]); 						
				}])->with(['jobs' => function ($q2) use ($candidatestatuses, $jobstatuses, $alertlevels) {
											
					if (!empty($alertlevels))
						$q2->whereIn('alert_level', $alertlevels); 
					if (!empty($jobstatuses))																	
						$q2->whereIn('static_work_alert_job_ads.status_id', $jobstatuses); 
					elseif (!empty($candidatestatuses))						
					    $q2->whereIn('static_work_alert_job_ads.status_id', [-1]); 											
																					
				}]);

			if (isset($q_sent_from)) {
				$result->where('created_at', '>=', $q_sent_from); 
				$queryFilter['From'] = $q_sent_from;			
			}
			
			if (isset($q_sent_to)) {
				$result->where('created_at', '<=', $q_sent_to); 
				$queryFilter['To'] = $q_sent_from;			
			}	

//        $data = $result->orderBy('activated_at','DESC')->paginate(25);

			if (isset($consultants) && (!empty($consultants))) {
				$result->whereIn('user_id', $consultants); 
			}
							

			if ((isset($alerttypes) && (!empty($alerttypes))) || (isset($alertlevels) && (!empty($alertlevels)))) {

				$result->where(function ($query) use ($jobstatuses, $candidatestatuses, $alertlevels) {				
						$query->whereHas("candidates", function ($q2) use ($candidatestatuses, $alertlevels) {
								if (!empty($alertlevels))									
									$q2->whereIn('alert_level', $alertlevels); 
								if (!empty($candidatestatuses))													
									$q2->whereIn('static_work_alert_candidates.status_id', $candidatestatuses); 
						});
						
						$query->orWhereHas("jobs", function ($q2) use ($jobstatuses, $alertlevels) {
							if (!empty($alertlevels))
								$q2->whereIn('alert_level', $alertlevels); 
							
							if (!empty($jobstatuses))																	
								$q2->whereIn('static_work_alert_job_ads.status_id', $jobstatuses); 
						});
													
				});							
			}
			
			$data = $result->orderBy('created_at','ASC')->get();
		
			if ($isExport) {
				$fileName = 'static_alerts'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ['Timestamp', 'Consultant', 'Type', 'Level', 'Reference', 'Status'];
			
			
				foreach ($data as $key => $alert) {
					foreach ($alert->jobs as $key1 => $jobalert) {

						$output['results'][] = [\Carbon\Carbon::parse($alert->created_at)->format('Y-m-d H:i'),
												$alert->consultant->listname,
												'Job',
												$jobalert->pivot->alert_level,
												$jobalert->jobref,
												$jobalert->pivot->status->description];
					}
					foreach ($alert->candidates as $key2 => $candalert) {

						$output['results'][] = [\Carbon\Carbon::parse($alert->created_at)->format('Y-m-d H:i'),
												$alert->consultant->listname,
												'Candidate',
												$candalert->pivot->alert_level,
												$candalert->user->listname,
												$candalert->pivot->status->description];
					}
				}				
	
				return $this->exportCsv($fileName, $output);
			}		
		}
        return view('reports.staticalertfilter',compact('data', 'allconsultants', 'allalerttypes', 'allalertlevels', 'q', 'queryFilter'));
		
	}

//////
////// CANDIDATES
//////


    public function candidates(Request $request) 
	{
		$queryFilter = [];

		$allconsultants = $this->getConsultants();
		$allstatuses = $this->getCandidateStatuses();

		$q = $request->get('q');

		$q_upload_from = $request->get('q_upload_from');
		$q_upload_to = $request->get('q_upload_to');
		
		if($q_upload_from)
			$q['upload_from'] = $q_upload_from;
		if($q_upload_to)
			$q['upload_to'] = $q_upload_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				
				
		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {	
			$result = Candidate::has('user')->with('user')->with('emailaddresses')->with('preferredlocations')->with('contactfields')->with('jobtitle')->with('consultant');

			if (isset($q['name']) && (!empty($q['name']))) {
				$r = $q['name'];
				$result->WhereHas("user", function ($q) use ($r) {
					$q->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%');
				});
				$queryFilter['Candidate Name'] = $r;
			}
			

			if (isset($q['consultants']) && (!empty($q['consultants']))) {
				$result->whereIn('consultant_id', $q['consultants']); 
					$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');			
			}

			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
					$queryFilter['Statuses'] = $allstatuses->only($q['statuses'])->implode(', ');			
			}		
		
			if (isset($q_upload_from)) {
				$result->where('activated_at', '>=', $q_upload_from); 
				$queryFilter['Upload From'] = $q_upload_from;			
			}
			
			if (isset($q_sent_to)) {
				$result->where('activated_at', '<=', $q_sent_to); 
				$queryFilter['Upload To'] = $q_sent_from;			
			}	

			$data = $result->orderBy('activated_at','DESC')->get();
		
			if ($isExport) {
				$fileName = 'candidates'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ["No", "VIS", "Name", "Surname", "Current Job Title", "Company", "Email Address", "Location", "Linkedin Profile", "Contact Details", "Upload Date"];
			
			
				foreach ($data as $key => $rec) {

					$jobtitle = preg_match("/[a-z]/i",$rec->jobtitle_text) ? $rec->jobtitle_text : (isset($rec->jobtitle) ? $rec->jobtitle->description : '');
				if(!$rec->emailaddresses->isEmpty()) 
					$addresses = $rec->emailaddresses->pluck('data')->implode(PHP_EOL);
				else
					$addresses = "";

				if(!$rec->preferredlocations->isEmpty()) 
					$locations = $rec->preferredlocations->pluck('description')->implode(PHP_EOL);
				else
					$locations = "";
				
				if(!$rec->contactfields->isEmpty()) {
					$linkedin = $rec->contactfields->filter(function ($value, $key) {
										return $value->contact_field_type_id == 4;
								})->pluck('data')->implode(PHP_EOL);
					$contactphones = $rec->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [2,3]);
								})->pluck('data')->unique()->implode(PHP_EOL);			
				}
				else {
					$linkedin = "";
					$contactphones = "";				
				}
				
					$output['results'][] = [$rec->id,
										'Yes',
										$rec->user->firstname,
										$rec->user->lastname,
										$jobtitle,
										"",
										$addresses,
										$locations,
										$linkedin,
										$contactphones,
										\Carbon\Carbon::parse($rec->activated_at)->format('Y-m-d H:i')];
				}				
	
				return $this->exportCsv($fileName, $output);
			}				
		}
        return view('reports.candidatefilter',compact('data', 'allconsultants', 'allstatuses', 'q', 'queryFilter'));
	}
	
	
	
    public function candidatehistory(Request $request)
	{
		$queryFilter = [];

		$allconsultants = $this->getConsultants();
		$allstatuses = $this->getCandidateStatuses();

		$q = $request->get('q');

		$q_changed_from = $request->get('q_changed_from');
		$q_changed_to = $request->get('q_changed_to');
		
		if($q_changed_from)
			$q['changed_from'] = $q_changed_from;
		if($q_changed_to)
			$q['changed_to'] = $q_changed_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				

		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {	

			$consultants = [];

				
	
			$result = Candidate::has('user')->with('status')->with('user')->with(['audits' => function ($q1) use ($q) {
		
//					$q2->where('audits.created_at', '=', 'job_applications.created_at'); 
			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$q1->whereIn('user_id', $q['consultants']); 
			}
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$q1->whereJsonContains('new_values->status_id', $q['statuses']); 
			}

			if (isset($q['changed_from']) && ($q['changed_from'])) {
				$q1->where('created_at', '>=', $q['changed_from']); 
			}

			if (isset($q['changed_to']) && ($q['changed_to'])) {
				$q1->where('created_at', '<=', $q['changed_to']); 
			}
							
				
//					$q2->whereJsonContains('new_values->status_id', 'job_applications.status_id');
	//		dd($q1->toSql());		
				}]);

			if (isset($q['name']) && (!empty($q['name']))) {
				$r = $q['name'];
				$result->WhereHas("user", function ($q) use ($r) {
					$q->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%');
				});
				$queryFilter['Candidate Name'] = $r;
			}		

			$consultants = $statuses = $changed_from = $changed_to = null;
			
			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$consultants = $q['consultants'];
				$queryFilter['Consultants'] = $allconsultants->only($consultants)->implode(', ');			
							
			}
			
			
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$statuses = $q['statuses'];
				$queryFilter['Statuses'] = $allstatuses->only($statuses)->implode(', ');			
			}
			
			if (isset($q['changed_from']) && ($q['changed_from'])) {
				    $changed_from = $q['changed_from'];
					$queryFilter['Status Changed Date >='] = $changed_from;				
			}

			if (isset($q['changed_to']) && ($q['changed_to'])) {
					$changed_to = $q['changed_to'];
					$queryFilter['Status Changed Date <='] = $changed_to;
			}
			
			$result->WhereHas('audits', function ($query) use ($consultants, $statuses, $changed_from, $changed_to) {
            // Everything within this closure will be grouped together
				if (!is_null($consultants))
					$query->whereIn('user_id', $consultants); 
				if (!is_null($statuses))
					$query->whereJsonContains('new_values->status_id', $statuses); 
				if (!is_null($changed_from))
					$query->where('created_at', '>=', $changed_from); 
				if (!is_null($changed_to))
					$query->where('created_at', '<=', $changed_to); 
			
					});				
			
			
	
			if (isset($q['clientnames']) && ($q['clientnames'])) {
				$clientArray = $this->convertTextareaToArray($q['clientnames']);
				if (!empty($clientArray)) {
					$result->WhereHas('client', function ($query) use ($clientArray) {
            // Everything within this closure will be grouped together
						foreach ($clientArray as $k => $v) {
							$query->orWhere('name', ' ilike',  '%'.$v.'%');
						}
				
					});		
					$queryFilter['Clients'] = implode(', ', $clientArray);
				}
			}
	
/*				->with(['audits.user' => function ($q2) use ($consultants, $q) {
											
//					$q2->where('audits.created_at', '=', 'job_applications.created_at'); 
					if (!empty($consultants))									
						$q2->whereIn('id', $consultants); 
				
					
//					$q2->whereJsonContains('new_values->status_id', 'job_applications.status_id');
					
				}])  */



// dd($result->toSql());

			$data = $result->orderBy('activated_at','DESC')->get(); //->paginate(25);
			
			if ($isExport) {
				$fileName = 'candidatehistory'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ["No", "VIS", "Name", "Surname", "Current Job Title", "Company", "Email Address", "Location", "Linkedin Profile", "Contact Details", "Upload Date"];
			
			
				foreach ($data as $key => $rec) {

					$jobtitle = preg_match("/[a-z]/i",$rec->jobtitle_text) ? $rec->jobtitle_text : (isset($rec->jobtitle) ? $rec->jobtitle->description : '');
				if(!$rec->emailaddresses->isEmpty()) 
					$addresses = $rec->emailaddresses->pluck('data')->implode(PHP_EOL);
				else
					$addresses = "";

				if(!$rec->preferredlocations->isEmpty()) 
					$locations = $rec->preferredlocations->pluck('description')->implode(PHP_EOL);
				else
					$locations = "";
				
				if(!$rec->contactfields->isEmpty()) {
					$linkedin = $rec->contactfields->filter(function ($value, $key) {
										return $value->contact_field_type_id == 4;
								})->pluck('data')->implode(PHP_EOL);
					$contactphones = $rec->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [2,3]);
								})->pluck('data')->unique()->implode(PHP_EOL);			
				}
				else {
					$linkedin = "";
					$contactphones = "";				
				}
				
					$output['results'][] = [$rec->id,
										'Yes',
										$rec->user->firstname,
										$rec->user->lastname,
										$jobtitle,
										"",
										$addresses,
										$locations,
										$linkedin,
										$contactphones,
										\Carbon\Carbon::parse($rec->activated_at)->format('Y-m-d H:i')];
				}				
	
				return $this->exportCsv($fileName, $output);
			}		
		}
        return view('reports.candidatehistoryfilter',compact('data', 'allconsultants', 'allstatuses', 'q', 'queryFilter'));
		
	}
	

    public function linkedcandidates(Request $request)
    {
		$queryFilter = [];
        //
		$allconsultants = $this->getConsultants();
        $allstatuses = $this->getJobApplicationStatuses(true);

		$q = $request->get('q');

		$q_linked_from = $request->get('q_linked_from');
		$q_linked_to = $request->get('q_linked_to');
		
		if($q_linked_from)
			$q['linked_from'] = $q_linked_from;
		if($q_linked_to)
			$q['linked_to'] = $q_linked_to;
		
		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				

		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = false; //$request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {

//		if (isset($q['consultants']) && (!empty($q['consultants']))) {
//			$result->whereIn('consultant_id', $q['consultants']); 
//		}
		
			$consultants = [];

			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$consultants = $q['consultants'];
				$queryFilter['Consultants'] = $allconsultants->only($consultants)->implode(', ');			
			}		

		$jobref = isset($q['jobref']) ? $q['jobref'] : '';
		if($jobref)
		$queryFilter['Job Ref'] = $jobref;			

			$result = JobApplication::with(['audits.user' => function ($q2) use ($consultants, $q) {
											
//					$q2->where('audits.created_at', '=', 'job_applications.created_at'); 
					if (!empty($consultants))									
						$q2->whereIn('id', $consultants); 
				
					
//					$q2->whereJsonContains('new_values->status_id', 'job_applications.status_id');
					
				}])->whereHasMorph('applicationable', [JobAd::class], function ($query) use ($jobref){
					if ($jobref)
					$query->where('jobref', $jobref);
				})->with(['candidate'])->with('applicationable', function (MorphTo $morphTo) {
					$morphTo->morphWith([JobApplication::class => 'jobad']);
				});
//				->where('applicationable_type', 'App\JobAd')->with(['candidate'])->with('applicationable', function (App\Http\Controllers\MorphTo $morphTo) {
//					$morphTo->morphWith([JobAd::class => ['jobad']]);
//				});

			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
					$queryFilter['Statuses'] = $allstatuses->only($q['statuses'])->implode(', ');			
			}	
			
			if (isset($q['linked_from']) && ($q['linked_from'])) {
				$result->where('created_at', '>=', $q['linked_from']); 
			}
			
			if (isset($q['linked_to']) && ($q['linked_to'])) {
				$result->where('created_at', '<=', $q['linked_to']); 
			}					
		

// $result->where('id', '>', 300000); 
$validExportStates = array('CNDINTRST', 'CNDPRSPCT', 'JOBREJECT', 'CLNTINTRST', 'RECAGNT', 'INTPROC', 'OFFERPROC', 'CNDREJECT', 'CNDACCEPT', 'LINKPROC');		

// dd($result->toSql());
//dd($result->toSql());
			$data = $result->orderBy('created_at','DESC')->get();

			if ($isExport) {
				$fileName = 'linkedcandidates'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ["Job Ref", "Firstname", "Surname", "Email Address", "Job Title", "Date Linked", "Consultant"];
			
			
				foreach ($data as $key => $rec) {

					$jobtitle = preg_match("/[a-z]/i",$rec->jobad->jobtitle_text) ? $rec->jobad->jobtitle_text : (isset($rec->jobad->jobtitle) ? $rec->jobad->jobtitle->description : '');

				if(!$rec->candidate->emailaddresses->isEmpty()) 
					$addresses = $rec->candidate->emailaddresses->pluck('data')->implode(PHP_EOL);
				else
					$addresses = "";

				
					$output['results'][] = [$rec->jobad->jobref,
										$rec->candidate->user->firstname,
										$rec->candidate->user->lastname,
										$addresses,
										$jobtitle,
										\Carbon\Carbon::parse($rec->created_at)->format('Y-m-d H:i'),
										isset($rec->audits[0]->user) ? $rec->audits[0]->user->listname : ''
										];
				}				
//	dd($output);
				return $this->exportCsv($fileName, $output);
			}

		}
//		$query = $searchid ? ['search_id'=>$searchid] : [];

        return view('reports.linkedcandidatesfilter',compact('data', 'allconsultants', 'allstatuses', 'q', 'queryFilter'));
 //           ->with('i', ($request->input('page', 1) - 1) * 25);
		
    }

//////
//////    CLIENTS
//////
	
    public function clients(Request $request)
	{
		$queryFilter = [];

		$allconsultants = $this->getConsultants();
		$allstatuses = $this->getClientStatuses();

		$q = $request->get('q');

		$q_upload_from = $request->get('q_upload_from');
		$q_upload_to = $request->get('q_upload_to');
		
		if($q_upload_from)
			$q['upload_from'] = $q_upload_from;
		if($q_upload_to)
			$q['upload_to'] = $q_upload_to;
		


		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {
			if (is_array($q))
				$q = $this->removeEmptyCriteria($q);				
				
							
			$result = Client::with('status')->with('consultant')->with('addresses')->with('contactfields');
			$withContact = false;
			
			if (isset($q['contact'])) {
				$withContact = true;
				$result->with('staff');
			} else
				$result->with('techenvironment');
			
			if (isset($q['name']) && ($q['name'])) {
				$result->where('name', 'ILIKE', '%'.str_replace(' ', '%', $q['name']).'%');
				$queryFilter['Client Name'] = $q['name'];
			}

			if (isset($q['consultants']) && (!empty($q['consultants']))) {
				$result->whereIn('consultant_id', $q['consultants']); 
				$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');
			}
		
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
				$queryFilter['Statuses'] = $allstatuses->pluck('description', 'id')->only($q['statuses'])->implode(', ');
			}
			if ($withContact) {

				$name = isset($q['contact']['name']) ? $q['contact']['name'] : null;
				$position = isset($q['contact']['position']) ? $q['contact']['position'] : null;
				$phone = isset($q['contact']['phone']) ? $q['contact']['phone'] : null;
				$email = isset($q['contact']['email']) ? $q['contact']['email'] : null;

				if ($name || $position || $phone || $email) {
					
					$result->whereHas('staff', function($contactQuery) use ($name, $position, $phone, $email, &$queryFilter) {
						
						$this->clientContactConditional($contactQuery, $name, $position, $phone, $email, $queryFilter);

					})->with(['staff' => function($contactQuery) use ($name, $position, $phone, $email)	{
						$tmp = [];
						$this->clientContactConditional($contactQuery, $name, $position, $phone, $email, $tmp);
					
					}] );
				}
			}
				
 			$data =	$result->orderBy('name','ASC')->get();


			if ($isExport) {

				$output = [];
				$output['filter'] = $queryFilter;
				if ($withContact) {
				$fileName = 'clientcontacts'.date('Ymd-hi').'.csv';
				 $output['columns'] = ["No", "Company Name", "Contact Name", "Job Title", "Phone", "Cell Number", "Email Address", "City", 
                    "Country", "Client Phone", "Consultant", "Status"];
				} else {
				$fileName = 'clients'.date('Ymd-hi').'.csv';
				$output['columns'] = ["No", "Company Name", "City", "Country", "Phone", "Consultant", "Status", "Tech Environment"];
				}
			
				foreach ($data as $key => $rec) {
					
					$addresses = $rec->addresses;
					$city = [];
					$country = [];
					foreach($addresses as $address) {
						$city[] = $address->city;
						$country[] = $address->country;
					}
					$clientphones = $rec->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [2,3]);
								})->pluck('data')->unique()->implode(PHP_EOL);			
					
					if ($withContact) {
						
						foreach($rec->staff as $staff) {

							$contactfields = $staff->contactfields;
							$phones = $contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [2]);
								})->pluck('data')->unique()->implode(PHP_EOL);	
							$cells = $contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [3]);
								})->pluck('data')->unique()->implode(PHP_EOL);		
							$emails = $contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [1]);
								})->pluck('data')->unique()->implode(PHP_EOL);										

//dd($rec->consultant);
							$consultantname = ($rec->consultant) ? $rec->consultant->listname : "";
							$output['results'][] = [$rec->id,
										$rec->name,
										$staff->listname,
										$staff->position,
										$phones,
										$cells,
										$emails,
										$city,
										$country,
										$clientphones,
										$consultantname,
										isset($rec->status) ? $rec->status->description : ''];	
						}
						
					} else {
								
						$output['results'][] = [$rec->id,
										$rec->name,
										$city,
										$country,
										$clientphones,
										isset($rec->consultant) ? $rec->consultant->listname : '',
										isset($rec->status) ? $rec->status->description : '',
										isset($rec->techenvironment) ? $rec->techenvironment->chunk : ''];	
					}						

				}
	
				return $this->exportCsv($fileName, $output);
			}
 
		}
        return view('reports.clientfilter',compact('data', 'allconsultants', 'allstatuses', 'q', 'queryFilter'));
		
	}

//////
//////  JOBS
//////
	
    public function jobs(Request $request)
	{
		$queryFilter = [];

        $allstatuses = $this->getJobStatuses();
		$allconsultants = $this->getConsultants();

		$alleestatuses = $this->getEeStatuses();
		$allsalarycategories = $this->getSalaryCategories();
		$alllocations = $this->getLocations();

		$q = $request->get('q');

		$q_upload_from = $request->get('q_upload_from');
		$q_upload_to = $request->get('q_upload_to');
		
		if($q_upload_from)
			$q['upload_from'] = $q_upload_from;
		if($q_upload_to)
			$q['upload_to'] = $q_upload_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				

		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {	

			$result = JobAd::with('status')->with('consultant')->with('client')->with('clientcontacts')->with('projectplan')->with('summary')->with('salarycategory')->with('eestatus')->with('locations')->with('documents');


			if (isset($q['clientname']) && (!empty($q['clientname']))) {
				$r = $q['clientname'];
				$queryFilter['Client Name'] = $q['clientname'];
				$result->WhereHas("client", function ($q) use ($r) {
					$q->where('name', 'ILIKE', '%'.str_replace(' ', '%', $r).'%');
				});
			}



			if (isset($q['clientexclude']) && ($q['clientexclude'])) {
				$excludeArray = $this->convertTextareaToArray($q['clientexclude']);
				if (!empty($excludeArray)) {
					$result->WhereHas('client', function ($query) use ($excludeArray) {
            // Everything within this closure will be grouped together
						foreach ($excludeArray as $k => $v) {
							$query->where('name', 'not ilike',  '%'.$v.'%');
						}
				
					});		
					$queryFilter['Clients Excluded'] = implode(', ', $excludeArray);
				}
			}

			if (isset($q['jobref']) && ($q['jobref'])) {
				$result->where('jobref', 'ILIKE', '%'.$q['jobref'].'%');
				$queryFilter['Job Ref'] = $q['jobref'];
			}

			if (isset($q['jobtitles']) && ($q['jobtitles'])) {
				$titlesArray = $this->convertTextareaToArray($q['jobtitles']);
				if (!empty($titlesArray)) {
					$result->where(function ($query) use ($titlesArray) {
            // Everything within this closure will be grouped together
						$query->where(DB::raw("CONCAT(' ',jobtitle_text, ' ')"), 'ilike',  '%[^a-z]'.$titlesArray[0].'[^a-z]%');
						foreach ($titlesArray as $k => $v) {
							if ($k > 0)
								$query->orWhere(DB::raw("CONCAT(' ',jobtitle_text, ' ')"), 'ilike', '%[^a-z]'.$v.'[^a-z]%');
						}
					});	
					$queryFilter['Job Title'] = implode(', ', $titlesArray);
				}
			}

			if (isset($q['consultants']) && (!empty($q['consultants']))) {
				$result->whereIn('consultant_id', $q['consultants']); 
					$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');
			}
		
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
				$queryFilter['Statuses'] = $allstatuses->pluck('description', 'id')->only($q['statuses'])->implode(', ');
			}

			if (isset($q['eestatuses']) && (!empty($q['eestatuses']))) {
				$result->whereIn('ee_status_id', $q['eestatuses']); 
				$queryFilter['EE Statuses'] = $alleestatuses->pluck('description', 'id')->only($q['eestatuses'])->implode(', ');
			}


			if (isset($q['contact']['name']) && (!empty($q['contact']['name']))) {
				$r = $q['contact']['name'];
				$result->WhereHas("staff", function ($q) use ($r) {
					$q->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%');
				});
				$queryFilter['Contact Name'] = $q['contact']['name'];
			}


			if (isset($q['contact']['phone']) && (!empty($q['contact']['phone']))) {
				$r = $q['contact']['phone'];
				$result->WhereHas("staff", function ($q) use ($r) {
					$q->whereHas("contactfields", function ($q1) use ($r) {
						$q1->where('data', 'ILIKE', '%'.$r.'%')->whereIn('contact_field_type_id', [2,3]);
					});
				});
				$queryFilter['Contact Phone'] = $q['contact']['phone'];
			}
		
			if (isset($q['contact']['email']) && (!empty($q['contact']['email']))) {
				$r = $q['contact']['email'];
				$result->WhereHas("staff", function ($q) use ($r) {
					$q->whereHas("contactfields", function ($q1) use ($r) {
						$q1->where('data', 'ILIKE', '%'.$r.'%')->where('contact_field_type_id', 1);
					});
				});
				$queryFilter['Contact Email'] = $q['contact']['email'];
			}
	


			if (isset($q['locations']) && (!empty($q['locations']))) {
				$r = $q['locations'];
				$result->whereHas("locations", function ($q1) use ($r) {
					$q1->whereIn('location_id', $r);
				});					
//				$result->whereIn('location_id', $q['locations']); 
				$queryFilter['Location'] = $alllocations->only($q['locations'])->implode(', ');
			}


			if (isset($q['salarycategories']) && (!empty($q['salarycategories']))) {
				$result->whereIn('salary_category_id', $q['salarycategories']); 
				$queryFilter['Salary Category'] = $allsalarycategories->pluck('description', 'id')->only($q['salarycategories'])->implode(', ');
			}

			if (isset($q['minsalary']) && ($q['minsalary'])) {
				$result->where('salary_from', '>=', $q['minsalary']); 
				$queryFilter['Salary >='] = $q['minsalary'];
			}

			if (isset($q['maxsalary']) && ($q['maxsalary'])) {
				$result->where('salary_from', '<', $q['maxsalary']); 
				$queryFilter['Salary <'] = $q['maxsalary'];
			}
	
			if (isset($q['upload_from']) && ($q['upload_from'])) {
				$result->where('activated_at', '>=', $q['upload_from']); 
				$queryFilter['Activated Date >='] = $q['upload_from'];
			}
			
			if (isset($q['upload_to']) && ($q['upload_to'])) {
				$result->where('activated_at', '<=', $q['upload_to']); 
				$queryFilter['Activated <='] = $q['upload_to'];
			}
	
// long text	
			$fulltextfields = ['agencynotes'=>['var'=>'agencynotes', 'label'=>'Agency Notes'],
							'summary'=>['var'=>'summary', 'label'=>'Summary'],
							'projectplan'=>['var'=>'projectplan', 'label'=>'Project Plan'],
							'skills'=>['var'=>'skills', 'label'=>'Skills'],
							'fulldescription'=>['var'=>'fulldescription', 'label'=>'Description'],
							];
										
			foreach ($fulltextfields as $relation => $html) {
				$htmlvar = $html['var'];
				$htmllabel = $html['label'];
				if (isset($q[$htmlvar]) && (!empty($q[$htmlvar]))) {
					$r = $this->FTS_setLongTextQueryString($q[$htmlvar]);
					$result->WhereHas($relation, function ($q) use ($r) {
//						$q->where(DB::raw("chunk_tokens @@ to_tsquery(".$r.")"), '=', 'true');
						$q->whereRaw($this->FTS_getFullTextClauseSyntax($r));
					});
					$queryFilter[$htmllabel] = $q[$htmlvar];
					
				}
			}
			
			$data = $result->orderBy('created_at','DESC')->get();

			if ($isExport) {
				$fileName = 'jobs'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ["No", "VIS", "Client", "Ref No", "Job Title", "Locations", "Salary Category", "Contact Name", "Contact Surname", "Contact Position", "Contact Telephone", "Contact Mobile", "Contact Email"];
			
			
				foreach ($data as $key => $rec) {

					$jobtitle = preg_match("/[a-z]/i",$rec->jobtitle_text) ? $rec->jobtitle_text : (isset($rec->jobtitle) ? $rec->jobtitle->description : '');

				
				$contacts = [];
				if(!$rec->clientcontacts->isEmpty()) {
					foreach($rec->clientcontacts as $clientcontact) {

					$contactTel = $clientcontact->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [2]);
								})->pluck('data')->unique()->toArray();
					$contactMobile = $clientcontact->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [3]);
								})->pluck('data')->unique()->toArray();
					$contactEmail = $clientcontact->contactfields->filter(function ($value, $key) {
										return in_array($value->contact_field_type_id, [1]);
								})->pluck('data')->unique()->toArray();
								
						$contacts[] = ['name'=>$clientcontact->firstname,
									'surname'=>$clientcontact->lastname,
									'position'=>$clientcontact->position,
									'telephone'=>$contactTel,
									'mobile'=>$contactMobile,
									'email'=>$contactEmail
								]; 
					}
			
				} else {
					$contacts[] = ['name'=>'',
									'surname'=>'',
									'position'=>'',
									'telephone'=>'',
									'mobile'=>'',
									'email'=>''
								]; 
				}
				    foreach($contacts as $contact)
					
						if(!$rec->locations->isEmpty()) 
							$locations = $rec->locations->pluck('description')->implode(PHP_EOL);
						else
							$locations = "";
				
					$output['results'][] = [$rec->id,
										'Yes',
										$rec->client->name,
										$rec->jobref,
										$jobtitle,
										$locations,
										isset($rec->salarycategory) ? $rec->salarycategory->description : '',
										$contact['name'],
										$contact['surname'],
										$contact['position'],
										$contact['telephone'],
										$contact['mobile'],
										$contact['email']];
				}				
	
				return $this->exportCsv($fileName, $output);
			}				
		}
        return view('reports.jobfilter',compact('data', 'allconsultants', 'allstatuses', 'allconsultants', 'alleestatuses', 'alllocations', 'allsalarycategories', 'q', 'queryFilter'));
	}
	
    public function jobhistory(Request $request)
	{
		$queryFilter = [];

		$allconsultants = $this->getConsultants();
		$allstatuses = $this->getJobStatuses();

		$q = $request->get('q');

		$q_changed_from = $request->get('q_changed_from');
		$q_changed_to = $request->get('q_changed_to');
		
		if($q_changed_from)
			$q['changed_from'] = $q_changed_from;
		if($q_changed_to)
			$q['changed_to'] = $q_changed_to;
		

		if (is_array($q))
			$q = $this->removeEmptyCriteria($q);				
		
		$isExport = $request->get('export') && (Auth::user()->hasRole('Data Exporter'));
		$isSearch = $request->get('search');
		
		if (!($isExport || $isSearch)) {
			$data = collect([]);
		} else {

			$consultants = [];

				
	
			$result = JobAd::with(['audits' => function ($q1) use ($q) {
		
//					$q2->where('audits.created_at', '=', 'job_applications.created_at'); 
			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$q1->whereIn('user_id', $q['consultants']); 
			}
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$q1->whereJsonContains('new_values->status_id', $q['statuses']); 
			}

			if (isset($q['changed_from']) && ($q['changed_from'])) {
				$q1->where('created_at', '>=', $q['changed_from']); 
			}

			if (isset($q['changed_to']) && ($q['changed_to'])) {
				$q1->where('created_at', '<=', $q['changed_to']); 
			}
						
				
//					$q2->whereJsonContains('new_values->status_id', 'job_applications.status_id');
	//		dd($q1->toSql());		
				}]);

		$jobref = isset($q['jobref']) ? $q['jobref'] : '';
			if($jobref) {
				$queryFilter['Job Ref'] = $jobref;	
				$result->where('jobref', 'ILIKE', '%'.$jobref.'%');
			}			

			$consultants = $statuses = $changed_from = $changed_to = null;
			
			if (isset($q['consultants']) && (!empty($q['consultants']))) {		
				$consultants = $q['consultants'];
				$queryFilter['Consultants'] = $allconsultants->only($consultants)->implode(', ');			
							
			}
			
			
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$statuses = $q['statuses'];
				$queryFilter['Statuses'] = $allstatuses->only($statuses)->implode(', ');			
			}
			
			if (isset($q['changed_from']) && ($q['changed_from'])) {
				    $changed_from = $q['changed_from'];
					$queryFilter['Status Changed Date >='] = $changed_from;				
			}

			if (isset($q['changed_to']) && ($q['changed_to'])) {
					$changed_to = $q['changed_to'];
					$queryFilter['Status Changed Date <='] = $changed_to;
			}
			
			$result->WhereHas('audits', function ($query) use ($consultants, $statuses, $changed_from, $changed_to) {
            // Everything within this closure will be grouped together
				if (!is_null($consultants))
					$query->whereIn('user_id', $consultants); 
				if (!is_null($statuses))
					$query->whereJsonContains('new_values->status_id', $statuses); 
				if (!is_null($changed_from))
					$query->where('created_at', '>=', $changed_from); 
				if (!is_null($changed_to))
					$query->where('created_at', '<=', $changed_to); 
			
					});				
			
			
	
			if (isset($q['clientnames']) && ($q['clientnames'])) {
				$clientArray = $this->convertTextareaToArray($q['clientnames']);
				if (!empty($clientArray)) {
					$result->WhereHas('client', function ($query) use ($clientArray) {
            // Everything within this closure will be grouped together
						foreach ($clientArray as $k => $v) {
							$query->orWhere('name', ' ilike',  '%'.$v.'%');
						}
				
					});		
					$queryFilter['Clients'] = implode(', ', $clientArray);
				}
			}
	
/*				->with(['audits.user' => function ($q2) use ($consultants, $q) {
											
//					$q2->where('audits.created_at', '=', 'job_applications.created_at'); 
					if (!empty($consultants))									
						$q2->whereIn('id', $consultants); 
				
					
//					$q2->whereJsonContains('new_values->status_id', 'job_applications.status_id');
					
				}])  */



// dd($result->toSql());

			$data = $result->orderBy('activated_at','DESC')->get(); //->paginate(25);
			
			if ($isExport) {
				$fileName = 'jobhistory'.date('Ymd-hi').'.csv';
				$output = [];
				$output['filter'] = $queryFilter;
				$output['columns'] = ["No", "Ref No", "Date Activated", "Salary", "Status", "Action By"];
			
			
				foreach ($data as $key => $rec) {

				
					$output['results'][] = [$rec->id,
										$rec->refno,
										\Carbon\Carbon::parse($rec->activated_at)->format('Y-m-d H:i'),
										$rec->salary_from,
										isset($rec->status) ? $rec->status->description : '',
										isset($rec->audit[0]->user) ? $rec->audit[0]->user->fullname_username : ''
										];
				}				
	
				return $this->exportCsv($fileName, $output);
			}
			
		}

        return view('reports.jobhistoryfilter',compact('data', 'allconsultants', 'allstatuses', 'q', 'queryFilter'));
		
	}

//////

	protected function getDateRange()
	{
		$from = Input::has('from') ? new Carbon(Input::get('from')) : null;
		$to = Input::has('to') ? new Carbon(Input::get('to')) : null;
		return [$from, $to];
	}


	protected function exportCsv(String $fileName, Array $data)
	{

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = $data['columns']; 

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
			
			if (isset($data['filter']) && !empty($data['filter'])) {
				fputcsv($file, ['Filter Criteria']);
				foreach($data['filter'] as $k => $v) {
						fputcsv($file, [$k, '', (is_array($v) ? implode(PHP_EOL, $v) : $v)]);
				}
				fputcsv($file, []);
			}

//            fputcsv($file, $columns);
            fputcsv($file, $data['columns']);
            foreach ($data['results'] as $record) {
				$row = [];
				foreach($columns as $colKey=>$colVal) {
                     $row[] =  (is_array($record[$colKey]) ? implode(PHP_EOL, $record[$colKey]) : $record[$colKey]); // $record[$colKey];
				}
				fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
	
	
	
}