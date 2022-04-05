<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\StaticWorkAlert;
use App\StaticWorkAlertCandidate;
use App\StaticWorkAlertJobAd;
use App\StaticWorkEmail;

use App\User;
use App\JobAd;
use App\Candidate;
use App\PublicHoliday;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Mail\StaticWorkEmailNotification;

use Carbon\Carbon;

trait StaticWorkTrait
{
		// status for each category
		private 	$statusActiveJob; // = [\Config::get('constants.static_status.job_active')];  // [2];
		private 	$statusHotLead; // = [\Config::get('constants.static_status.job_hot_lead')];  //[126];
		private 	$statusActiveCandidate; // = [\Config::get('constants.static_status.candidate_active')];  //[2];
		private 	$statusInProcessCandidate; // = [\Config::get('constants.static_status.candidate_inprocess')];  //[127];
		
		// Define number of days for each type
		private 	$hotLeadTriggerDays; // = \Config::get('constants.trigger_days.job_hot_lead'); //array('1'=>3, '2'=>5, '3'=>6);
		private 	$activeJobTriggerDays; // = \Config::get('constants.trigger_days.job_active'); //array('1'=>2, '2'=>4, '3'=>7);
		private 	$inProcessCandidateTriggerDays; // = \Config::get('constants.trigger_days.candidate_inprocess'); //array('1'=>2, '2'=>4, '3'=>6);
		private 	$activeCandidateTriggerDays; // = \Config::get('constants.trigger_days.candidate_active'); //array('1'=>2, '2'=>4, '3'=>6);
		

  public function initStaticWorkTrait() //__construct()
  {
		$this->statusActiveJob = [\Config::get('constants.static_status.job_active')];  // [2];
		$this->statusHotLead = [\Config::get('constants.static_status.job_hot_lead')];  //[126];
		$this->statusActiveCandidate = [\Config::get('constants.static_status.candidate_active')];  //[2];
		$this->statusInProcessCandidate = [\Config::get('constants.static_status.candidate_inprocess')];  //[127];
		
		// Define number of days for each type
		$this->hotLeadTriggerDays = \Config::get('constants.trigger_days.job_hot_lead'); //array('1'=>3, '2'=>5, '3'=>6);
		$this->activeJobTriggerDays = \Config::get('constants.trigger_days.job_active'); //array('1'=>2, '2'=>4, '3'=>7);
		$this->inProcessCandidateTriggerDays = \Config::get('constants.trigger_days.candidate_inprocess'); //array('1'=>2, '2'=>4, '3'=>6);
		$this->activeCandidateTriggerDays = \Config::get('constants.trigger_days.candidate_active'); //array('1'=>2, '2'=>4, '3'=>6);
  }

    /**
     * Check if the specified date is a work day.
     *
     * @return boolean
     */	
	private function isHoliday($dt, $recurringHolidays, $nonRecurringHolidays) {
		return ($recurringHolidays->contains(function($value, $key) use ($dt) {
								return substr($value->holiday_date, 5) == substr($dt, 5);
							}) 
						|| $nonRecurringHolidays->contains('holiday_date', $dt));
	}

    /**
     * Get the list of public holidays.
     *
     * @return array
     */
	public function getHolidays(){
        $result = PublicHoliday::orderBy('holiday_date','ASC')->get();
		
		list($recurring, $specific) = $result->partition(function($item){
			return $item->recurring;
		});
		
		$yearSpecific = $specific->groupBy(function($item, $key) {
			return substr($item->holiday_date, 0, 4);
		});
		return array($recurring, $specific);
	}
	
    /**
     * 
     *
     * @return 
     */
	public function getPreviousWorkDays($date, $number){
		list($year,$month,$day) = explode('-',$date);
		list($recurringHolidays, $nonRecurringHolidays) = $this->getHolidays();
		$dates    = array();
		$workdays = array(1, 2, 3, 4, 5);
		$today = date('Y-m-d',mktime(0,0,0,$month,$day,$year));
		$dow = date('w',strtotime($today));
	
		if ((in_array($dow, $workdays)) && !$this->isHoliday($today, $recurringHolidays, $nonRecurringHolidays))
			$dates[] = $date; // day 0 is a workday
		else
			$dates[] = '';	
		
		while(count($dates) <= $number){
			$newDate = date('Y-m-d',mktime(0,0,0,$month,--$day,$year));
		//	$dow = date('N',strtotime($newDate)); // ISO-8601 php 5.1
			$dow = date('w',strtotime($newDate)); 
			$weekday = in_array($dow, $workdays);

			if(($weekday) && !$this->isHoliday($newDate, $recurringHolidays, $nonRecurringHolidays)) {
				if(!($dow == 1 && $this->isHoliday(date('Y-m-d',mktime(0,0,0,$month,$day-1,$year)), $recurringHolidays, $nonRecurringHolidays))) // Current day is Monday and recurring day is Sunday
				$dates[] = $newDate;
			}
		}
	    return ($dates);
	}

    /**
     * 
     *
     * @return 
     */
	public function getTriggerDatesForList($workingDays, $triggerDays) {
		$arr = array();
		foreach ($triggerDays as $key => $numberOfDays) {
			$arr[$key] = $workingDays[$numberOfDays];
		}
		return $arr;
	}	


    /**
     * 
     *
     * @return 
     */
	public function shownStaticWorkAlertsToday($user_id) {

		$lastNotification = StaticWorkAlert::where('user_id', $user_id)->latest()->first();

		if (is_null($lastNotification))
			return false;
	
		$date = Carbon::parse($lastNotification->created_at);

		return $date->isToday();
	}


    /**
     * 
     *
     * @return 
     */
	public function getStaticJobs($userid, $statusids, $triggerdate, $mindate="") {

		// job not sent
		if (is_array($statusids)) {
			$isActiveState = in_array(2, $statusids);
		} else {
			$isActiveState = (2 == $statusids) ? true : false;
		}
	
		$res = JobAd::with('client')->where('consultant_id', $userid)->where('status_id', $statusids)->where('activated_at', '<', $triggerdate)
				->where('updated_at', '<', $triggerdate)
				->whereNotIn('id', DB::table('static_work_alert_job_ads')->join('static_work_alerts', 'id', '=', 'alert_id')
				->select('job_ad_id')->where('alert_level', '3')
//				->whereColumn('user_id', '=', 'job_ads.consultant_id')
				->whereColumn('created_at', '>', 'job_ads.activated_at'));
/*  ->whereNotIn('id', DB::table('static_work_alert_job_ads')->join('static_work_alerts', 'id', '=', 'alert_id')
			->select('candidate_id')->where('alert_level', '3')
		->whereColumn('created_at', '>', 'candidates.activated_at')->where('candidate_id', '=', 'candidate.id'));
		*/
/*				->whereDoesntHave('emails', function($query) {
	$query->whereColumn('job_ads.activated_at', '<', 'logged_emails.date')
	->where(function ($query) {
		$query->where('title', 'like', 'Emailed CV%');
	});
	
});
*/
//				  ->whereDoesntHave('calendar_events', function($q) use ($triggerdate) {
//				   $q->where('type_id', 7)->where('created_at', '>', $triggerdate);
//				   });

//				  ->whereDoesntHave('id', DB::table('curses')->select('id_user')->where('id_user', '=', $id)->get()->toArray());
//				  ->whereNotIn('id', DB::table('curses')->select('id_user')->where('id_user', '=', $id)->get()->toArray())
		
		if ($mindate)
			$res->where('activated_at', '>=', $mindate);
//						"and j.activeflag != 10 ". 
// " and j.id not in (select jobid from staticworkalertjobs swj, staticworkalerts sw where swj.alertid = sw.id and j.createdate < sw.timestamp and swj.alert_level = 3) ".
		if ($isActiveState)
			$res->whereDoesntHave('emails', function($query) {
						$query->whereColumn('job_ads.activated_at', '<', 'logged_emails.date')
							->where(function ($query) {
//								$query->where('type_id', 3);
								$query->where('title', 'like', 'Emailed CV%');
							});
					});

/* .=	" and not exists (select * from event e, evententitylink el2, evententitylink el1 ".
										" where e.createdate > j.createdate ".
										" and (el2.eventid = e.id and el2.entitytypeid=4 and el2.entityid=j.id) ".
										" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7 and e.description like 'Emailed CV%')) ";
*/		
		$res->orderBy('activated_at');		
// dd($res->toSql());
		return $res->get();
	}	

    /**
     * 
     *
     * @return 
     */
	public function getStaticCandidates($userid, $statusids, $triggerdate, $mindate="") {
// cand not sent
		if (is_array($statusids)) {
			$isActiveState = in_array(2, $statusids);
		} else {
			$isActiveState = (2 == $statusids) ? true : false;
		}
	
		$res = Candidate::with('user')->where('consultant_id', $userid)->where('status_id', $statusids)->where('activated_at', '<', $triggerdate)
				->where('updated_at', '<', $triggerdate)
				->whereNotIn('id', DB::table('static_work_alert_candidates')->join('static_work_alerts', 'id', '=', 'alert_id')
				->select('candidate_id')->where('alert_level', '3')
//				->whereColumn('user_id', '=', 'candidates.consultant_id')				
				->whereColumn('created_at', '>', 'candidates.activated_at'));
/*				->whereDoesntHave('staticworkalerts', function($query) {
	$query->whereColumn('job_ads.activated_at', '<', 'static_work_alerts.created_at')
		->where('alert_level', 3);
	});
*/	
//  ->whereDoesntHave('id', DB::table('static_work_alert_candidates')->select('candidate_id')->where('alert_level', '3')->where('created_at', '>', 'candidates.activated_at')->where('id_user', '=', $id)->get()->toArray());

// "and u.id not in (select candidateid from staticworkalertcandidates swc, staticworkalerts sw where swc.alertid = sw.id and cpe.createdate < sw.timestamp and swc.alert_level = 3) ".

		if ($mindate)
			$res->where('activated_at', '>=', $mindate);

		if ($isActiveState)
			$res->whereDoesntHave('emails', function($query) {
					$query->whereColumn('candidates.activated_at', '<', 'logged_emails.date')
					->where(function ($query) {
//								$query->whereIn('type_id', [2,3]);
						$query->where('title', 'like', 'Emailed CV%');
						$query->orWhere('title', 'like', 'Emailed Job Spec%');
					});
			});
			
		$res->orderBy('activated_at');		
// dd($res->toSql());
		return $res->get();
	}	


    /**
     * 
     *
     * @return 
     */
	public function getLastAlertsCandidates($userid) {
		
		$res = StaticWorkAlertCandidate::whereHas('staticworkalert', function($query) use ($userid) {
					$query->where('user_id', $userid)
						->latest()
						->limit(1);
				})
				->get();

		return $res;
	}

    /**
     * 
     *
     * @return 
     */
	public function getLastAlertsJobs($userid) {
		$res = StaticWorkAlertJobAd::whereHas('staticworkalert', function($query) use ($userid) {
					$query->where('user_id', $userid)
					->latest()
					->limit(1);
				})
				->get();

		return $res;
	}


    /**
     * 
     *
     * @return 
     */
	private function checkFinalAlert($alert_level, $ordinal, $triggerDays) {
		$final = true;
		if (isset($triggerDays[$alert_level]) && isset($triggerDays[$alert_level+1])) {
			$daysCurrentLevel = $triggerDays[$alert_level+1] - $triggerDays[$alert_level];
			$final = ($ordinal >= $daysCurrentLevel);
		}
		return $final;
	}


    /**
     * 
     *
     * @return 
     */
	private function getReminderStaticJobs($userid) {
			
/*		$res = JobAd::where('consultant_id', $userid)			
				->whereIn('id', DB::table('static_work_alert_job_ads')->join('static_work_alerts', 'id', '=', 'alert_id')
				->select('job_ad_id')->where('alert_level', '3')
				->whereColumn('created_at', '>', 'job_ads.updated_at')); */

		$res = JobAd::where('consultant_id', $userid)			
				->whereExists(function($q) { 
				      $q->select('job_ad_id')->from('static_work_alert_job_ads')->join('static_work_alerts', 'id', '=', 'alert_id')
					->where('alert_level', '3')
					->whereColumn('static_work_alert_job_ads.job_ad_id', 'job_ads.id')
					->whereColumn('created_at', '>', 'job_ads.updated_at');
				});
				
		$res->whereDoesntHave('emails', function($query) {
					$query->whereColumn('job_ads.activated_at', '<', 'logged_emails.date')
					->where(function ($query) {
//								$query->where('type_id', 3);
						$query->where('title', 'like', 'Emailed CV%');
					});
			});
		$res->orderBy('activated_at');		
//   dd($res->toSql());				
		return $res->get();
	}

    /**
     * 
     *
     * @return 
     */
	private function getReminderStaticCandidates($userid) {
		
/*		$res = Candidate::with('user')->where('consultant_id', $userid)
				->whereIn('id', DB::table('static_work_alert_candidates')->join('static_work_alerts', 'id', '=', 'alert_id')
				->select('candidate_id')->where('alert_level', '3')
				->whereColumn('created_at', '>', 'candidates.updated_at')); */

		$res = Candidate::with('user')->where('consultant_id', $userid)
				->whereExists(function($q) {
					$q->select('candidate_id')
					->from('static_work_alert_candidates')->join('static_work_alerts', 'id', '=', 'alert_id')
				->where('alert_level', '3')
				->whereColumn('static_work_alert_candidates.candidate_id', 'candidates.id')
				->whereColumn('created_at', '>', 'candidates.updated_at');
				});
				
		$res->whereDoesntHave('emails', function($query) {
				$query->whereColumn('candidates.activated_at', '<', 'logged_emails.date')
				->where(function ($query) {
//								$query->whereIn('type_id', [2,3]);					
					$query->where('title', 'like', 'Emailed CV%');
					$query->orWhere('title', 'like', 'Emailed Job Spec%');
				});
			});
		$res->orderBy('activated_at');		
		
		return $res->get();
	}


    /**
     * 
     *
     * @return 
     */
	private function sendEmailAssignStaticWork($user_id, $emailJobsHotleads, $emailJobsActive, 
														$emailCandidatesInProcess, $emailCandidatesActive, 
														$reminderJobs, $reminderCandidates) {

		$candidateR = [];
		$jobR = [];
		$data = [];
	
		if (!($emailJobsHotleads->isEmpty())) {
//		$jobs = JobAd::find($emailJobsHotleads);
			$data['jobsHotleads'] = $emailJobsHotleads;
			foreach ($emailJobsHotleads as $job){ 
				$jobR[$job->id] = ['status_id' => $job->status_id];
			}		
		}
   
		if (!($emailJobsActive->isEmpty())) {
//		$jobs = JobAd::find($emailJobsActive);
			$data['jobsActive'] = $emailJobsActive;
			foreach ($emailJobsActive as $job){ 
				$jobR[$job->id] = ['status_id' => $job->status_id];
			}		
		}

		if (!($emailCandidatesInProcess->isEmpty())) {
//		$candidates = Candidate::with('user')->find($emailCandidatesInProcess);
			$data['candidatesInprocess'] = $emailCandidatesInProcess;
			foreach ($emailCandidatesInProcess as $candidate){ 
				$candidateR[$candidate->id] = ['status_id' => $candidate->status_id];
			}		
		}
	
		if (!($emailCandidatesActive->isEmpty())) {
//		$candidates = Candidate::with('user')->find($emailCandidatesActive);
			$data['candidatesActive'] = $emailCandidatesActive;
			foreach ($emailCandidatesActive as $candidate){ 
				$candidateR[$candidate->id] = ['status_id' => $candidate->status_id];
			}		
		}

		if (!($reminderJobs->isEmpty())) {
//		$jobs = JobAd::find($reminderJobs);
			$data['jobsReminder'] = $reminderJobs;
			foreach ($reminderJobs as $job){ 
				$jobR[$job->id] = ['status_id' => $job->status_id, 'reminder'=>1];
			}		
		}
	
		if (!($reminderCandidates->isEmpty())) {
//		$candidates = Candidate::with('user')->find($reminderCandidates);
			$data['candidatesReminder'] = $reminderCandidates;
			foreach ($reminderCandidates as $candidate){ 
				$candidateR[$candidate->id] = ['status_id' => $candidate->status_id, 'reminder'=>1];
			}		
		}

		$recipients = User::with('emailaddresses')->whereHas("roles", function($q){ $q->where("name", "Static Work Admin"); })->get();
 
		if ($recipients->isEmpty())
			return false;

//	$recipients = array('numrows'=>1, 0=>array('id'=>0, 'emailaddress'=>'husseinb@worldonline.co.za'));

		$to = [];
		$recipientids = [];
		foreach ($recipients as $recipient) {
			$recipientname = $recipient->listname;
			$recipientids[] = $recipient->id;
			if (!$recipient->emailaddresses->isEmpty()) {
				foreach ($recipient->emailaddresses as $toAddress) {
					$to[] = ['email' => $toAddress->data, 'name' => $recipientname];
				}
			} else {
				if ($recipient->emailaddress)
					$to[] = ['email' => $recipient->emailaddress, 'name' => $recipientname];
			}
		}

		$consultant = User::find($user_id);
		$consultantName = $consultant->listname;
	
		$messagebody = "";

		$clientName = "";
		$candidateName = "";

		$prop = ['consultant_id'=>$user_id];
		
		$cal = StaticWorkEmail::create($prop);
		if ($cal) {
//			dd($cal->recipients());
			if (isset($recipientids) && is_array($recipientids) && !empty($recipientids))
				$cal->recipients()->sync($recipientids);

			if (isset($candidateR) && is_array($candidateR) && !empty($candidateR))
				$cal->candidates()->sync($candidateR);

			if (isset($jobR) && is_array($jobR) && !empty($jobR))
				$cal->jobs()->sync($jobR);

		}
   
   
//   $mail->headers = "Errors-To: foo@bar.com";
   

		$messagebody = "The consultant has NOT serviced the work there for you need to delegate the work to some one else.\r\n ";

		$subject = date('Y-m-d')." Static Work for Consultant : ".$consultantName;
		$data['messagebody'] = $messagebody;
		$data['subject'] = $subject; 
			
		$calendarProperties = [];
		$calendarProperties['title'] = $subject;
		$calendarProperties['user'][] = $user_id;
			
		Mail::to($to)->send(new StaticWorkEmailNotification($data, $calendarProperties));

		$mailsent = (count(Mail::failures()) == 0);

		return ($mailsent);

	}


    /**
     * 
     *
     * @return 
     */
	private function getRemainingStaticJobs($user_id) {
/*	$SQLstr = "select j.id, c.name as companyname, j.jobref, js.description as jobstatus, j.jobtitletxt as jobtitle, j.createdate, 
	convert(varchar(12), j.createdate, 106) as uploaddate, j.activeflag, a.level_ordinal, a.alert_level ".
				"from joblisting j, jobstatus js, clntinfo c, staticworkalertjobs a, staticworkalerts w where j.id = a.jobid 
				and w.id = a.alertid and w.userid = $userid and j.activeflag = js.id and j.activeflag=a.statusid ".
						"and j.clientid=c.id and w.timestamp > cast (GETDATE() as DATE) and not exists 
						(select * from jobaudit where jobid = a.jobid and timestamp > w.timestamp) 
						and not exists (select * from event e, evententitylink el2, evententitylink el1 ".
		" where e.createdate >=  w.timestamp ".
		" and (el2.eventid = e.id and el2.entitytypeid=4 and el2.entityid=j.id) ".
		" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7 and e.description like 'Emailed CV%'))";
*/   
		$res = JobAd::with('client')
				->join('static_work_alert_job_ads', 'job_ads.id', '=', 'job_ad_id')->join('static_work_alerts', 'static_work_alerts.id', '=', 'alert_id')
				->whereColumn('job_ads.updated_at', '<', 'static_work_alerts.created_at')
				->where('static_work_alerts.user_id', $user_id)
				->whereDate('static_work_alerts.created_at', '=', Carbon::today()->toDateString())
				->addSelect(['job_ads.*', 'job_ad_id', 'alert_level', 'level_ordinal'])
				->whereColumn('static_work_alerts.created_at', '>', 'job_ads.activated_at');
		
			$res->whereDoesntHave('emails', function($query) {
						$query->whereColumn('static_work_alerts.created_at', '<', 'logged_emails.date')
							->where(function ($query) {
//								$query->where('type_id', 3);
								$query->where('title', 'like', 'Emailed CV%');
							});
					});

		$res->orderBy('job_ads.activated_at');		

		return $res->get();
	}

	    /**
     * 
     *
     * @return 
     */
	private function getRemainingStaticCandidates($user_id) {
/*   $SQLstr = "select u.id, u.username, u.email, u.firstname + ' ' + u.lastname fullname, cp.jobrole, jt.description as jobtitle, cs.description as status, ".
            "convert(varchar(12), cpe.createdate, 106) as uploaddate, cpe.createdate, u.activeflag, a.level_ordinal, a.alert_level ".
				"from vuser u, candstatus cs, cndprofinfo cp, cndpersinfo cpe, jobtitle jt, staticworkalertcandidates a, staticworkalerts w ".
					"where cp.id = u.id and u.activeflag=a.statusid ". 
						"and cpe.id = u.id  ". 
						"and cp.jobrole = jt.id and w.id = a.alertid and w.userid = $userid and w.timestamp > cast (GETDATE() as DATE) and not exists (select * from candidateaudit where candidateid = a.candidateid and timestamp > w.timestamp) and not exists (select * from ".
									" evententitylink el1, event e ".
									" where e.createdate >= w.timestamp ".
									" and (el1.entityid=a.candidateid) ".
									" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7) and (e.description like 'Emailed Job Spec%' or e.description like 'Emailed CV%'))";
*/   
		$res = Candidate::with('user')
				->join('static_work_alert_candidates', 'candidates.id', '=', 'candidate_id')->join('static_work_alerts', 'static_work_alerts.id', '=', 'alert_id')
				->whereColumn('candidates.updated_at', '<', 'static_work_alerts.created_at')
				->where('static_work_alerts.user_id', $user_id)
				->whereDate('static_work_alerts.created_at', '=', Carbon::today()->toDateString())
				->addSelect(['candidates.*', 'candidate_id', 'alert_level', 'level_ordinal'])
				->whereColumn('static_work_alerts.created_at', '>', 'candidates.activated_at');

			$res->whereDoesntHave('emails', function($query) {
					$query->whereColumn('candidates.activated_at', '<', 'logged_emails.date')
					->where(function ($query) {
//								$query->whereIn('type_id', [2,3]);						
						$query->where('title', 'like', 'Emailed CV%');
						$query->orWhere('title', 'like', 'Emailed Job Spec%');
					});
			});
			
		$res->orderBy('activated_at');		

		return $res->get();

	}




    /**
     * 
     *
     * @return 
     */

	public function jobCheckSentEmailsAfterReassignTrigger($jobid) {

/*		$isRole = DB_GetFieldValue("select notify_delegatework as description from vuser where id=$UserID");
	
	if (!$isRole)
		return array(0,0);
	
	$alertDate = DB_GetFieldValue("select max(timestamp) as description from staticworkalerts a, staticworkalertjobs aj, joblisting j where aj.alert_level=3 and a.id = aj.alertid and aj.jobid=$jobid and aj.jobid=j.id and timestamp > j.createdate");
	
	if (is_null($alertDate) || (!alertDate) || ($alertDate == '-'))
		return array(0,0);
		
	$toClient = DB_GetFieldValue("select count(*) as description from event e, evententitylink el2, evententitylink el1 ".
		" where e.createdate >=  '$alertDate' ".
		" and (el2.eventid = e.id and el2.entitytypeid=4 and el2.entityid=$jobid) ".
		" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7 and e.description like 'Emailed CV%')");

	$toCandidate = DB_GetFieldValue("select count(*) as description from event e, evententitylink el2, evententitylink el1 ".
		" where e.createdate >=  '$alertDate' ".
		" and (el2.eventid = e.id and el2.entitytypeid=4 and el2.entityid=$jobid) ".
		" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7 and e.description like 'Emailed Job Spec%')");
		
    return array($toClient, $toCandidate);  */
	$dt = DB::table('static_work_alert_job_ads')->join('static_work_alerts', 'static_work_alerts.id', '=', 'alert_id')
	->join('job_ads', 'job_ads.id', '=', 'job_ad_id')
				->where('alert_level', '3')
				->whereColumn('static_work_alerts.created_at', '>', 'job_ads.updated_at')
				->where('job_ads.id', $jobid)->max('static_work_alerts.created_at');
				
//				dd($dt);
		if (is_null($dt))
				return ['toClient'=>0, 'toCandidate'=>0];	
		$res = JobAd::withCount(['emails as to_client' => function($query) use ($dt) {
						$query->whereDate('logged_emails.date', '>=', $dt);
//							->where(function ($query) {
//								$query->where('type_id', 3);	
								$query->where('title', 'like', 'Emailed CV%');
//							});
					},
					'emails as to_candidate' => function($query)  use ($dt) {
						$query->whereDate('logged_emails.date', '>=', $dt);
//							->where(function ($query) {
//								$query->where('type_id', 2);	
								$query->where('title', 'like', 'Emailed Job Spec%');
//							});
					}
					])->find($jobid);
		if (is_null($res))
			return ['toClient'=>0, 'toCandidate'=>0];
		
		return ['toClient'=>$res->to_client, 'toCandidate'=>$res->to_candidate];
	}


    /**
     * 
     *
     * @return 
     */

	public function candCheckSentEmailsAfterReassignTrigger($candid) {

/*    $isRole = DB_GetFieldValue("select notify_delegatework as description from vuser where id=$UserID");
	
	if (!$isRole)
		return array(0,0);
	
	$alertDate = DB_GetFieldValue("select max(timestamp) as description from staticworkalerts a, staticworkalertcandidates aj, cndpersinfo j where aj.alert_level=3 and a.id = aj.alertid and aj.candidateid=$candid and aj.candidateid=j.id and timestamp > j.createdate");
	
	if (is_null($alertDate) || (!alertDate) || ($alertDate == '-'))
		return array(0,0);
		
	$toClient = DB_GetFieldValue("select count(*) as description from ".
									" evententitylink el1, event e ".
									" where e.createdate > '$alertDate' and (el1.entityid = $candid)".
									" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7) and (e.description like 'Emailed CV%')");

	$toCandidate = DB_GetFieldValue("select count(*) as description from ".
									" evententitylink el1, event e ".
									" where e.createdate > '$alertDate' and (el1.entityid = $candid)".
									" and (el1.eventid = e.id and el1.entitytypeid=1 and e.typeid=7) and (e.description like 'Emailed Job Spec%')");
		
		
		
    return array($toClient, $toCandidate); */
	
	$dt = DB::table('static_work_alert_candidates')->join('static_work_alerts', 'static_work_alerts.id', '=', 'alert_id')
	->join('candidates', 'candidates.id', '=', 'candidate_id')
				->where('alert_level', '3')
				->whereColumn('static_work_alerts.created_at', '>', 'candidates.updated_at')
				->where('candidates.id', $candid)->max('static_work_alerts.created_at');
				
//				dd($dt);
		if (is_null($dt))
			return ['toClient'=>0, 'toCandidate'=>0];	
		$res = Candidate::withCount(['emails as to_client' => function($query) use ($dt) {
						$query->whereDate('logged_emails.date', '>=', $dt);
//							->where(function ($query) {
//								$query->where('type_id', 3);	
								$query->where('title', 'like', 'Emailed CV%');
//							});
					},
					'emails as to_candidate' => function($query)  use ($dt) {
						$query->whereDate('logged_emails.date', '>=', $dt);
//							->where(function ($query) {
//								$query->where('type_id', 2);	
								$query->where('title', 'like', 'Emailed Job Spec%');
//							});
					}
					])->find($candid);
		if (is_null($res))
			return ['toClient'=>0, 'toCandidate'=>0];	
		return ['toClient'=>$res->to_client, 'toCandidate'=>$res->to_candidate];
	
}



    /**
     * 
     *
     * @return 
     */

	public function checkNewStaticWork($user_id) {
		
		$previousWorkDays = $this->getPreviousWorkDays(date('Y-m-d'), 7);
	
		$triggerStart = '2019-11-02';
	
		$hotLeadTriggerDates = $this->getTriggerDatesForList($previousWorkDays, $this->hotLeadTriggerDays);
		$activeJobTriggerDates = $this->getTriggerDatesForList($previousWorkDays, $this->activeJobTriggerDays);
		$inProcessCandidateTriggerDates = $this->getTriggerDatesForList($previousWorkDays, $this->inProcessCandidateTriggerDays);
		$activeCandidateTriggerDates = $this->getTriggerDatesForList($previousWorkDays, $this->activeCandidateTriggerDays);

		$isWorkDay = (strlen($previousWorkDays[0]) > 0 ) ? true : false;

		$hotLeads = collect([]);
		$activeJobs = collect([]);
		$inprocessCandidates = collect([]);
		$activeCandidates = collect([]);
		
		$hotLeads = $this->getStaticJobs($user_id, $this->statusHotLead, $hotLeadTriggerDates[1], $triggerStart);
		$activeJobs = $this->getStaticJobs($user_id, $this->statusActiveJob, $activeJobTriggerDates[1], $triggerStart);
		$inprocessCandidates = $this->getStaticCandidates($user_id, $this->statusInProcessCandidate, $inProcessCandidateTriggerDates[1], $triggerStart);
		$activeCandidates = $this->getStaticCandidates($user_id, $this->statusActiveCandidate, $activeCandidateTriggerDates[1], $triggerStart);

		$count = $hotLeads->count() + $activeJobs->count() + $inprocessCandidates->count() + $activeCandidates->count();
		$reminderJobs = collect([]);
		$reminderCandidates = collect([]);
		if ($isWorkDay) {
			$reminderJobs = $this->getReminderStaticJobs($user_id);
			$reminderCandidates = $this->getReminderStaticCandidates($user_id);
		}

		if (($hotLeads->isEmpty()) && ($activeJobs->isEmpty()) && ($inprocessCandidates->isEmpty()) && ($activeCandidates->isEmpty())) {

			$prop = ['user_id'=>$user_id];
			$cal = StaticWorkAlert::create($prop);
			
			if ((!$reminderJobs->isEmpty()) || (!$reminderCandidates->isEmpty()))
				$this->sendEmailAssignStaticWork($user_id, $hotLeads, $activeJobs, $inprocessCandidates, $activeCandidates, $reminderJobs, $reminderCandidates);		 
return [[],[],[],[], 0, false];
		}
				
		$lastSentAlertsJobs = $this->getLastAlertsJobs($user_id);
		$lastSentAlertsCandidates = $this->getLastAlertsCandidates($user_id);
		
		$lastLevelHotLead = $lastSentAlertsJobs->filter(function($item) { // use ($this->statusHotLead) {
				return in_array($item->status_id, $this->statusHotLead);
			})->keyBy('job_ad_id')->toArray();
		$lastLevelJobActive = $lastSentAlertsJobs->filter(function($item) { // use ($this->statusActiveJob) {
				return in_array($item->status_id, $this->statusActiveJob);
			})->keyBy('job_ad_id')->toArray();
		$lastLevelCandidateInProcess = $lastSentAlertsCandidates->filter(function($item) { // use ($this->statusInProcessCandidate) {
				return in_array($item->status_id, $this->statusInProcessCandidate);
			})->keyBy('candidate_id')->toArray();
		$lastLevelCandidateActive = $lastSentAlertsCandidates->filter(function($item) { // use ($this->statusActiveCandidate) {
				return in_array($item->status_id, $this->statusActiveCandidate);
			})->keyBy('candidate_id')->toArray();
	

		$emailJobsHotleads = collect([]);
		$emailJobsActive = collect([]);
		$emailCandidatesInProcess = collect([]);
		$emailCandidatesActive = collect([]);

		$arrLeads = $this->calcAlertLevel($isWorkDay, $hotLeads, $lastLevelHotLead, $this->hotLeadTriggerDays, $emailJobsHotleads);

		$arrActiveJobs = $this->calcAlertLevel($isWorkDay, $activeJobs, $lastLevelJobActive, $this->activeJobTriggerDays, $emailJobsActive);

		$arrInprocessCandidates = $this->calcAlertLevel($isWorkDay, $inprocessCandidates, $lastLevelCandidateInProcess, $this->inProcessCandidateTriggerDays, $emailCandidatesInProcess);

		$arrActiveCandidates = $this->calcAlertLevel($isWorkDay, $activeCandidates, $lastLevelCandidateActive, $this->activeCandidateTriggerDays, $emailCandidatesActive);


		$jobR = [];
		$candR = [];


		if (!empty($arrLeads)) {
			foreach ($arrLeads as $levelObjs){ 
				foreach($levelObjs as $job){
					$model = $job['model'];
					$jobR[$model->id] = ['status_id' => $model->status_id, 'alert_level'=>$job['alert_level'], 'level_ordinal'=>$job['level_ordinal']];
				}		
			}
		}
   
		if (!empty($arrActiveJobs)) {
			foreach ($arrActiveJobs as $levelObjs){ 
				foreach($levelObjs as $job){ 
					$model = $job['model'];
					$jobR[$model->id] = ['status_id' => $model->status_id, 'alert_level'=>$job['alert_level'], 'level_ordinal'=>$job['level_ordinal']];
				}
			}		
		}

		if (!empty($arrInprocessCandidates)) {
			foreach ($arrInprocessCandidates as $levelObjs){ 
				foreach($levelObjs as $candidate){ 
					$model = $candidate['model'];
					$candidateR[$model->id] = ['status_id' => $model->status_id, 'alert_level'=>$candidate['alert_level'], 'level_ordinal'=>$candidate['level_ordinal']];
				}
			}
		}
	
		if (!empty($arrActiveCandidates)) {
			foreach ($arrActiveCandidates as $levelObjs){ 
				foreach($levelObjs as $candidate){ 
					$model = $candidate['model'];
					$candidateR[$model->id] = ['status_id' => $model->status_id, 'alert_level'=>$candidate['alert_level'], 'level_ordinal'=>$candidate['level_ordinal']];
				}
			}
		}

		$prop = ['user_id'=>$user_id];
		
		$cal = StaticWorkAlert::create($prop);
		if ($cal) {

			if (isset($candidateR) && is_array($candidateR) && !empty($candidateR))
				$cal->candidates()->sync($candidateR);

			if (isset($jobR) && is_array($jobR) && !empty($jobR))
				$cal->jobs()->sync($jobR);

		}
		 
		if 	(($isWorkDay) && (!($emailJobsHotleads->isEmpty()) || !($emailJobsActive->isEmpty()) || !($emailCandidatesInProcess->isEmpty()) || !($emailCandidatesActive->isEmpty()) 
					|| (!($reminderJobs->isEmpty())) || (!($reminderCandidates->isEmpty()))))
			$this->sendEmailAssignStaticWork($user_id, $emailJobsHotleads, $emailJobsActive, $emailCandidatesInProcess, $emailCandidatesActive, $reminderJobs, $reminderCandidates);
		
//		return view('staticwork.swlist', compact('arrLeads', 'arrActiveJobs', 'arrInprocessCandidates', 'arrActiveCandidates'));
		return [$arrLeads, $arrActiveJobs, $arrInprocessCandidates, $arrActiveCandidates, $count, true];
	}
	
	
	private function calcAlertLevel($isWorkDay, $alertCollection, $lastLevels, $triggerDays, &$emailObj) {
		$res = array();
		foreach ($alertCollection as $alertInst) {

			$row_id = $alertInst->id;

			$dt = date('Y-m-d', strtotime($alertInst->created_at));

/*		$alert1sent = $alert2sent = $alert3sent = false;
		if (isset($lastLevels[$row_id])) {
			$alert1sent = true;
			if ($lastLevels[$row_id] >= 2)
				$alert2sent = true;
			if ($lastLevels[$row_id] >= 3)
				$alert3sent = true;				
		}
						
		if (($dt <= $triggerDates[3]) && $alert2sent)
			$alertlevel = 3;
		elseif (($dt <= $triggerDates[2])) // || ((($dt >= $triggerDates[3]) && !$alert2sent)))
			$alertlevel = 2;
		else
			$alertlevel = 1;
*/
			$alertlevel = $ordinal = 0;
			if (isset($lastLevels[$row_id])) {
				$alertlevel = $lastLevels[$row_id]['alert_level'];
				$ordinal = $lastLevels[$row_id]['level_ordinal'];			
			}
			if ($isWorkDay) {
				if (!$alertlevel) {
					$alertlevel = $ordinal = 1;
				} else {
					if (isset($triggerDays[$alertlevel+1]) && isset($triggerDays[$alertlevel+1])) {
						$daysCurrentLevel = $triggerDays[$alertlevel+1] - $triggerDays[$alertlevel];
						if ($ordinal < $daysCurrentLevel) {
							$ordinal++;
						} else {
							$alertlevel++;
							$ordinal = 1;
						}
					}
				}
			}
			if (!$alertlevel)
				$alertlevel = 1;		
		

			$finalAlert = $this->checkFinalAlert($alertlevel, $ordinal, $triggerDays);
		// Retrieve cell list_jobref_0
	   		
			$res[$alertlevel][] = ['model'=>$alertInst, 'is_final'=>$finalAlert, 'alert_level'=>$alertlevel, 'level_ordinal'=>$ordinal];
		
			if ($alertlevel == 3)
				$emailObj->put($row_id, $alertInst);

		}
		return $res;
	}

	public function getRemainingStaticAlerts($user_id) {

		$remainingJobs = $this->getRemainingStaticJobs($user_id);
		$remainingCandidates = $this->getRemainingStaticCandidates($user_id);
		
		$arrLeads = $this->sortRemainingAlerts($remainingJobs, $this->statusHotLead, $this->hotLeadTriggerDays);

		$arrActiveJobs = $this->sortRemainingAlerts($remainingJobs, $this->statusActiveJob, $this->activeJobTriggerDays);
		$arrInprocessCandidates = $this->sortRemainingAlerts($remainingCandidates, $this->statusInProcessCandidate, $this->inProcessCandidateTriggerDays);
		$arrActiveCandidates = $this->sortRemainingAlerts($remainingCandidates, $this->statusActiveCandidate, $this->activeCandidateTriggerDays);
		

		$count = $remainingJobs->count() + $remainingCandidates->count();
		return [$arrLeads, $arrActiveJobs, $arrInprocessCandidates, $arrActiveCandidates, $count, false];
	}
		
	private function sortRemainingAlerts($alertCollection, $status_ids, $triggerDays) {
		$res = array();
	//	dd($status_ids);
		
		foreach ($alertCollection as $alertInst) {

	   		if (!in_array($alertInst->status_id, $status_ids))
				continue;
			$row_id = $alertInst->id;

			$dt = date('Y-m-d', strtotime($alertInst->created_at));

			$alertlevel = $alertInst->alert_level;
			$ordinal = $alertInst->level_ordinal;
		

			$finalAlert = $this->checkFinalAlert($alertlevel, $ordinal, $triggerDays);

			$res[$alertlevel][] = ['model'=>$alertInst, 'is_final'=>$finalAlert, 'alert_level'=>$alertlevel, 'level_ordinal'=>$ordinal];
		
		}
		return $res;
	}
		
	
}
