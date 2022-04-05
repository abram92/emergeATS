<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Dmcbrn\LaravelAlertDatabaseLog\Events\EventFactory;
use App\StaticWorkAlert;

class StaticWorkAlertController extends Controller {

    public function index(Request $request)
    {
        //validate
        $request->validate([
            'filterAlert' => 'string',
            'filterSubject' => 'string',
        ]);

        //get Alerts
        $filterAlert = $request->filterAlert;
        $filterSubject = $request->filterSubject;
        $Alerts = StaticWorkAlert::with([
                'events' => function($q) {
                    $q->select('messageId','created_at','event');
                }
            ])
            ->select('id','messageId','date','from','to','subject')
            ->when($filterAlert, function($q) use($filterAlert) {
                return $q->where('to','like','%'.$filterAlert.'%');
            })
            ->when($filterSubject, function($q) use($filterSubject) {
                return $q->where('subject','like','%'.$filterSubject.'%');
            })
            ->orderBy('id','desc')
            ->paginate(20);

        //return
        return view('Alerts.index', compact('Alerts','filterAlert','filterSubject'));
    }

    public function show($id)
    {
        $Alert = StaticWorkAlert::find($id);
		$documents = $Alert->documents()->get();
		$candidates = $Alert->candidates;
		$clients = $Alert->clients;
		$jobads = $Alert->jobads;
        return view('Alerts.show', compact('Alert', 'documents', 'candidates', 'clients', 'jobads'));
    }

    public function fetchAttachment($id,$attachment)
    {
        $Alert = AlertLog::select('id','attachments')->find($id);
        $attachmentFullPath = explode(',',$Alert->attachments)[$attachment];

        return Storage::get(urldecode($attachmentFullPath));
    }

    public function createEvent(Request $request)
    {
    	$event = EventFactory::create('mailgun');

    	//check if event is valid
    	if(!$event)
            return response('Error: Unsupported Service', 400)->header('Content-Type', 'text/plain');

        //validate the $request data for this $event
        if(!$event->verify($request))
            return response('Error: verification failed', 400)->header('Content-Type', 'text/plain');

        //save event
        return $event->saveEvent($request);
    }
    
}