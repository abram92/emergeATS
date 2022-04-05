<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;

use App\Candidate;
use App\JobAd;
use App\StaticWorkAlerts;

use App\Http\Traits\StaticWorkTrait;

class HomeController extends Controller
{
	
	use StaticWorkTrait;
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
		$this->initStaticWorkTrait();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		$user_id = Auth::user()->id;
		
		$arrLeads = $arrActiveJobs = $arrInprocessCandidates = $arrActiveCandidates = [];
		$staticalerts_count = $staticalerts_new = 0;		
		
//		dd(DB::select('select "users".*, (select count(*) from "audits" where "users"."id" = "audits"."user_id") as "audited_count" from "users" where "users"."deleted_at" is null order by "lastname" asc, "firstname" asc'));
//		dd(\App\User::find(32909)->withCount('audited')->toSql()); // => function ($query) {
//    $query->where('auditable_type', '\App\Client');
//	$query->where('auditable_type', '\App\JobAd');
	
//}])->get());
		if (Auth::user()->hasRole('Admin')) {
		}
		if (Auth::user()->hasRole('Consultant')) {
//			$this->getRemainingStaticJobs($user_id);
		if (!$this->shownStaticWorkAlertsToday($user_id)) {
			if (!Auth::isLoggedInByMasterPass())
			list($arrLeads, $arrActiveJobs, $arrInprocessCandidates, $arrActiveCandidates, $staticalerts_count, $staticalerts_new)  = $this->checkNewStaticWork($user_id);
		} else {
			list($arrLeads, $arrActiveJobs, $arrInprocessCandidates, $arrActiveCandidates, $staticalerts_count, $staticalerts_new)  = $this->getRemainingStaticAlerts($user_id);
		}
		}
		$mycandidates = Candidate::with('user')->where('status_id', 1)->where(function($q) use ($user_id) {
			$q->where('consultant_id', $user_id)
				->orWhereNull('consultant_id');
		})->orderBy('activated_at', 'DESC')->get();

		$myjobs = JobAd::with('client')->where('status_id', 1)->where('consultant_id', $user_id)->orderBy('activated_at', 'DESC')->get();

        return view('home', compact('arrLeads', 'arrActiveJobs', 'arrInprocessCandidates', 'arrActiveCandidates', 'staticalerts_count', 'staticalerts_new',
									'mycandidates', 'myjobs'));
    }
}
