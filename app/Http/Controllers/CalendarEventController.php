<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect,Response;

use App\CalendarEvent;
use App\PublicHoliday;
use App\EventType;
use Auth;
use DB;

use App\Candidate;
use App\User;
use App\Client;
use App\JobAd;

use App\Http\Traits\LookupListTrait;
use App\Http\Traits\DataFileTrait;
use App\Http\Traits\CalendarEventTrait;

class CalendarEventController extends Controller
{
	use LookupListTrait;
	use DataFileTrait;
	use CalendarEventTrait;
	
    public function __construct()
	{
		$this->model_class = 'App\CalendarEvent';
	}	

    public function index(Request $request)
    {
		$events = [];

        $alleventtypes = $this->getDraggableEventTypes();
		if (Auth::user()->hasRole('Admin')) {
			$allconsultants = $this->getConsultants();
		} else {
			$allconsultants = $this->getTeamMembers(null, Auth::user()->id);
		}

        return view('calendarevents.fullcalendar', compact('alleventtypes', 'allconsultants'));
    }
	
	public function eventfeed(Request $request)
    {
		$current_user_id  = Auth::user()->id;
        $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
        $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
		$user_ids = [$current_user_id];
		$usertags = [];
		if (Auth::user()->hasRole('Admin')) {
			if (!empty($_GET["consultant_ids"]))
				$user_ids = explode(',', urldecode($_GET["consultant_ids"]));
			  $usertags = $this->getUserInitials($user_ids)->toArray();
		} else {
			if (!empty($_GET["consultant_ids"])) {
				$allconsultants = $this->getTeamMembers(null, Auth::user()->id); //->pluck('id', 'id');
				$user_ids = array_intersect(explode(',', urldecode($_GET["consultant_ids"])), $allconsultants->keys()->toArray());
				
				if(!empty($user_ids))
					$usertags = $this->getUserInitials($user_ids)->toArray();
			}
		}
        $eventtype_ids = (!empty($_GET["event_type_ids"])) ? explode(',', urldecode($_GET["event_type_ids"])) : [];
        $title = (!empty($_GET["title"])) ? $_GET["title"] : '';

        $candidate_ids = (!empty($_GET["cand_ids"])) ? explode(',', urldecode($_GET["cand_ids"])) : [];
        $job_ids = (!empty($_GET["job_ids"])) ? explode(',', urldecode($_GET["job_ids"])) : [];
        $client_ids = (!empty($_GET["client_ids"])) ? explode(',', urldecode($_GET["client_ids"])) : [];
		
		$data = CalendarEvent::select(['calendar_events.id','title', 'user_id', 'created_user_id', 'type_id', 'time_start as start', 'time_end as end', 'color' => EventType::select('colour_hex')
					->whereColumn('calendar_events.type_id', 'id')
				])->whereDate('time_start', '>=', $start)->whereDate('time_end',   '<=', $end)
				->when($title, function($query) use ($title) {
						return $query->where('title', 'ilike',  '%'.$title.'%');
				})
				->when(!empty($eventtype_ids), function($query) use ($eventtype_ids) {
						return $query->whereIn('type_id', $eventtype_ids);
				})
				->when(!empty($user_ids), function($query) use ($user_ids) {
						return $query->whereIn('user_id', $user_ids);
				})
				->when(!empty($candidate_ids), function($query) use ($candidate_ids) {
						return $query->whereHas("candidates", function ($q2) use ($candidate_ids) {
									$q2->whereIn('entityable_id', $candidate_ids); 
						});
				})
				->when(!empty($job_ids), function($query) use ($job_ids) {
						return $query->whereHas("jobs", function ($q2) use ($job_ids) {
									$q2->whereIn('entityable_id', $job_ids); 
						});				})
				->when(!empty($client_ids), function($query) use ($client_ids) {
						return $query->whereHas("clients", function ($q2) use ($client_ids) {
									$q2->whereIn('entityable_id', $client_ids); 
						});				})
				->with(['clients:id,name as text'])->with(['candidates:id,jobtitle_text as text'])
				->with(['jobs:id,jobref as text'])
				->get(['id','title', 'editable', 'start', 'end', 'color']);

		foreach ($data as $event) {		

			if ($current_user_id != $event->user_id) {
//				$event->setEditableAttribute(false);
				$event->usertag = isset($usertags[$event->user_id]) ? $usertags[$event->user_id] : '--';
				$event->usercol = (array_search($event->user_id, $user_ids) % 10);
			}
//dd($event);			
			foreach ($event->candidates as $candidate) {
				$candidate->user->append('listname');
				$candidate->text = $candidate->user->listname;				
			}
			$event->candidates->makeHidden(['user', 'pivot']);

/*foreach ($event->candidates as $candidate) {
$candidate->user->append('listname');
			$candidate->map(function ($cand) {
				$cand['text'] = $cand->user->listname;				
				return $cand;
			});
			
			$candidate->makeHidden(['user', 'pivot']);
}*/
			$event->clients->makeHidden(['pivot']);
			$event->jobs->makeHidden(['client', 'pivot']);
		}
		$data->toArray();
// dd($data);
		$holidays = PublicHoliday::where('recurring', '1')->orWhere(function ($query) use ($start, $end) {
					$query->whereDate('holiday_date', '>=', $start)->whereDate('holiday_date',   '<=', $end);
				})->get();
		foreach($holidays as $holiday) {
			if($holiday['recurring'] == 1) {
				$start_year = substr($start, 0, 4);
				$data[] = ['title'=>$holiday['description'], 'start'=>$start_year.substr($holiday['holiday_date'], 4), 'holiday'=>1, 'display'=>'background'];
				$end_year = substr($end, 0, 4);
				if ($end_year != $start_year)
					$data[] = ['title'=>$holiday['description'], 'start'=>$end_year.substr($holiday['holiday_date'], 4), 'holiday'=>1, 'display'=>'background'];
			} else
				$data[] = ['title'=>$holiday['description'], 'start'=>$holiday['holiday_date'], 'holiday'=>1, 'display'=>'background'];
		}
        return Response::json($data);
    }

		
	public function createEvent(Request $request){
		
        $alleventtypes = $this->getDraggableEventTypes();
		$allconsultants = $this->getConsultants();
		
		$candidatelist = [];
		$joblist = [];
		$clientlist = [];
		$clientid = $request->get('client_id');
		$candidateid = $request->get('candidate_id');
		$jobid = $request->get('job_id');
		
		if ($clientid)
		   $clientlist = Client::where('id', $clientid)->get()->pluck('name', 'id');
		if ($candidateid)
		   $candidatelist = User::where('userable_type', 'App\Candidate')->where('id', $candidateid)->orderBy('firstname')->orderBy('lastname')->get()->pluck('listname', 'id');
		if ($jobid)
		   $joblist = JobAd::where('id', $jobid)->get()->pluck('jobref', 'id');	   
		return view('calendarevents.createevent', compact('alleventtypes', 'allconsultants', 'candidatelist', 'joblist', 'clientlist'));
	}		
	
	public function store(Request $request){
//		$event = new Event();
//		$event->title=$request->get('title');
//        $event->start_date=$request->get('startdate');
//        $event->end_date=$request->get('enddate');
//        $event->save();
//        return redirect('event')->with('success_message', 'Event added successfully');

       //
//	   dd($request);
        $this->validate($request, [
            'title' => 'required',
			'type_id' => 'required|exists:event_types,id',
            'start_date' => 'required|date',
			'start_time' =>'date_format:H:i|nullable',
            'end_date' => 'date|after_or_equal:time_start|nullable',		
			'end_time' => 'date_format:H:i|nullable',
        ], ['type_id.required'=>'The type field is required']);

        $input = $request->all();

		$auth_user_id = Auth::user()->id;
		$input['user_id'] = $auth_user_id;
		$input['created_user_id'] = $auth_user_id;
//		$input['jobref'] .= $input['nextjobnumber'];
//        $input['activated_at'] = date('Y-m-d H:i:s');
		$input['time_start'] = ($request->start_date) ? $request->start_date.' '.(($request->start_time) ? $request->start_time : '') : null;
		$input['time_end'] = ($request->end_date) ? $request->end_date.' '.(($request->end_time) ? $request->end_time : '') : $input['time_start'];
		$input['comments'] = $request->comments;
		
		$entities = [];
		$entities['candidate'] = (isset($request->candidatelist)) ? (is_array($request->candidatelist) ? $request->candidatelist : explode(',',$request->candidatelist)) : null;
		$entities['client'] = (isset($request->clientlist)) ? (is_array($request->clientlist) ? $request->clientlist : explode(',',$request->clientlist)) : null;
		$entities['job'] = (isset($request->joblist)) ? (is_array($request->joblist) ? $request->joblist : explode(',',$request->joblist)) : null;

		$success = false;
		DB::beginTransaction();
		try {
		
			$event = CalendarEvent::create($input);

			if ($event) {
				$this->saveCalendarEventEntities($event, $entities);
				$success = true;
			}
		} catch (\Exception $e) {
			
			dd($e);
		}
		if ($success) {		
			DB::commit();
			if($request->ajax()){
                return Response::json(['success' => true, 'insert_id' => $event->id], 200);
            }
			return redirect()->route('calendarevents.index')
                   ->with('success_message','Event created successfully')->with('calInitialDate', $request->start_date);
		} else {
			DB::rollback();
			if($request->ajax()){
				return Response::json(['errors'=>['Event not created']]);
			}
			return redirect()->back()
                        ->with('error_message','Event not created');
		}


	}

    public function show($id)
    {
        //
        $event = CalendarEvent::with('clientcontacts')->with('jobs')->with('candidates')->with('emails')->find($id);
        return view('calendarevents.show',compact('event'));
		
    }


    public function create(Request $request)
    {  
        $insertArr = [ 'title' => $request->title,
                       'time_start' => $request->start,
                       'time_end' => $request->end
                    ];
        $event = CalendarEvent::insert($insertArr);   
        return Response::json($event);
    }
     
 
    public function update(Request $request)
    {   
		$id = $request->id;
		if (!$id)
			return Response::json(false);
//        $where = array('id' => $request->id);

        $this->validate($request, [
            'title' => 'required',
			'type_id' => 'required|exists:event_types,id',
            'start_date' => 'required|date',
			'start_time' =>'date_format:H:i|nullable',
            'end_date' => 'date|after_or_equal:time_start|nullable',		
			'end_time' => 'date_format:H:i|nullable',			
        ], ['type_id.required'=>'The type field is required']);
		
		$updateArr = [];
		if (isset($request->title))
			$updateArr['title'] = $request->title;
		if (isset($request->type_id))
			$updateArr['type_id'] = $request->type_id;
//		if (isset($request->time_start))
//			$updateArr['time_start'] = $request->time_start;
//		if (isset($request->time_end))
//			$updateArr['time_end'] = $request->time_end;
		$updateArr['time_start'] = ($request->start_date) ? $request->start_date.' '.(($request->start_time) ? $request->start_time : '') : null;
		$updateArr['time_end'] = ($request->end_date) ? $request->end_date.' '.(($request->end_time) ? $request->end_time : '') : $input['time_start'];

		if (isset($request->comments))
			$updateArr['comments'] = $request->comments;
	
		$entities = [];
		$entities['candidate'] = (isset($request->candidatelist)) ? (is_array($request->candidatelist) ? $request->candidatelist : explode(',',$request->candidatelist)) : null;
		$entities['client'] = (isset($request->clientlist)) ? (is_array($request->clientlist) ? $request->clientlist : explode(',',$request->clientlist)) : null;
		$entities['job'] = (isset($request->joblist)) ? (is_array($request->joblist) ? $request->joblist : explode(',',$request->joblist)) : null;
//	dd($entities);
		$success = false;
		DB::beginTransaction();
		try {
			
			$event = CalendarEvent::find($id);
	
			if ($event) {
				$event->update($updateArr);
				if ($event->created_user_id > 0) {
					$this->saveCalendarEventEntities($event, $entities);
					$success = true;
				}
			}
		} catch (\Exception $e) {
			
			return Response::json(['error'=>$e]);
		}
		if ($success) {		
			DB::commit();
			if($request->ajax()){
                return Response::json(true);
            }
			return redirect()->route('calendarevents.index')
                   ->with('success_message','Event created successfully');
		} else {
			DB::rollback();
			if($request->ajax()){
				return Response::json(false);
			}
			return redirect()->back()
                        ->with('error_message','Event not created');
		}

    } 
 
 
    public function destroy($id)
    {
		
        $res = CalendarEvent::where('deletable', true)->where('id',$id)->delete();

        if ($res) {
            return response()->json([
                'status' => '1',
                'msg' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => '0',
                'msg' => 'fail'
            ]);
        }		
    }    


}
