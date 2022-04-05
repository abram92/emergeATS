<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

use App\Candidate;

use App\User;

use DB;
use Hash;
use Arr;

use App\JobAd;

use App\Http\Traits\LookupListTrait;

use App\Http\Traits\DataFileTrait;
use App\Http\Traits\AddressTrait;
use App\Http\Traits\LongFullTextTrait;
use App\Http\Traits\ContactFieldTrait;
use App\Http\Traits\SearchTrait;
use App\Http\Traits\StaticWorkTrait;

use InterventionImage;
use Intervention\Image\Exception\NotReadableException;

class CandidateController extends Controller //UserController
{
	use LookupListTrait;
	use DataFileTrait;
	use AddressTrait;
	use ContactFieldTrait;
	use LongFullTextTrait;
	use SearchTrait;
	use StaticWorkTrait;
	
    public function __construct()
	{
		$this->model_class = 'App\Candidate';
		$this->initStaticWorkTrait();
	}	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		$queryFilter = [];
		$ranks = [];			

		$isSaved = false;

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
		$allstatuses = $this->getCandidateStatuses(); 
		$allconsultants = $this->getConsultants(); 

		$alleestatuses = $this->getEeStatuses(); 
		$allratings = $this->getCandidateRatings();
		$alllevels = $this->getCandidateLevels(); 
		$allavailabilities = $this->getCandidateAvailabilities(); 
		$allgenders = $this->getGenders();
		$allsalarycategories = $this->getSalaryCategories();
			
		$alllocations = $this->getLocations(); 
		$alljobtitles = $this->getJobTitles(); 

		
		if (!$q) {
			$data = collect([]);
		} else {
			if (is_array($q))
				$q = $this->removeEmptyCriteria($q);
		
			$result = Candidate::has('user')->with('status')->with('consultant')->with('user')->with('gender')
					->with('eestatus')->with('location')->with('salarycategory')->with('availability')
					->with('idealjob')->with('agencynotes')->with('summary')->with('sellme')
					->with('documents')->with('contactfields');

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
				$queryFilter['Statuses'] = $allstatuses->pluck('description', 'id')->only($q['statuses'])->implode(', ');
			}

			if (isset($q['eestatuses']) && (!empty($q['eestatuses']))) {
				$result->whereIn('ee_status_id', $q['eestatuses']); 
				$queryFilter['EE Statuses'] = $alleestatuses->pluck('description', 'id')->only($q['eestatuses'])->implode(', ');
			}

			if (isset($q['ratings']) && (!empty($q['ratings']))) {
				$result->whereIn('candidate_rating_id', $q['ratings']); 
				$queryFilter['Candidate Rating'] = $allratings->pluck('description', 'id')->only($q['ratings'])->implode(', ');
			}
			
			if (isset($q['availability']) && (!empty($q['availability']))) {

					$seq = current($allavailabilities->pluck('sort_seq', 'id')->only($q['availability'])->toArray());

					$avail = $allavailabilities->filter(function($value, $key) use ($seq) {
	
						if($value['sort_seq'] <= $seq){
							return true;
						}   
					})->pluck('id')->toArray();
				$result->whereIn('availability_id', $avail); 
				$queryFilter['Availability'] = $allavailabilities->pluck('description', 'id')->only($q['availability'])->implode(', ');
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

			if (isset($q['jobtitles']) && (!empty($q['jobtitles']))) {
				$result->whereIn('jobtitle_id', $q['jobtitles']); 
				$queryFilter['Job Title'] = $alljobtitles->only($q['jobtitles'])->implode(', ');
			}

			if (isset($q['actualjobtitles']) && ($q['actualjobtitles'])) {
				$titlesArray = $this->convertTextareaToArray($q['actualjobtitles']);
				if (!empty($titlesArray)) {
					$result->where(function ($query) use ($titlesArray) {
            // Everything within this closure will be grouped together
						$query->where($this->getRegexConditional("CONCAT(' ',jobtitle_text, ' ')",  '[^a-z]'.$titlesArray[0].'[^a-z]', true, false));
						foreach ($titlesArray as $k => $v) {
							if ($k > 0)
								$query->orWhere($this->getRegexConditional("CONCAT(' ',jobtitle_text, ' ')",  '[^a-z]'.$v.'[^a-z]', true, false));
						}
					});	
					$queryFilter['Actual Job Title'] = implode(', '.PHP_EOL, $titlesArray);
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

			if (isset($q['salarycategories']) && (!empty($q['salarycategories']))) {
				$result->whereIn('salary_category_id', $q['salarycategories']); 
				$queryFilter['Salary Category'] = $allsalarycategories->pluck('description', 'id')->only($q['salarycategories'])->implode(', ');
			}

			if (isset($q['minsalary']) && ($q['minsalary'])) {
				$result->where('salary', '>=', $q['minsalary']); 
				$queryFilter['Salary >='] = $q['minsalary'];
			}

			if (isset($q['maxsalary']) && ($q['maxsalary'])) {
				$result->where('salary', '<', $q['maxsalary']); 
				$queryFilter['Salary <'] = $q['maxsalary'];
			}
		

			if (isset($q['currlocations']) && (!empty($q['currlocations']))) {
				$result->whereIn('current_location_id', $q['currlocations']); 
				$queryFilter['Current Location'] = $alllocations->only($q['currlocations'])->implode(', ');
			}

			if (isset($q['preflocations']) && (!empty($q['preflocations']))) {
				$r = $q['preflocations'];
				$result->whereHas("preferredlocations", function ($q1) use ($r) {
					$q1->whereIn('location_id', $r);
				});
				$queryFilter['Preferred Location'] = $alllocations->only($q['preferredlocations'])->implode(', ');
			}

			if (isset($q['interviewed']) && (in_array( $q['interviewed'], ['t','f']))) {
				$result->where('interviewed', '=', $q['interviewed']);
				$queryFilter['Interviewed'] = ($q['interviewed'] == 't') ? 'Yes' : 'No';
			}
		

			if (isset($q['contact']['phone']) && (!empty($q['contact']['phone']))) {
				$r = $q['contact']['phone'];
				$result->whereHas("contactfields", function ($q1) use ($r) {
					$q1->where(DB::raw("replace(data, ' ','')"), 'ILIKE', '%'.str_replace(' ', '', $r).'%')->whereIn('contact_field_type_id', [2,3]);
				});
				$queryFilter['Contact Phone'] = $q['contact']['phone'];
			}
		
			if (isset($q['contact']['email']) && (!empty($q['contact']['email']))) {
				$r = $q['contact']['email'];
				$result->whereHas("contactfields", function ($q1) use ($r) {
					$q1->where('data', 'ILIKE', '%'.$r.'%')->where('contact_field_type_id', 1);
				});
				$queryFilter['Contact Email'] = $q['contact']['email'];
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
			$fulltextfields = ['idealjob'=>['var'=>'idealjob', 'label'=>'Ideal Job'],
							'summary'=>['var'=>'summary', 'label'=>'Summmary'],
							'agencynotes'=>['var'=>'agencynotes', 'label'=>'Agency Notes'],
							'sellme'=>['var'=>'skills', 'label'=>'Skills'],
							'textcv'=>['var'=>'textcv', 'label'=>'Text CV'],
							'interviewnotes'=>['var'=>'interviewnotes', 'label'=>'Interview Notes'],
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
					if (in_array($relation, ['sellme', 'textcv'])) {
						$ranks[$relation] = $this->FTS_getFullTextRank($r, 'App\Candidate', $relation);
					}
					
					$queryFilter[$htmllabel] = $q[$htmlvar];
					
				}
			}		
			$data = $result->orderBy('activated_at','DESC')->paginate(25);
		}
		
		$jobid = $request->get('jobid');
		
		if ($jobid)
			$jobad = JobAd::with('cvsendinstructions')->with('agencynotes')->find($jobid);
		else
			$jobad = null;

		if ($saveSearch)
			$searchid = $this->storeSearch($q, $queryFilter, 'candidate');

		$query = $searchid ? ['search_id'=>$searchid] : [];
		if ($jobid)
			$query['jobid'] = $jobid;

        return view('candidates.index',compact('data', 'ranks', 'allstatuses', 'allconsultants', 'alleestatuses', 'allratings',
						'alllevels', 'allsalarycategories', 'allavailabilities', 'allgenders', 'alllocations', 'alljobtitles', 'q', 'jobad', 'queryFilter', 'isSaved'))->withQuery($query);
 //           ->with('i', ($request->input('page', 1) - 1) * 25);
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		$statuses = $this->getCandidateStatuses(); 
		$consultants = $this->getConsultants(); 
		$eestatuses = $this->getEeStatuses(); 
		$candidateratings = $this->getCandidateRatings(); 
		$candidatelevels = $this->getCandidateLevels();
		$availabilities = $this->getCandidateAvailabilities(); 
		$genders = $this->getGenders(); 

		$salarycategories = $this->getSalaryCategories();
		$jobtitles = $this->getJobTitles();
		$locations = $this->getLocations();
		$candidate = null;
		$contact_types = $this->getContactFieldTypes();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
		
        return view('candidates.create',compact('candidate', 'statuses', 'consultants', 'jobtitles',
												'contact_types', 'eestatuses', 
												'candidateratings', 'candidatelevels', 'availabilities', 'genders',
												'salarycategories', 'locations'));
		
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
//		dd($request);
		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
 
 		$customMessages = [
			'consultant_id.required' => 'Consultant is required',
			'availability_id.required_if' => 'Availability is required when the status is Active.'
		];
//	dd($request);	
		$this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
			'gender_id' => 'exists:genders,id|nullable',
            'consultant_id' => 'required|exists:users,id',
			'status_id' => 'required|exists:candidate_statuses,id',
			'availability_id' => 'required_if:status_id,2|exists:candidate_availabilities,id|nullable',
 			'contacts.*.data' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
 			'emailfields' => 'required|uniqueemail:users,email,0,App\Candidate',
       ], $customMessages);

		$request->merge(array("interviewed" => $request->has("interviewed") ? true :false));

        $input = $request->all();
        $input['password'] = Hash::make('CandPassword');
		$input['activated_at'] = date('Y-m-d H:i:s');
		$input['is_active'] = '1';
		
		$input['email'] = implode(',', $input['emailfields']);
		$input['username'] = date('Ymd').'_'.$input['emailfields'][0];

		$success = false;
		DB::beginTransaction();
		try {

			$user = User::create($input);
			if ($user) {
				$input['id'] = $user->id;
				$candidate = Candidate::create($input);
				if ($candidate) {
					$candidate->user()->save($user);

				$locationData = $request->input('preferredlocations');
				$candidate->preferredlocations()->sync($locationData);

					$this->setLongFullTextFields($request, $candidate);
		
					$contactData = $request->input('contacts');
					if (!empty($contactData)) {
						$this->contactfieldmodel = $candidate;
						$this->updateOrCreateContactFields($request->input('contacts'));
					}
		
					$addressData = $request->input('addresses');
					if (!empty($addressData)) {
						$this->addressmodel = $candidate;
						$this->updateOrCreateAddresses($request->input('addresses'));
					}
					$success = true;
				}
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('candidates.show', $user->id)
                        ->with('success_message','Candidate created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Candidate not created');
		}	
    }

	private function getEmailsSentSinceLastStaticWorkEscalation($id) {
		if (!Auth::user()->hasRole('Static Work Admin')) {
			return $this->candCheckSentEmailsAfterReassignTrigger($id);
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
        $candidate = Candidate::has('user')->with('status')->with('consultant')->with('user')->with('eestatus')->with('location')->with('preferredlocations')->with('gender')
							->with('rating')->with('level')->with('availability')->with('jobapplicationsShortlisted')->with('jobapplicationsSentTo')->with('directapplications')->find($id);
//dd($candidate);
        $icons = $this->getContactFieldTypeIcons(); 
		$documents = $candidate->documents()->get();
		$addresses = $candidate->addresses;
		$contacts = $candidate->contactfields;
		
		$emailaddresses = $contacts->filter(function($item) {
					if($item->contact_field_type_id == 1) {
						return $item;
					}
			})->pluck('data')->toArray();
		$duplicates = $this->getDuplicateRecords($id, $emailaddresses);

/*		dd($candidate->jobapplications->whereHas("calendar_event_entities", function ($q1) {
					$q1->where("entityable_type='App\Candidate');
			})
			);
*/		
//DB::enableQueryLog();

		$audit = $candidate->audits()->with(array('user'=>function($query){
												$query->withTrashed();
											}))->orderBy('created_at', 'DESC')->limit(10)->get();
//$query = DB::getQueryLog();

$emails = $candidate->emails;

//dd($query);

		$statuses = $this->getCandidateStatuses();

		$newSent = $this->getEmailsSentSinceLastStaticWorkEscalation($id);

        return view('candidates.show',compact('candidate', 'icons', 'documents', 'addresses', 'contacts', 'emails', 'audit', 'statuses', 'newSent', 'duplicates'));
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
		$contact_types = $this->getContactFieldTypes();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);

        $candidate = Candidate::has('user')->with('user')->with('sellme')->with('textcv')->with('interviewnotes')
								->with('agencynotes')->with('idealjob')->with('summary')->find($id);
								
		$statuses = $this->getCandidateStatuses();
		$eestatuses = $this->getEeStatuses();
		$candidateratings = $this->getCandidateRatings();
		$candidatelevels = $this->getCandidateLevels();
		$availabilities = $this->getCandidateAvailabilities(); 
		$genders = $this->getGenders();
		$salarycategories = $this->getSalaryCategories();
		$locations = $this->getLocations();
		$jobtitles = $this->getJobTitles();

		$preferredlocations = $candidate->preferredlocations()->pluck('location_id', 'location_id');
		$addresses = $candidate->addresses;
		$contacts = $candidate->contactfields;
/*		$sellme = $candidate->sellme;
		$textcv = $candidate->textcv;
		$interviewnotes = $candidate->interviewnotes;
		$agencynotes = $candidate->agencynotes;
		$idealjob = $candidate->idealjob;
		$summary = $candidate->summary;
*/
		$newSent = $this->getEmailsSentSinceLastStaticWorkEscalation($id);

        return view('candidates.edit',compact('candidate', 'statuses', 'consultants', 'jobtitles',
												'contact_types', 'eestatuses', 
												'candidateratings', 'candidatelevels', 'availabilities', 'genders', 'preferredlocations', 
												'salarycategories', 'locations', 'addresses','contacts', 'newSent'));
//												'sellme', 'textcv', 'interviewnotes',
//												'agencynotes', 'idealjob', 'summary',  

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

		$customMessages = [
			'consultant_id.required' => 'Consultant is required',
			'availability_id.required_if' => 'Availability is required when the status is Active.'
		];
		$this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
			'gender_id' => 'exists:genders,id|nullable',
            'consultant_id' => 'required|exists:users,id',
			'status_id' => 'required|exists:candidate_statuses,id',
			'availability_id' => 'required_if:status_id,2|exists:candidate_availabilities,id|nullable',
  			'contacts.*.data' => 'required',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
 			'emailfields' => 'required|uniqueemail:users,email,'.$id.',App\Candidate',
       ], $customMessages);


		$request->merge(array("interviewed" => $request->has("interviewed") ? true :false));
        $input = $request->all();

		$input['email'] = implode(',', $input['emailfields']);

		$success = false;
		DB::beginTransaction();
		try {

			$candidate = Candidate::find($id);
			
			$candidate->edit_comment = isset($input['comments']) ? $input['comments'] : "";

			if ($candidate->update($input)) {

				$userprops = ['firstname'=> $input['firstname'],
				              'lastname'=> $input['lastname'],
							  'email'=> $input['email'],];
        //
				$candidate->user()->update($userprops);

				$locationData = $request->input('preferredlocations');
				$candidate->preferredlocations()->sync($locationData);

				$this->setLongFullTextFields($request, $candidate);
		
				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $candidate;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}
		
				$addressData = $request->input('addresses');
				if (!empty($addressData)) {
					$this->addressmodel = $candidate;
					$this->updateOrCreateAddresses($request->input('addresses'));
				}
				$success = true;
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('candidates.show', $candidate->id)
                        ->with('success_message','Candidate updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Candidate not updated');
		}	
    }


    public function activate(Request $request, $id)
    {

        $input = $request->all();
		$success = false;
		DB::beginTransaction();
		try {

			$candidate = Candidate::find($id);
			
			$candidate->edit_comment = isset($input['comments']) ? $input['comments'] : "";

			if ($candidate->update(['activated_at'=>date('Y-m-d H:i:s')])) {

				$success = true;
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('candidates.show', $candidate->id)
                        ->with('success_message','Candidate activated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Candidate not activated');
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
	

	private function setLongFullTextFields(Request $request, Candidate $candidate)
	{
		$longtextfields = [];
		if (($request->has('sellme')))
			$longtextfields['sellme'] =['value'=>strval($request->input('sellme')), 'relation'=>'sellme'];
		if (($request->has('textcv')))
			$longtextfields['textcv'] =['value'=>strval($request->input('textcv')), 'relation'=>'textcv'];
		if (($request->has('interviewnotes')))
			$longtextfields['interviewnotes'] =['value'=>strval($request->input('interviewnotes')), 'relation'=>'interviewnotes'];
		if (($request->has('agencynotes')))
			$longtextfields['agencynotes'] =['value'=>strval($request->input('agencynotes')), 'relation'=>'agencynotes'];
		if (($request->has('idealjob')))
			$longtextfields['idealjob'] =['value'=>strval($request->input('idealjob')), 'relation'=>'idealjob'];
		if (($request->has('summary')))
			$longtextfields['summary'] =['value'=>strval($request->input('summary')), 'relation'=>'summary'];
	
		$this->longtextfullmodel = $candidate;
		return $this->updateOrCreateLongFullTextFields($longtextfields);
	}
	
	
    public function importCandidate(Request $request)
    {
		$defaultStatus = 135;
		$openStatus = 1;
		$activeLookingStatus = 144;
		$defaultConsultant = 1;
		$defaultEE = 9;
		$defaultLevel = 3;
		$defaultRating = 3;

		$contact_types = $this->getContactFieldTypes()->pluck('id', 'name');

		$input = [];
		
		$inpVars = $request->all();

		$input['firstname'] = isset($inpVars["firstname"]) ? ($inpVars["firstname"]) : "";
		$input['lastname'] = isset($inpVars["lastname"]) ? ($inpVars["lastname"]) : "";
		$input['emailaddress'] = isset($inpVars["emailaddresses"]) ? ($inpVars["emailaddresses"]) : "";
		
		$input['contacts'] = [];
		$phone = isset($inpVars["phone"]) ? ($inpVars["phone"]) : "";
		if ($phone)
			$input['contacts'][] = ['data'=>$phone, 'type'=> $contact_types['Phone']];
		$cell = isset($inpVars["cell"]) ? ($inpVars["cell"]) : "";
		if ($cell)
			$input['contacts'][] = ['data'=>$cell, 'type'=> $contact_types['Cell']];
		
//		$dupWhere = [];
		$addresses = [];
		if ($input['emailaddress'])
		{
//			$addresses = explode("\n", $input['emailaddress']);
			$addresses = preg_split("/(,| |\||;".PHP_EOL.")/", $input['emailaddress']);
			foreach ($addresses as $address) {
				$input['contacts'][] = ['data'=>$address, 'type'=> $contact_types['Email']];
//				$dupWhere[] = ['data', 'ilike', '%'.$address.'%'];
			}
		}
		
//   $input['consultant = "";
		$input['idnumber'] = isset($inpVars["idnumber"]) ? ($inpVars["idnumber"]) : null;
		$input['birthdate'] = isset($inpVars["birthdate"]) ? ($inpVars["birthdate"]) : null;
   
		$skills = isset($inpVars["skills"]) ? ($inpVars["skills"]) : "";
		$textcv = isset($inpVars["textcv"]) ? ($inpVars["textcv"]) : "";
		$messagebody = isset($inpVars["messagebody"]) ? ($inpVars["messagebody"]) : "";	

		$idealjob = isset($inpVars["idealjob"]) ? ($inpVars["idealjob"]) : "";	      
   
		$webappl = isset($inpVars["webappl"]) ? ($inpVars["webappl"]) : 0;

		$duplicate = 0;   

		$duplicateCandidate = $this->getDuplicateRecords(0, $addresses, true);

/*		$duplicateCandidate = Candidate::whereHas("contactfields", function ($q1) use ($dupWhere) {
			
						foreach ($dupWhere as $k => $v) {
							if ($k > 0)
								$q1->orWhere([$v]);
							else
								$q1->where([$v]);
						}			
			})->OrderBy('id', 'DESC')->first();
*/
		if ($duplicateCandidate)
		{
			$existingStatus = $duplicateCandidate->status_id;
			$duplicate = 1;
			
	        if (in_array($existingStatus, array($defaultStatus, $openStatus, $activeLookingStatus))) {
		        
		        $ignoreDuplicate = 1;
		        
		        // 180116
		        if ($existingStatus == $activeLookingStatus) {
					$now = new DateTime('now');
					$from = new DateTime($duplicateCandidate->activated_at);
					$diff = date_diff($now, $from);
					if ($diff > 30)
		              $ignoreDuplicate = 0;
	            }
	            
	            if ($ignoreDuplicate) {
		           echo "false";
		           exit;
	            }
            }
			$input['duplicate'] = 1;
		}
		$refcodesmax = str_replace("'", "''", $messagebody);

		preg_match('/(?=\b|[#:_-])([A-Z]{2,3})[0-9]+/i', $refcodesmax, $matches);
		
		$jobcode = isset($matches[1]) ? strtolower($matches[1]) : '---';
		$consultant = User::whereRaw('lower(jobcode) ilike ?', $jobcode)->first();
		$consultantid = ($consultant) ? $consultant->id : $defaultConsultant;
	
        //
		$inputRequest = new Request($input);
        $this->validate($inputRequest, [
            'firstname' => 'required',
            'lastname' => 'required',
 			'contacts.*.data' => 'required'
        ]);

        $input = $inputRequest->all();
        $input['password'] = Hash::make('CandPassword');
		$input['activated_at'] = date('Y-m-d H:i:s');
		$input['username'] = date('Ymdhis').'_'.$input['lastname'];
		$input['is_active'] = 1;

		$input['email'] = (count($addresses) > 0) ? implode(',', $addresses) : 'undefined';
		$input['consultant_id'] = $consultantid;
		$input['ee_status_id'] = $defaultEE;
		$input['candidate_level_id'] = $defaultLevel;
		$input['candidate_rating_id'] = $defaultRating;
		$input['salary_category_id'] = 1;
		$input['status_id'] = $defaultStatus;

//dd($input);
		$success = false;
		DB::beginTransaction();
		try {

			$user = User::create($input);
			if ($user) {
				$input['id'] = $user->id;
				$candidate = Candidate::create($input);
				if ($candidate) {
					$candidate->user()->save($user);

				$locationData = $inputRequest->input('preferredlocations');
				$candidate->preferredlocations()->sync($locationData);

					$this->setLongFullTextFields($inputRequest, $candidate);

					$contactData = $inputRequest->input('contacts');
					if (!empty($contactData)) {
						$this->contactfieldmodel = $candidate;
						$this->updateOrCreateContactFields($contactData);
					}
		
					$addressData = $inputRequest->input('addresses');
					if (!empty($addressData)) {
						$this->addressmodel = $candidate;
						$this->updateOrCreateAddresses($addressData);
					}		

					$success = true;
				}
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			echo $user->id;
		} else {
			DB::rollback();
			echo "123";
			
			}	
    }
	
	public function importFile(Request $request, $modelid)
	{
		return $this->fileupload($request, $modelid);
	}
	
	public function bulkArchive(Request $request)
	{

		if (isset($request['archive_to']) && ($request['archive_to'])) {
			$res = Candidate::doesnthave('jobapplications')->where('activated_at', '<=', $request['archive_to']);
			if (isset($request['archive_from']) && ($request['archive_from'])) {			
				$res->where('activated_at', '>=', $request['archive_from']);
			}
			if (!isset($request['check'])) {
				$res->update('status_id', '3'); 
				$request->session()->flash('success_message', $res->count().'Candidates were successfully archived!');				
			} else
				return $res->count();
		}
		
        return view('candidates.bulkarchive');
	}
	
	
	public function avatarupload(Request $request, $id)
	{
		
//		dd($request);
        $this->validate($request, [
             'file' => 'required|mimes:jpg,jpeg,png,svg|max:2048'
         ]);
		 
        //retrieve authenticated user
        $user = User::findOrfail($id);

		$newname = "";
		
		if ($file = $request->file('file')) {
			$newname = $id.'-'.time().".".$file->getClientOriginalExtension(); //getClientOriginalName();
			
			// for save original image
			$ImageUpload = InterventionImage::make($file);
			$originalPath = storage_path('app/avatars_uploaded/');
			$ImageUpload->save($originalPath.$newname);

			// for save thumnail image

			$ImageUpload->resize(null, 150, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
			$thumbnailPath = public_path('avatars/');
			$ImageUpload = $ImageUpload->save($thumbnailPath.$newname);
			
			if ($user->avatar) {
				// del previous avatar ??
			}
			$user->avatar = $newname;
			$user->save();
		}
		
		// Delete Avatar
		if (isset($request['deleteavatar'])) {
			
			$user->avatar = '';
			$user->save();
		}
        return $newname;		
	}	
	

	public function mergeCandidate($idToKeep, $idToMerge) {
		
		$success = false;
		DB::beginTransaction();
		try {

			$candidateToKeep = Candidate::with('user')->with('preferredlocations')->with('addresses')->with('contactfields')->with('jobapplications')
						->with('directapplications')->with('eventparticipant')->with('documents')->find($idToKeep);
			
			$candidateToMerge = Candidate::with('user')->with('preferredlocations')->with('addresses')->with('contactfields')->with('jobapplications')
						->with('directapplications')->with('eventparticipant')->with('documents')->find($idToMerge);
			
			$toKeepAttr = $candidateToKeep->getAttributes();
			$toMergeAttr = $this->removeEmptyCriteria($candidateToMerge->getAttributes());
			unset($toMergeAttr['id']);
			$newPreferredLocations = $candidateToKeep->preferredlocations->pluck('id');
//			dd([$toKeepAttr, $toMergeAttr, array_merge($toKeepAttr, $toMergeAttr)]);	
			
			$input = array_merge($toKeepAttr, $toMergeAttr);
			$candidateToKeep->edit_comment = "Merged with ".$idToMerge;
			
			if ($candidateToKeep->update($input)) {

				$longtext = [];
				$sellmeToKeep = $this->getLongText($candidateToKeep->sellme);
				$sellmeToMerge = $candidateToMerge->sellme ? $this->getLongText($candidateToMerge->sellme) : '';			
				$longtext['sellme'] = implode(PHP_EOL.'----'.PHP_EOL, [$sellmeToKeep, $sellmeToMerge]);
			
				$agencynotesToKeep = $this->getLongText($candidateToKeep->agencynotes);
				$agencynotesToMerge = $candidateToMerge->agencynotes ? $this->getLongText($candidateToMerge->agencynotes) : '';			
				$longtext['agencynotes'] = implode(PHP_EOL.'----'.PHP_EOL, [$agencynotesToKeep, $agencynotesToMerge]);
			
				$longtextRequest = new Request($longtext);
        
//				$userprops = ['firstname'=> $input['firstname'],
//				              'lastname'=> $input['lastname'],
//							  'email'=> $input['email'],];
        //
// ??				$candidateToKeep->user()->update($userprops);

				if (!empty($newPreferredLocations))
					$candidateToKeep->preferredlocations()->sync($newPreferredLocations);

				$this->setLongFullTextFields($longtextRequest, $candidateToKeep);
		
				$contactData = $candidateToMerge->contactfields->toArray();
				foreach ($contactData as $k => $arr) {
					$contactData[$k]['type'] = $arr['contact_field_type_id'];
				}
//				dd($contactData);

				if (!empty($contactData)) {
					$this->contactfieldmodel = $candidateToKeep;
					$this->updateOrCreateContactFields($contactData);
				}
		
				$addressData = $candidateToMerge->addresses->toArray();
				if (!empty($addressData)) {
					$this->addressmodel = $candidateToKeep;
					$this->updateOrCreateAddresses($addressData);
				}
				
				////
				////   Move Documents
				////
				DB::table('data_files')
                ->whereRaw("datafileable_type = 'App\Candidate' and datafileable_id =$idToMerge")
//                ->update(['datafileable_id' =>$idToKeep, 'updated_at'=>'now']);				
                ->update(['datafileable_id' =>$idToKeep]);				
				////
				////   Move Job Applications ??
				////
				DB::table('job_applications')
                ->whereRaw("candidate_id = $idToMerge")
                ->update(['candidate_id' =>$idToKeep, 'updated_at'=>'now']);
	
				DB::table('calendar_event_entities')
                ->whereRaw("entityable_type = 'App\Candidate' and entityable_id =$idToMerge")
                ->update(['entityable_id' =>$idToKeep, 'updated_at'=>'now']);			
/*				DB::table('audits')
                ->whereRaw("auditable_type = 'App\Candidate' and auditable_id =$idToMerge")
                ->update(['auditable_id' =>$idToKeep, 'comments'=>'merged from $idToMerge', 'updated_at'=>'now']);
*/				
				$success = true;
			}
			
//			$candidateToKeep->update
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			DB::commit();
			$candidateToMerge->destroy([$idToMerge]);
			return redirect()->route('candidates.show', $idToKeep)
                        ->with('success_message','Candidate merged successfully');			
		} else {
			DB::rollback();			
			return redirect()->route('candidates.show', $idToKeep)
                        ->with('error_message','Candidate merged failed');					
		}			
	}	
/*	
function MergeCandidate($userID)
{

    $myDB = new DB;

    $sqlarray = array();
    
    $OldID = CheckDuplicate($userID, true);
    
 if ($OldID != '-') {
    $OldStatus = DB_GetFieldValue("Select activeflag as description from vuser where id = $OldID");
    $OldAgencyNote = DB_GetFieldValue("Select agencynotes as description from cndprofinfo where id = $OldID");
    $OldInterviewNote = DB_GetFieldValue("Select interviewnotes as description from cndprofinfo where id = $OldID");
    $OldJobTitle = DB_GetFieldValue("Select jobrole as description from cndprofinfo where id = $OldID");
    $OldInterviewed = DB_GetFieldValue("Select interviewed as description from cndprofinfo where id = $OldID");
    $OldConsultantid = DB_GetFieldValue("Select consultantid as description from cndpersinfo where id = $OldID");
    
    // added 17/10/31
    $OldSummary = DB_GetFieldValue("Select summary as description from cndprofinfo where id = $OldID");
    $OldJobTitleTxt = DB_GetFieldValue("Select jobtitletxt as description from cndprofinfo where id = $OldID");
    $OldCandLevel = DB_GetFieldValue("Select candlevel as description from cndprofinfo where id = $OldID");
    $OldCandRating = DB_GetFieldValue("Select candrating as description from cndprofinfo where id = $OldID");
    $OldEEstatus = DB_GetFieldValue("Select eestatus as description from cndprofinfo where id = $OldID");
    $OldSalaryCat = DB_GetFieldValue("Select salarycat as description from cndprofinfo where id = $OldID");
    $OldSalary = DB_GetFieldValue("Select salary as description from cndprofinfo where id = $OldID");
    
    
    $sqlarray[] = "delete from cand_lang where userid=$OldID";
    $sqlarray[] = "delete from cand_qual where userid=$OldID";
    $sqlarray[] = "delete from cand_skills where userid=$OldID";

    $sqlarray[] = "update jobhistory set candid=$userID where candid=$OldID";
    if ($OldAgencyNote != "") {
	    
       $oldNoteText = VIS_getLongText($OldAgencyNote, false);
       $NewAgencyNote = DB_GetFieldValue("Select agencynotes as description from cndprofinfo where id = $userID");
       $newNoteText = VIS_getLongText($NewAgencyNote, false);
       
       $sqlarray[] = "update cndprofinfo set agencynotes=$OldAgencyNote where id=$userID";

	   VIS_putLongText($oldNoteText.'\n'.$newNoteText, $OldAgencyNote, &$sqlarray);
       
    }

        
    // added 18/02/01
    $OldSellme = DB_GetFieldValue("Select sellme as description from cndprofinfo where id = $OldID");

    if ($OldSellme != "") {
	    
       $oldSellmeText = VIS_getLongText($OldSellme, false);
       $NewSellme = DB_GetFieldValue("Select sellme as description from cndprofinfo where id = $userID");
       $newSellmeText = VIS_getLongText($NewSellme, false);
       
       $sqlarray[] = "update cndprofinfo set sellme=$OldSellme where id=$userID";

	   VIS_putLongText($oldSellmeText.'\n'.$newSellmeText, $OldSellme, &$sqlarray);
       
    }
    //
        
    if ($OldInterviewNote != "")
       $sqlarray[] = "update cndprofinfo set interviewnotes=$OldInterviewNote where id=$userID";
    if ($OldJobTitle != "")
       $sqlarray[] = "update cndprofinfo set jobrole=$OldJobTitle where id=$userID";

    if ($OldSummary != "")
       $sqlarray[] = "update cndprofinfo set summary=$OldSummary where id=$userID";              
    if ($OldJobTitleTxt != "")
       $sqlarray[] = "update cndprofinfo set jobtitletxt=".VIS_makeSQLValue($OldJobTitleTxt)." where id=$userID";
    if ($OldCandLevel != "")
       $sqlarray[] = "update cndprofinfo set candlevel=$OldCandLevel where id=$userID";
    if ($OldCandRating != "")
       $sqlarray[] = "update cndprofinfo set candrating=$OldCandRating where id=$userID";
    if ($OldEEstatus != "")
       $sqlarray[] = "update cndprofinfo set eestatus=$OldEEstatus where id=$userID";
    if ($OldSalaryCat != "")
       $sqlarray[] = "update cndprofinfo set salarycat=$OldSalaryCat where id=$userID";
    if ($OldSalary != "")
       $sqlarray[] = "update cndprofinfo set salary=$OldSalary where id=$userID";                                          

    $sqlarray[] = "update cndpreflocations set candid=$userID where candid=$OldID and location not in (select location from cndpreflocations where candid=$userID)";                                          
    $sqlarray[] = "delete from cndpreflocations where candid=$OldID";
                         
    if ($OldConsultantid != "")
       $sqlarray[] = "update cndpersinfo set consultantid=$OldConsultantid where id=$userID";
    $sqlarray[] = "update cndpersinfo set duplicate=0 where id=$userID";   
    $sqlarray[] = "update cndprofinfo set interviewed=$OldInterviewed where id=$userID";   
    $sqlarray[] = "update evententitylink set entityid=$userID where entityid=$OldID and entitytypeid=1";   
    $sqlarray[] = "update cndfiles set candid=$userID where candid=$OldID";
    $sqlarray[] = "update candidateinterest set userid=$userID where userid=$OldID";   
    $sqlarray[] = "update cndhistory set userid=$userID where userid=$OldID";
    $sqlarray[] = "update cndhistory set actionuserid=$userID where actionuserid=$OldID";
    $sqlarray[] = "update candidateaudit set candidateid=$userID where candidateid=$OldID";
    $sqlarray[] = "delete from cndimages where candid=$OldID";
    $sqlarray[] = "update jobinterest set userid=$userID where userid=$OldID";
    $sqlarray[] = "update emaillinkedcandidates set candid=$userID where candid=$OldID";
    $sqlarray[] = "update notes set userid=$userID where userid=$OldID";
    $sqlarray[] = "update vuser set activeflag=$OldStatus where id=$userID";
    
    $sqlarray[] = "delete from cndpersinfo where id=$OldID";
    $sqlarray[] = "delete from cndprofinfo where id=$OldID";
    $sqlarray[] = "delete from sessioninfo where userid=$OldID";
    $sqlarray[] = "delete from usergroup where userid=$OldID";
  
    $sqlarray[] = "delete from vuser where id=$OldID";
error_log("Move $OldID to $userID");    
error_log(print_r($sqlarray, true));;
 }
// return true; 
    return DB_multSQL( "", $sqlarray );
}
*/	


	private function getDuplicateRecords(int $userid, Array $emailaddresses, $last=false) {
		
		if (empty($emailaddresses))
			return collect([]);
		$duplicateCandidate = Candidate::with('user')->with('status')->where('id', '<>', $userid)->whereHas("contactfields", function ($q1) use ($emailaddresses) {
				
				$q1->where('contact_field_type_id', 1);
				foreach ($emailaddresses as $k => $v) {
					if ($k > 0)
						$q1->orWhere('data', 'ILIKE', '%'.$v.'%');
					else
						$q1->where('data', 'ILIKE', '%'.$v.'%');
				}			
			})->OrderBy('id', 'DESC')->get();
//		if ($last)
//			$duplicateCandidate->first();
//		else	
//			$duplicateCandidate->get();
		return 	$duplicateCandidate;
	}
	

    public function getAutocompleteData(Request $request){
				$cP = [['key'=>1,'value'=>'Mavis'], ['key'=>2,'value'=>'Garth']];
//			return json_encode($cP);
   
        if($request->has('term')){
			$r = $request->input('term');
			

//            $cands = Candidate::with('user')->WhereHas("user", function ($q) use ($r) {
//					$q->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%');
//				})->get((['id','users.listname']));

//            $cands = User::where('userable_type', 'App\Candidate')->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%')->orderBy('firstname')->orderBy('lastname')->get(['id', 'firstname', 'lastname']);
            $cands = User::select(['id', 'firstname', 'lastname'])->where('userable_type', 'App\Candidate')->where(DB::raw($this->searchFullnameFields()), 'ILIKE', '%'.str_replace(' ', '%', $r).'%')->orderBy('firstname')->orderBy('lastname')->paginate(100);
			$cands->append('listname');
			$cands->map(function ($cand) {
				$cand['text'] = $cand['listname'];				
				return $cand;
			});
			
			$cands->makeHidden(['firstname', 'lastname', 'listname']);
			
			$candsArr = $cands->toArray();
			$response = [];
			$response['results'] = $candsArr['data'];
			$response['pagination']['more'] = ($candsArr['current_page'] < $candsArr['last_page']);

			return json_encode($response);			
//			$cands->toJson();
//			return $cands;
        }
    }

    public function test(){
        return view('test');
    }
	
}
