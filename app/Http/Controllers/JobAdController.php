<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use DB;
use App\JobAd;
use App\User;
use App\Client;
use App\ClientContact;


use App\LongFullText;

use App\NextJobNumber;

use App\Candidate;

use App\Http\Traits\LookupListTrait;
use App\Http\Traits\DataFileTrait;
use App\Http\Traits\LongFullTextTrait;
use App\Http\Traits\SearchTrait;
use App\Http\Traits\StaticWorkTrait;

class JobAdController extends Controller
{
	use LookupListTrait;
		use DataFileTrait;
		use LongFullTextTrait;
		use SearchTrait;
		use StaticWorkTrait;
		
    public function __construct()
	{
		$this->model_class = 'App\JobAd';
		$this->initStaticWorkTrait();
	}	

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
// dd($request);		
		$queryFilter = [];
		$isSaved = false;
		
		$isExport = $request->get('export');
		
		$q = $request->get('q');
		
		$q_upload_from = $request->get('q_upload_from');
		$q_upload_to = $request->get('q_upload_to');

		if($q_upload_from)
			$q['upload_from'] = $q_upload_from;
		if($q_upload_to)
			$q['upload_to'] = $q_upload_to;

		$saveSearch = false;
		if ($q) {
			$saveSearch = true;
		} else {
			$searchid = $request->get('search_id');
			if ($searchid) {
				list($q, $isSaved) = $this->getSavedSearch($searchid);
			}
		}

		// get drop downs
		$icons = $this->getContactFieldTypeIcons();

        $allstatuses = $this->getJobStatuses();
		$allconsultants = $this->getConsultants();

		$allgenders = $this->getGenders();

		$alleestatuses = $this->getEeStatuses();
		$allsalarycategories = $this->getSalaryCategories();
		$alllocations = $this->getLocations();

		if (!$q) {
			$data = collect([]);
		} else {
			if (is_array($q))
				$q = $this->removeEmptyCriteria($q);				

        //
			$result = JobAd::withCount([
    'emails as cv_sent_before_update' => function ($query) {
											$query->select(DB::raw('count(distinct(logged_emails.id))'));
						$query->whereColumn('logged_emails.date', '<=', 'job_ads.activated_at');
//							->where(function ($query) {
//								$query->where('type_id', 3);	
								$query->where('title', 'like', 'Emailed CV%');
    },
    'emails as cv_sent_current' => function ($query) {
											$query->select(DB::raw('count(distinct(logged_emails.id))'));
						$query->whereColumn('logged_emails.date', '>', 'job_ads.activated_at');
//							->where(function ($query) {
//								$query->where('type_id', 3);	
								$query->where('title', 'like', 'Emailed CV%');
    },	
	])->with('status')->with('consultant')->with('client')->with('gender')->with('clientcontacts')->with('projectplan')->with('summary')->with('salarycategory')->with('eestatus')->with('locations')->with('documents');


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

/* REGEX */
				if (!empty($titlesArray)) {
					$result->where(function ($query) use ($titlesArray) {
            // Everything within this closure will be grouped together
//						$query->where(DB::raw("CONCAT(' ',jobtitle_text, ' ')"), '~',  '[^a-z]'.$titlesArray[0].'[^a-z]');
						$query->where($this->getRegexConditional("CONCAT(' ',jobtitle_text, ' ')",  '[^a-z]'.$titlesArray[0].'[^a-z]', true));
						foreach ($titlesArray as $k => $v) {
							if ($k > 0)
								$query->orWhere($this->getRegexConditional("CONCAT(' ',jobtitle_text, ' ')",  '[^a-z]'.$v.'[^a-z]', true));
//								$query->orWhere(DB::raw("CONCAT(' ',jobtitle_text, ' ')"), '~', '[^a-z]'.$v.'[^a-z]');
						}
					});	
					$queryFilter['Job Title'] = implode(', ', $titlesArray);
				}
			}

			if (isset($q['jobtitleexclude']) && ($q['jobtitleexclude'])) {
				$excludeArray = $this->convertTextareaToArray($q['jobtitleexclude']);
				if (!empty($excludeArray)) {
					$result->where(function ($query) use ($excludeArray) {
            // Everything within this closure will be grouped together
						foreach ($excludeArray as $k => $v) {
							$query->where($this->getRegexConditional("CONCAT(' ',jobtitle_text, ' ')",  '[^a-z]'.$v.'[^a-z]', true, true));
						}
				
					});		
					$queryFilter['Job Titles Excluded'] = implode(', ', $excludeArray);
				}
			}
			
			if (isset($q['genders']) && (!empty($q['genders']))) {

					$seq = current($allgenders->pluck('sort_seq', 'id')->only($q['genders'])->toArray());

					$gend = $allgenders->filter(function($value, $key) use ($seq) {
	
						if($value['sort_seq'] <= $seq){
							return true;
						}   
					})->pluck('id')->toArray();
				$result->whereIn('gender_id', $gend); 
				$queryFilter['Gender'] = $allgenders->pluck('description', 'id')->only($q['genders'])->implode(', ');
			}	
			
			if (isset($q['consultants']) && (!empty($q['consultants']))) {
				$result->whereIn('consultant_id', $q['consultants']); 
					$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');
			}
		
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
				$queryFilter['Statuses'] = $allstatuses->pluck('description', 'id')->only($q['statuses'])->implode(', ');
			}

			if (isset($q['workserviced']) && (in_array( $q['workserviced'], ['t','f']))) {
				if ($q['workserviced'] == 't') {
					$result->WhereHas("emails", function ($q) {
						$q->whereColumn('logged_emails.date', '>', 'job_ads.activated_at');
//								$query->where('type_id', 3);						
								$q->where('title', 'like', 'Emailed CV%');
					});
				} else {
					$result->WhereDoesntHave("emails", function ($q) {
						$q->whereColumn('logged_emails.date', '>', 'job_ads.activated_at');
//								$query->where('type_id', 3);						
								$q->where('title', 'like', 'Emailed CV%');
					});					
				}
				$queryFilter['Work Serviced'] = ($q['workserviced'] == 't') ? 'Yes' : 'No';
			}
			
			if (isset($q['eestatuses']) && (!empty($q['eestatuses']))) {
				$result->whereIn('ee_status_id', $q['eestatuses']); 
				$queryFilter['EE Statuses'] = $alleestatuses->pluck('description', 'id')->only($q['eestatuses'])->implode(', ');
			}

			if (isset($q['contact'])) {

				$name = isset($q['contact']['name']) ? $q['contact']['name'] : null;
				$position = isset($q['contact']['position']) ? $q['contact']['position'] : null;
				$phone = isset($q['contact']['phone']) ? $q['contact']['phone'] : null;
				$email = isset($q['contact']['email']) ? $q['contact']['email'] : null;

				if ($name || $position || $phone || $email) {
					
					$result->whereHas('staff', function($contactQuery) use ($name, $position, $phone, $email, &$queryFilter) {
						
						$this->clientContactConditional($contactQuery, $name, $position, $phone, $email, $queryFilter);

					});
				}
			}

/*			if (isset($q['contact']['name']) && (!empty($q['contact']['name']))) {
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
*/	


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
			
			if ($isExport) {
				$data = $result->orderBy('activated_at','DESC')->get()->all();
				$data['columns'] = ['id'=>'ID', 'jobref'=>'Job Ref'];
				return $this->exportCsv('test.csv', $data);
			} else	
				$data = $result->orderBy('activated_at','DESC')->paginate(25);
		}
		
		$candid = $request->get('candid');

		if ($candid)
			$candidate = Candidate::with('user')->with('agencynotes')->with('jobapplications')->find($candid);
		else
			$candidate = null;
//dd($candidate);
		if ($saveSearch)
			$searchid = $this->storeSearch($q, $queryFilter, 'job');
		
		$query = $searchid ? ['search_id'=>$searchid] : [];
		if ($candid)
			$query['candid'] = $candid;

        return view('jobads.index',compact('data', 'icons', 'allstatuses', 'allconsultants', 'allgenders', 'alleestatuses', 'alllocations', 'allsalarycategories', 'q', 'candidate', 'queryFilter', 'isSaved'))->withQuery($query);
//            ->with('i', ($request->input('page', 1) - 1) * 25);
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($clientid)
    {
        //

        $statuses = $this->getJobStatuses();
		$consultants = $this->getConsultants();

		$eestatuses = $this->getEeStatuses();
		$salarycategories = $this->getSalaryCategories();
		$alllocations = $this->getLocations();
		$genders = $this->getGenders(); 


		$nextjobnumber = $this->getNextJobNumber('');
////		$clientid = isset($request['client_id']) ? $request['client_id'] : null;
////		if (isset($client_id)) {
			$clients = Client::where('id', '=', $clientid)->get()->pluck('name', 'id');
////			$clientcontacts = ClientContact::with('user')->where('client_id', '=', $client_id)->pluck('listname', 'id')->all();
			$allcontacts = ClientContact::where('client_id', '=', $clientid)->get()->pluck('listname', 'id');
////		} else {
////			$clients = Client::pluck('name','id')->all();
////			$first_client_id = key($clients); //->first()->id;
//			$clientcontacts = User::whereHas('clientcontact', function($q) use($first_client_id) {
//			$q->where('client_id', '=', $first_client_id);})->pluck('name', 'id')->all();			
////			$allcontacts = ClientContact::where('client_id', '=', $first_client_id)->get()->pluck('listname', 'id');
////		}
		
        return view('jobads.create',compact('statuses', 'eestatuses', 'consultants', 'alllocations', 'clients', 'allcontacts', 'genders', 'salarycategories', 'nextjobnumber'));
		
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
            'jobref' => 'required',
			'status_id' => 'required|exists:job_statuses,id',
			'client_id' => 'required|exists:clients,id',
			'consultant_id' => 'required|exists:users,id',
			'gender_id' => 'exists:genders,id|nullable',
            'jobtitle_text' => 'required',
			'salary_from' => 'required|numeric|min:0',
			'fulldescription' => 'required'
        ]);

        $input = $request->all();
		$input['jobref'] .= $input['nextjobnumber'];
        $input['activated_at'] = date('Y-m-d H:i:s');

		$success = false;
		DB::beginTransaction();
		try {
			
			$jobad = JobAd::create($input);
	
			if ($jobad) {
				$this->setLongFullTextFields($request, $jobad);
				$contactData = $request->input('contacts');
				$jobad->clientcontacts()->sync($contactData);
				$locationData = $request->input('locations');
				$jobad->locations()->sync($locationData);				
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('jobs.show', $jobad->id)
                        ->with('success_message','Job created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Job not created');
		}
			
    }

	private function getEmailsSentSinceLastStaticWorkEscalation($id) {
		if (!Auth::user()->hasRole('Static Work Admin')) {
			return $this->jobCheckSentEmailsAfterReassignTrigger($id);
		}
		return ['toClient'=>0, 'toCandidate'=>0];	
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
        $jobad = JobAd::with('status')->with('consultant')->with('client')->with('clientcontacts')->with('eestatus')->with('locations')->with('gender')
				->with('jobapplicationsLinked')
					->with('jobapplicationsProspect')
						->find($id);

        $icons = $this->getContactFieldTypeIcons();
		$documents = $jobad->documents()->get();
//		$contacts = $jobad->clientcontacts()->get();
		$audit = $jobad->audits()->with(array('user'=>function($query){
												$query->withTrashed();
											}))->orderBy('created_at', 'DESC')->limit(10)->get();
		$statuses = $this->getJobStatuses();
		
		$newSent = $this->getEmailsSentSinceLastStaticWorkEscalation($id);
        return view('jobads.show',compact('jobad', 'icons', 'documents', 'audit', 'statuses', 'newSent'));
		
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
        $jobad = JobAd::with('client')->with('cvsendinstructions')->with('agencynotes')->with('summary')->with('projectplan')->with('skills')->with('technicalarea')->with('fulldescription')->find($id);

        $statuses = $this->getJobStatuses();
		$consultants = $this->getConsultants();

		$genders = $this->getGenders();
		$eestatuses = $this->getEeStatuses();
		$salarycategories = $this->getSalaryCategories();
		$alllocations = $this->getLocations();

		$allcontacts = ClientContact::where('client_id', '=', $jobad->client_id)->get()->pluck('listname', 'id');

/*		$cvsendinstructions = $jobad->cvsendinstructions();
		$agencynotes = $jobad->agencynotes();
		$skills = $jobad->skills();
		$technicalarea = $jobad->technicalarea();
		$fulldescription = $jobad->fulldescription();
*/		
		$contacts = $jobad->clientcontacts()->pluck('id','id');
		$locations = $jobad->locations()->pluck('id','id');
		
		$clients = [$jobad->client->id=> $jobad->client->name];

		$newSent = $this->getEmailsSentSinceLastStaticWorkEscalation($id);
        return view('jobads.edit',compact('jobad', 'statuses', 'clients', 'consultants',  
											'eestatuses', 'contacts', 'allcontacts', 'genders', 
											'alllocations', 'locations', 'salarycategories', 'newSent')); 
//											'cvsendinstructions', 'agencynotes', 'skills',
//											'technicalarea','fulldescription'));
		
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
            'jobref' => 'required',
			'status_id' => 'required|exists:job_statuses,id',
//			'client_id' => 'required|exists:clients,id',
			'consultant_id' => 'required|exists:users,id',
			'gender_id' => 'exists:genders,id|nullable',
            'jobtitle_text' => 'required',
			'salary_from' => 'required|numeric|min:0',
			'fulldescription' => 'required'
        ]);

        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$jobad = JobAd::find($id);

		$jobad->edit_comment = isset($input['comments']) ? $input['comments'] : "";
		
			if ($jobad->update($input)) {
		
				$this->setLongFullTextFields($request, $jobad);

				$contactData = $request->input('contacts');
				$jobad->clientcontacts()->sync($contactData);

				$locationData = $request->input('locations');
				$jobad->locations()->sync($locationData);
				
				$success = true;
			}
		} catch (\Exception $e) {
			
			dd($e);
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('jobs.index')
                        ->with('success_message','Job updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Job not updated');
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
    }
	
	public function activate(Request $request, $id)
    {

        $input = $request->all();
		$success = false;
		DB::beginTransaction();
		try {

			$jobad = JobAd::find($id);
			
			$jobad->edit_comment = isset($input['comments']) ? $input['comments'] : "";

			if ($jobad->update(['activated_at'=>date('Y-m-d H:i:s')])) {

				$success = true;
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('jobads.show', $jobad->id)
                        ->with('success_message','Job activated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Job not activated');
		}	
    }
	
	private function setLongFullTextFields(Request $request, Jobad $jobad)
	{
		$longtextfields = [];
		if ($request->has('cvsendinstructions'))
			$longtextfields['cvsendinstructions'] =['value'=>strval($request->input('cvsendinstructions')), 'relation'=>'cvsendinstructions'];
		if (($request->has('summary')))
			$longtextfields['summary'] =['value'=>strval($request->input('summary')), 'relation'=>'summary'];
		if ($request->has('agencynotes'))
			$longtextfields['agencynotes'] =['value'=>strval($request->input('agencynotes')), 'relation'=>'agencynotes'];
		if ($request->has('projectplan'))
			$longtextfields['projectplan'] =['value'=>strval($request->input('projectplan')), 'relation'=>'projectplan'];
		if ($request->has('skills'))
			$longtextfields['skills'] =['value'=>strval($request->input('skills')), 'relation'=>'skills'];
//		if ($request->has('technicalarea'))
//			$longtextfields['technicalarea'] =['value'=>strval($request->input('technicalarea')), 'relation'=>'technicalarea'];
		if ($request->has('fulldescription'))
			$longtextfields['fulldescription'] =['value'=>strval($request->input('fulldescription')), 'relation'=>'fulldescription'];
		
		$this->longtextfullmodel = $jobad;
		return $this->updateOrCreateLongFullTextFields($longtextfields);
	}
	
	
	private function getNextJobNumber($auto)
	{
		if ($auto)
			return str_pad($auto, 5, "0", STR_PAD_LEFT);
		$newJob = new NextJobNumber;
		$newJob->user_id = Auth::user()->id;
		$newJob->save();
			return str_pad($newJob->id, 5, "0", STR_PAD_LEFT);

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
            fputcsv($file, $columns);

            foreach ($data['results'] as $record) {
				$row = [];
				foreach($columns as $colKey=>$colVal) {
                     $row[] =  $record[$colKey];
				}
				fputcsv($file, $row);
            }

            fclose($file);
			return $file;
        };
		dd($callback);
		$callback = '12,4,5,6';
    return response()->make($callback, 200, $headers);

 //       return response()->stream($callback, 200, $headers);
    }
	
	
    public function getAutocompleteData(Request $request){
   
        if($request->has('term')){
			$r = $request->input('term');
			
            $jobs = JobAd::select(['id', 'jobref as text'])->where('jobref', 'ILIKE', str_replace(' ', '%', $r).'%')->orderBy('jobref')->paginate(100)->toArray();
			
			$response = [];
			$response['results'] = $jobs['data'];
			$response['pagination']['more'] = ($jobs['current_page'] < $jobs['last_page']);

			return json_encode($response);

        }
    }	

}
