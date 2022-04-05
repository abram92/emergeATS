<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use DB;
use App\JobApplication;
use App\JobApplicationStatus;
use App\JobAd;
use App\Client;
use App\ClientContact;
use App\Candidate;
use App\User;
use Illuminate\Support\Facades\Mail;

use App\Http\Traits\LookupListTrait;

use App\Mail\CandidateToClient;
use App\Mail\JobSpecToCandidate;

use\Illuminate\Mail\Events\MessageSent;

class JobApplicationController extends Controller
{
	use LookupListTrait;


	protected $status_codes = [];
    public function __construct()
	{
		$this->model_class = 'App\JobApplication';
		$this->status_codes = JobApplicationStatus::get()->pluck('id', 'system_code');
//		dd($this->status_codes);
	}	

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		
	
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($clientid)
    {
        //
		
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
        $jobapp = JobApplication::with('status')->with('applicationable')->with('candidate')->with('emails')->find($id);
		
		$audit = $jobapp->audits()->with('user')->orderBy('created_at', 'DESC')->limit(10)->get();
		
		$statuses = $this->getJobApplicationStatuses(); 		

        return view('jobapplications.show',compact('jobapp', 'audit', 'statuses'));
		
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
        $jobapp = JobApplication::with('candidate')->with('applicationable')->find($id);

		$statuses = $this->getJobApplicationStatuses(); 
		
        return view('jobapplications.edit',compact('jobapp', 'statuses', )); 
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
            'status_id' => 'required|exists:job_application_statuses,id',
        ]);

        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$jobapp = JobApplication::find($id);

		$jobapp->comments = isset($input['comments']) ? $input['comments'] : "";
		$jobapp->status_id = isset($input['status_id']) ? $input['status_id'] : $jobapp->status_id;
		
			if ($jobapp->update($input)) {
				$success = true;
			}
		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('jobs.index')
                        ->with('success_message','Job Application updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Job Application not updated');
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
	
	

	public function RegisterInterest($candidate_id, $application_id, $application_type)
	{

		$jobapp = JobApplication::firstOrNew(['candidate_id'=>$candidate_id,'applicationable_id'=>$application_id, 'applicationable_type'=>$application_type]);

		if($jobapp->exists)
		{
			return false;  // already set
		}
	
		$jobapp->status_id = $this->status_codes['CNDPRSPCT'];
		
		$jobapp->save();

		return true;
	}

	public function ExpressInterest($candidate_id, $application_id, $application_type)
	{

		$jobapp = JobApplication::firstOrNew(['candidate_id'=>$candidate_id,'applicationable_id'=>$application_id, 'applicationable_type'=>$application_type]);

	
		$jobapp->status_id = $this->status_codes['CLNTINTRST'];
		
		$jobapp->save();

		return true;
	}
	
	public function LinkCandidateJob(Request $request, $candidate_id, $jobad_id)
	{
		if ($this->RegisterInterest($candidate_id, $jobad_id, 'App\JobAd'))
			return true;
		else
			return false;
	}	
	
	public function LinkCandidateClient(Request $request, $candidate_id, $client_id)
	{
		if ($this->RegisterInterest($candidate_id, $client_id, 'App\Client'))
			return session()->now('message', 'Success! message.');
		else
			return session()->now('message', 'Fail! message.');
	}	
	
	public function linkJobMultipleCandidates(Request $request, $matchid) {
		$linked['Success'] = [];
		$linked['Failed'] = [];

		$success = $fail = null;
		if (\Session::has('search_cand_'.$matchid)) {
			foreach (\Session::get('search_cand_'.$matchid) as $key => $value) {
				if ($this->RegisterInterest($value, $matchid, 'App\JobAd'))
					$linked["Success"][] = $value;
				else
					$linked["Failed"][] = $value;
				\Session::forget('search_cand_'.$matchid.'.'.$key); 				
			}
		}	
		
		$jobad = JobAd::find($matchid);

		if (!empty($linked["Success"])) {
			$success = Candidate::with('user')->whereIn('id', $linked["Success"])->get(); 
			$request->session()->flash('success_message', 'Candidates were successfully linked!');
		}

		if (!empty($linked["Failed"])) {
			$fail = Candidate::with('user')->whereIn('id', $linked["Failed"]); 
			$request->session()->flash('info_message', 'Candidates were already linked!')->get();
		}

        return view('jobapplications.multiplecandidates',compact('jobad', 'success', 'fail')); 
	}

	function linkCandMultipleJobs(Request $request, $matchid) {
		
//		dd(\Session::all());
		if (\Session::has('search_job_'.$matchid)) {
			foreach (\Session::get('search_job_'.$matchid) as $key => $value) {
				if ($this->RegisterInterest($matchid, $value, 'App\JobAd'))
					$linked["Success"][] = $value;
				else
					$linked["Failed"][] = $value;
				\Session::forget('search_job_'.$matchid.'.'.$key); 				
			}
		}	

		$success = $fail = null;
		$candidate = Candidate::with('user')->find($matchid);
		
		if (!empty($linked["Success"])) {
			$success = JobAd::whereIn('id', $linked["Success"])->get(); 
    $request->session()->flash('success_message', 'Jobs were successfully linked!');
		}
		if (!empty($linked["Failed"])) {
			$fail = JobAd::whereIn('id', $linked["Failed"])->get(); 
    $request->session()->flash('info_message', 'Jobs were already linked!');
		}

        return view('jobapplications.multiplejobs',compact('candidate', 'success', 'fail')); 
	
	}
	
	/////
	/////
	/////      EMAILS
	///// 
	/////
	
	
	
	/////
	/////
	/////      MULTIPLE CVS TO ONE CLIENT
	///// 
	/////	
	function emailCvsToClient_create(Request $request, $matchid) {
		
		$candids = [];
		$matchtype = explode('_', $matchid);
		
		if ($matchtype[0] == 'j') {
			$jobid = (int)$matchtype[1];
		if (\Session::has('search_cand_'.$jobid)) {
			foreach (\Session::get('search_cand_'.$jobid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
				$candids[] = $value;
			}
		}
			$target = JobAd::with('agencynotes')->with('clientcontacts')->find($jobid);
			$clientid = $target->client_id;
		} else {
			$jobapplication = JobApplication::find((int)$matchtype[1]);
			if ($jobapplication->applicationable_type == 'App\Client') {
				$clientid = $jobapplication->applicationable_id;
				$target = Client::find($clientid);
			} else {
				$jobid = $jobapplication->applicationable_id;
				$target = JobAd::with('agencynotes')->with('clientcontacts')->find($jobid);
				$clientid = $target->client_id;
			}
			$candids[] = $jobapplication->candidate_id;			
		}

		$success = $fail = null;
//		dd($jobad);
		$allcontacts = ClientContact::where('client_id', '=', $clientid)->get()->pluck('listname', 'id');

		if (!empty($candids))
			$candidates = Candidate::with('user')->with('agencynotes')->with('textcv')->with('documents')->whereIn('id', $candids)->get();
		else {
			$candidates = null;
			$request->session()->flash('error_message', 'No Candidates Selected!');
		}			
        return view('jobapplications.emailcvstoclient',compact('candidates', 'target', 'allcontacts', 'matchid')); 
	
	}
	

	function emailCvsToClient_store(Request $request, $matchid) {
//		dd($request);
		$jobid = null;
		$clientid = null;
		$jobapplicationid = null;
		
		$jobappexpress = [];
		$this->validate($request, [
            'contacts' => 'required|array|min:1',
            'subject' => 'required',
			'coverpage' => 'required'
        ]);

		$contacts = ClientContact::with('emailaddresses')->find($request['contacts']);
//dd(Auth::user());

		list($from, $emailsig) = $this->getEmailSenderProperties(Auth::user());

		$candids = [];
		$attachids = $request['attachIds'];
		
		
		$matchtype = explode('_', $matchid);
		
		if ($matchtype[0] == 'j') {
			$jobid = (int)$matchtype[1];
		if (\Session::has('search_cand_'.$jobid)) {
			foreach (\Session::get('search_cand_'.$jobid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
				$candids[] = $value;
			}
		}
			$target = JobAd::with('client')->find($jobid);
	//		$clientid = $target->client_id;
			$clientname = $target->client->name;
		} else {
			$jobapplicationid = (int)$matchtype[1];
			$jobapplication = JobApplication::find($jobapplicationid);
			if ($jobapplication->applicationable_type == 'App\Client') {
				$clientid = $jobapplication->applicationable_id;
				$target = Client::find($clientid);
				$clientname = $target->name;
				$jobappexpress[] = ['type'=>'App\Client', 'candid'=>$jobapplication->candidate_id, 'typeid'=>$clientid];
			} else {
				$jobid = $jobapplication->applicationable_id;
				$target = JobAd::with('client')->find($jobid);
//				$clientid = $target->client_id;
				$clientname = $target->client->name;
				$jobappexpress[] = ['type'=>'App\JobAd', 'candid'=>$jobapplication->candidate_id, 'typeid'=>$jobid];
			}
			$candids[] = $jobapplication->candidate_id;			
		}
		
		
		$candidates = Candidate::with('user')->with('documents')->find($candids);		
		$to = [];
		$contactids = [];
		foreach($contacts as $contact) {
			$contactname = $contact->listname;
			$contactids[] = $contact->id;
			foreach ($contact->emailaddresses as $toAddress) {
				$to[] = ['email' => $toAddress->data, 'name' => $contactname];
			}
		}
		$data = ['messagebody'=>$request['coverpage']];
		if ($emailsig)
			$data['emailsig'] = $emailsig;
		$attachToSend = [];
		if ($attachids) {
			foreach($candidates as $candidate) {
//				dd($candidate->documents);
				$attach = $candidate->documents->keyBy('id')->toArray();
//				dd($attach);
				$attachToSend += array_filter($attach, function($key) use ($attachids) {
						return in_array($key, $attachids);
					}, ARRAY_FILTER_USE_KEY);
//				dd($attachToSend);
			}
		}
		if (!empty($attachToSend))
			$data['attachments'] = $attachToSend;

			$calendarProperties = [];
			$calendarProperties['title'] = 'Emailed CVs to Client '.$clientname;
			$calendarProperties['type_id'] = 3;
			$calendarProperties['candidate'] = $candids;
			if ($jobid)
				$calendarProperties['job'][] = $jobid;
			if ($clientid)
				$calendarProperties['client'][] = $clientid;
			if ($jobapplicationid)
				$calendarProperties['jobapplication'][] = $jobapplicationid;
			$calendarProperties['clientcontact'] = $contactids;
			$calendarProperties['files'] = $attachids;

// dd($data);		
			$data['subject'] = $request['subject'];
			Mail::to($to)->send(new CandidateToClient($data, $calendarProperties, $from));

		$mailsent = (count(Mail::failures()) == 0);
	if ($mailsent) {
	    $request->session()->flash('success_message', 'CVs were successfully emailed to '.$clientname);
		foreach ($jobappexpress as $i)
			$this->ExpressInterest($i['candid'], $i['typeid'], $i['type']);		    
	} else
		 $request->session()->flash('error_message', 'Error emailing CVs to '.$clientname);
// Add Calendar Event here ??
//        return view('jobapplications.emailcvstoclient',compact('candidates', 'jobads', 'contacts')); 
		return view('jobapplications.mailedcvs');
	}


	/////
	/////
	/////      MULTIPLE JOB SPECS TO ONE CANDIDATE
	///// 
	/////	
	
	function emailJobSpecsToCandidate_create(Request $request, $candid) {
		
		$jobids = $request->input('jobIds');
		if (!$jobids) {
			$jobids = $request->old('jobIds');
		}
		if (!$jobids) {
			$jobids = [];
			if (\Session::has('search_job_'.$candid)) {
				foreach (\Session::get('search_job_'.$candid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
					$jobids[] = $value;
				}
			}
		}			
//dd($jobid);

		$success = $fail = null;
		$candidates = Candidate::with('user')->with('documents')->find($candid);
//	dd($jobad);
		if (!empty($jobids))
			$jobads = JobAd::with('agencynotes')->whereIn('id', $jobids)->get();
		else {
			$jobads = null;
			$request->session()->flash('error_message', 'No Jobs Selected!');
		}			
		$view = 'candidate';
        return view('jobapplications.emailjobspecstocandidates',compact('candidates', 'jobads', 'view')); 
	
	}

	function emailJobSpecsToCandidate_store(Request $request, $candid) {

		$jobids = [];
		$attachids = $request['attachIds'];
		
		if (\Session::has('search_job_'.$candid)) {
			foreach (\Session::get('search_job_'.$candid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
				$jobids[] = $value;
			}
		}
		$request['jobids'] = $jobids;

		$this->validate($request, [
            'jobids' => 'required|array|min:1',
            'subject' => 'required',
			'coverpage' => 'required'
        ]);

		list($from, $emailsig) = $this->getEmailSenderProperties(Auth::user());
		
		$candidate = Candidate::with('user')->with('emailaddresses')->find($candid);

		$jobads = JobAd::with('documents')->find($jobids);		
		$to = [];
		
		$contactname = $candidate->listname;
		foreach ($candidate->emailaddresses as $toAddress) {
			$to[] = ['email' => $toAddress->data, 'name' => $contactname];
		}
		
		$data = ['messagebody'=>$request['coverpage']];
		
		if ($emailsig)
			$data['emailsig'] = $emailsig;
		$attachToSend = [];
		if ($attachids) {
			foreach($jobads as $jobad) {
				$attach = $jobad->documents->keyBy('id')->toArray();
//				dd($attach);
				$attachToSend += array_filter($attach, function($key) use ($attachids) {
						return in_array($key, $attachids);
					}, ARRAY_FILTER_USE_KEY);
//				dd($attachToSend);
			}
		}
		
		if (!empty($attachToSend))
			$data['attachments'] = $attachToSend;

			$calendarProperties = [];
			$calendarProperties['title'] = 'Emailed Job Spec to Candidate '.$candidate->user->listname;
			$calendarProperties['type_id'] = 2;
			$calendarProperties['candidate'][] = $candid;
			$calendarProperties['job'] = $jobids;
			$calendarProperties['files'] = $attachids;

// dd($data);		
			$data['subject'] = $request['subject'];
		Mail::to($to)->send(new JobSpecToCandidate($data, $calendarProperties, $from));
// dd($candidate);		
		$mailsent = (count(Mail::failures()) == 0);
	if ($mailsent)
	    $request->session()->flash('success_message', 'Job Specs successfully emailed to '.$candidate->user->listname);
	else
		 $request->session()->flash('error_message', 'Error emailing Job Specs to '.$candidate->user->listname);
		
		return view('jobapplications.mailedjobspecs');

	}
	
	/////
	/////
	/////      SINGLE JOB SPEC TO MULTIPLE CANDIDATES
	///// 
	/////		
	function emailJobSpecToCandidates_create(Request $request, $jobid) {
		
		$applids = $request->input('applIds');
		if (!$applids)
			$applids = $request->old('applIds');
		$candids = [];
		if (!$applids) {
//			dd(\Session::all());
			if (\Session::has('search_cand_'.$jobid)) {
				foreach (\Session::get('search_cand_'.$jobid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
					$candids[] = $value;
				}
			}
		} else {
			foreach ($applids as $applid) {
				$chkids = explode('_', $applid);
				$candids[] = $chkids[1];
			}
		}			
//dd($jobid);

		$success = $fail = null;
		$jobads = JobAd::with('documents')->find($jobid);
//	dd($jobad);
		if (!empty($candids))
			$candidates = Candidate::with('user')->with('agencynotes')->whereIn('id', $candids)->orderBy('activated_at', 'DESC')->get();
		else {
			$candidates = null;
			$request->session()->flash('error_message', 'No Candidates Selected!');
		}			
		$view = 'job';
        return view('jobapplications.emailjobspecstocandidates',compact('candidates', 'jobads', 'view', 'applids')); 
	
	}


	function emailJobSpecToCandidates_store(Request $request, $jobid) {

		$candids = [];
		$jobapplications = [];
		
		$attachids = $request['attachIds'];
		$discardids = isset($request['discardIds']) ? $request['discardIds'] : [];
		
		$applids = $request->input('applIds');
		if (!$applids) {
//			dd(\Session::all());
			if (\Session::has('search_cand_'.$jobid)) {
				foreach (\Session::get('search_cand_'.$jobid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
					$candids[] = $value;
				}
			}
		} else {
			foreach ($applids as $applid) {
				$chkids = explode('_', $applid);
				$candid = $chkids[1];
				$candids[] = $candid;
				$jobapplications[$candid] = $chkids[0];
			}
		}			

		$candids = array_diff($candids, $discardids);

		$request['candids'] = $candids;

$allcustom = true;
foreach($candids as $candid) {
if (strip_tags($request['cnd'.$candid]) == '')
				$allcustom = false;
}
	$request['allcustom'] = $allcustom;
		$this->validate($request, [
            'candids' => 'required|array|min:1',
            'subject' => 'required',
			'coverpage' => 'required_if:allcustom,false'
        ], ['candids.required'=> 'All candidates have been discarded', 
		    'coverpage.required_if'=>'Default message required if custom message is not f illed in for all candidates']);


		$jobad = JobAd::with('documents')->find($jobid);		
		
		list($from, $emailsig) = $this->getEmailSenderProperties(Auth::user());
		$candidates = Candidate::with('user')->with('emailaddresses')->with('documents')->find($candids);		

		$attachToSend = [];
		
		if ($attachids) {
			$attach = $jobad->documents->keyBy('id')->toArray();
			$attachToSend += array_filter($attach, function($key) use ($attachids) {
					return in_array($key, $attachids);
			}, ARRAY_FILTER_USE_KEY);
		}
		$sent_success = $sent_fail = [];
		
		foreach($candidates as $candidate) {
			$data = [];
			$to = [];
			
			$calendarProperties = [];
			$calendarProperties['title'] = 'Emailed Job Spec to Candidate '.$candidate->user->listname;
			$calendarProperties['type_id'] = 2;
			$calendarProperties['candidate'][] = $candidate->id;
			if (isset($jobapplications[$candidate->id]))
				$calendarProperties['jobapplication'][] = $jobapplications[$candidate->id];
			$calendarProperties['job'][] = $jobid;
			$calendarProperties['files'] = $attachids;
/*			foreach($contacts as $contact) {
				$contactname = $contact->listname;
				foreach ($contact->emailaddresses as $toAddress) {
					$to[] = ['email' => $toAddress->data, 'name' => $contactname];
				}
			}
*/			
			$contactname = $candidate->user->listname;
			foreach ($candidate->emailaddresses as $toAddress) {
				
				$to[] = ['email' => $toAddress->data, 'name' => $contactname];
			}
			
//			if ($request['cnd'.$candidate->id])
			if (strip_tags($request['cnd'.$candidate->id]) != '')
				$data['messagebody'] = $request['cnd'.$candidate->id];
			else
				$data['messagebody'] = $request['coverpage'];

			if ($emailsig)
				$data['emailsig'] = $emailsig;
			if (!empty($attachToSend))
				$data['attachments'] = $attachToSend;




// dd($data);		
			$data['subject'] = $request['subject'];
			Mail::to($to)->send(new JobSpecToCandidate($data, $calendarProperties, $from));
			
		$mailsent = (count(Mail::failures()) == 0);
	if ($mailsent)
	    $sent_success[] = 'Job Specs successfully emailed to '.$candidate->user->listname;
	else
		$sent_fail[] = 'Error emailing Job Specs to '.$candidate->user->listname;
			
		}
		if (!empty($sent_success))
			$request->session()->flash('success_message', $sent_success);
		if (!empty($sent_fail))
			$request->session()->flash('error_message', $sent_fail);
			
		return view('jobapplications.mailedjobspecs');

	}
	
	/////
	/////
	/////      SINGLE CV TO MULTIPLE CLIENTS
	///// 
	/////	
	function emailCvToMultipleClients_create(Request $request, $candid) {
		
		$applids = $request->input('applIds');
//		dd($request);
		if (!$applids)
			$applids = $request->old('applIds');
		$jobids = [];
		$clientids = [];
		if (!$applids) {
//			dd(\Session::all());
			if (\Session::has('search_job_'.$candid)) {
				foreach (\Session::get('search_job_'.$candid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
					$jobids[] = $value;
				}
			}
		} else {
			foreach ($applids as $applid) {
				$chkids = explode('_', $applid);
				if ($chkids[1] == 'j')
					$jobids[] = $chkids[2];
				if ($chkids[1] == 'c')
					$clientids[] = $chkids[2];
			}
		}			
//dd($jobid);

		$success = $fail = null;
		$candidate = Candidate::with('user')->with('agencynotes')->with('documents')->find($candid);
		
//	dd($jobad);
		if (!empty($jobids))
			$jobads = JobAd::with('clientcontacts')->whereIn('id', $jobids)->orderBy('activated_at', 'DESC')->get();
		else {
			$jobads = collect([]);
		}	
$jobclients = $jobads->pluck('client_id')->toArray();		
//dd($jobads[0]->clientcontacts()->pluck('listname', 'id'));
		if (!empty($clientids))
			$clients = Client::whereIn('id', $clientids)->orderBy('name', 'ASC')->get();
		else {
			$clients = collect([]);
		}			
        
		$clientids = array_merge($clientids, $jobclients);
		
		if (($jobads->isEmpty()) && ($clients->isEmpty()))		
			$request->session()->flash('error_message', 'No Jobs\Clients Selected!');
	
		$allcontacts = ClientContact::whereIn('client_id', $clientids)->orderBy('client_id')->orderBy('lastname')->get(['client_id', 'id', 'firstname', 'lastname'])->groupBy('client_id'); //->pluck('listname', 'id');
		
		$allcontacts->append('listname');
		
		$allcontacts =  $allcontacts->map(function ($item) {
			return $item->pluck('listname', 'id');
		});
//	dd($allcontacts);
		$view = 'cand';
        return view('jobapplications.emailcvtomultipleclients',compact('candidate', 'jobads', 'clients', 'allcontacts', 'view', 'applids')); 
	
	}



	function emailCvToMultipleClients_store(Request $request, $candid) {

		$jobids = [];
		$clientids = [];
		$jobapplications = [];
		
		$attachids = $request['attachIds'];
		
		$applids = $request->input('applIds');

		$discardids = isset($request['discardIds']) ? $request['discardIds'] : [];
		
		if (!$applids) {
//			dd(\Session::all());
			if (\Session::has('search_job_'.$candid)) {
				foreach (\Session::get('search_job_'.$candid) as $key => $value) {
//				if ($this->RegisterInterest($jobid, $value))
//					$linked["Success"][] = $value;
//				else
//					$linked["Failed"][] = $value;
//				\Session::forget('search_job_'.$jobid.'.'.$key);
					$jobids[] = $value;
				}
			}
		} else {
			foreach ($applids as $applid) {
				$chkids = explode('_', $applid);
				$cl_id = $chkids[2];
				if ($chkids[1] == 'j')
					$jobids[] = $cl_id;
				if ($chkids[1] == 'c')
					$clientids[] = $cl_id;
				$jobapplications[$cl_id] = $chkids[0];

			}				
		}			

		$targetids = array_merge($jobids, $clientids);

		$targetids = array_diff($targetids, $discardids);
	
		$coverpage = [];
		foreach($targetids as $targetid) {
			$coverpage[$targetid] = $request->input('coverpage_'.$targetid);			
		}
		
//		$jobids = array_diff($jobids, $discardids);
		$request['coverpage'] = $coverpage;
		$request['jobids'] = $jobids;
		$request['clientids'] = $clientids;

		$request['selectedkeys'] =  array_flip($targetids);

if ($clientids)		
	$allcontacts = ClientContact::whereIn('client_id', $clientids)->get()->pluck('id', 'id');
else {
//	dd($request['contacts']);
	$allcontacts = JobAd::whereIn('id', $jobids)->with('clientcontacts')->get()->pluck('clientcontacts.id', 'clientcontacts.id');
}
		
//dd($request['contacts']);
/*		$this->validate($request, [
            'jobids' => 'required_if:clientids,empty',
            'contacts' => 'required|array|min:1',
            'subject.*' => 'required|min:1',
			'coverpage.*' => 'required|min:1'
        ], ['jobids.required_if'=> 'All jobs\clients have been discarded']);
*/

		$this->validate($request, [
            'jobids' => 'required_if:clientids,empty',
//            'contacts' => 'required|array|in:' . implode(',', $targetids).'|min:'.sizeof($targetids),
            'contacts' => 'required|array|min:'.sizeof($targetids),
            'subject.*' => 'required_with:selectedkeys.*',
			'coverpage.*' => 'required_with:selectedkeys.*'
        ], ['jobids.required_if'=> 'All jobs\clients have been discarded',
			'contacts.min'=>'Missing Contacts for selected emails',
			'subject.*.required_with'=>'Missing Subject for selected email',
			'coverpage.*.required_with'=>'Missing Message for selected email']);


$success = true;
		DB::beginTransaction();
		try {


		$candidate = Candidate::with('user')->with('emailaddresses')->with('documents')->find($candid);		
		
		list($from, $emailsig) = $this->getEmailSenderProperties(Auth::user());
		if (!empty($jobids)) {
			$jobads = JobAd::with('client')->with('clientcontacts')->whereIn('id', array_diff($jobids, $discardids))->orderBy('activated_at', 'DESC')->get();
			$targets = $jobads;
		} else {
			$jobads = collect([]);
		}	
$jobclients = $jobads->pluck('client_id')->toArray();		
//dd($jobads[0]->clientcontacts()->pluck('listname', 'id'));
		if (!empty($clientids)) {
			$clients = Client::whereIn('id', array_diff($clientids, $discardids))->orderBy('name', 'ASC')->get();
			$targets = $clients;
		} else {
			$clients = collect([]);
		}	

		$attachToSend = [];
		
		if ($attachids) {
			$attach = $candidate->documents->keyBy('id')->toArray();
			$attachToSend += array_filter($attach, function($key) use ($attachids) {
					return in_array($key, $attachids);
			}, ARRAY_FILTER_USE_KEY);
		}
		$sent_success = $sent_fail = [];
		
		foreach($targets as $target) {
			$data = [];
			$to = [];
			
			$contactids = [];
		$jobid = null;
		$clientid = null;
				
				
$class = get_class($target);
switch($class) {
    case 'App\Client':
			$clientname = $target->name;
			$clientid = $target->id;
        break;
    case 'App\JobAd':
			$clientname = $target->client->name." (referencing Job ".$target->jobref.")";
			$jobid = $target->id;
        break;
}				

			
			$contacts = ClientContact::with('emailaddresses')->find($request['contacts'][$target->id]);

		foreach($contacts as $contact) {
			$contactname = $contact->listname;
			$contactids[] = $contact->id;
			foreach ($contact->emailaddresses as $toAddress) {
				$to[] = ['email' => $toAddress->data, 'name' => $contactname];
			}
		}
			
			$data['messagebody'] = $request['coverpage_'.$target->id];

			if ($emailsig)
				$data['emailsig'] = $emailsig;
			if (!empty($attachToSend))
				$data['attachments'] = $attachToSend;

		if (!empty($attachToSend))
			$data['attachments'] = $attachToSend;

			$calendarProperties = [];
			$calendarProperties['title'] = 'Emailed CV to Client '.$clientname;
			$calendarProperties['type_id'] = 3;
			$calendarProperties['candidate'][] = $candidate->id;
			if ($jobid)
				$calendarProperties['job'][] = $jobid;
			if ($clientid)
				$calendarProperties['client'][] = $clientid;
			if (isset($jobapplications[$target->id]))
				$calendarProperties['jobapplication'][] = $jobapplications[$target->id];
			$calendarProperties['clientcontact'] = $contactids;
			$calendarProperties['files'] = $attachids;
// dd($data);		
			$data['subject'] = $request['subject'][$target->id];

			Mail::to($to)->send(new CandidateToClient($data, $calendarProperties, $from));
			
		$mailsent = (count(Mail::failures()) == 0);
		
	if ($mailsent)
	    $sent_success[] = 'CV was successfully emailed to '.$clientname;
	else
		 $sent_fail[] = 'Error emailing CV to '.$clientname;		
	 
	 if ($request->input('createlink')){
		if ($this->RegisterInterest($candid, $target->id, 'App\JobAd'))
			$sent_success[] = 'Candidate was successfully linked to '.$clientname;
		else
			$sent_fail[] = 'Error linking candidate to '.$clientname;		 
	}
	if ($mailsent)
		$this->ExpressInterest($candid, $target->id, $class);
		}
		if (!empty($sent_success))
			$request->session()->flash('success_message', $sent_success);
		if (!empty($sent_fail))
			$request->session()->flash('error_message', $sent_fail);

		} catch (\Exception $e) {
			dd($e);
			$success = false;
		}
		if ($success) {		
			DB::commit();
		return view('jobapplications.mailedcvs');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Error: Emails not sent');
		}			

	}

}
