<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Dmcbrn\LaravelEmailDatabaseLog\Events\EventFactory;
use App\LoggedEmail;

class LoggedEmailController extends Controller {


    public function __construct()
	{
		$this->model_class = 'App\LoggedEmail';
	}	
	
    public function index(Request $request)
    {
        //validate
        $request->validate([
            'filterEmail' => 'string',
            'filterSubject' => 'string',
        ]);

        //get emails
        $filterEmail = $request->filterEmail;
        $filterSubject = $request->filterSubject;
        $emails = LoggedEmail::with([
                'events' => function($q) {
                    $q->select('messageId','created_at','event');
                }
            ])
            ->select('id','messageId','date','from','to','subject')
            ->when($filterEmail, function($q) use($filterEmail) {
                return $q->where('to','like','%'.$filterEmail.'%');
            })
            ->when($filterSubject, function($q) use($filterSubject) {
                return $q->where('subject','like','%'.$filterSubject.'%');
            })
            ->orderBy('id','desc')
            ->paginate(20);

        //return
        return view('emails.index', compact('emails','filterEmail','filterSubject'));
    }

    public function show($id)
    {
        $email = LoggedEmail::find($id);
		$documents = $email->documents()->get();
		$candidates = $email->candidates;
		$clients = $email->clients;
		$jobads = $email->jobads;
        return view('emails.show', compact('email', 'documents', 'candidates', 'clients', 'jobads'));
    }

    public function fetchAttachment($id,$attachment)
    {
        $email = EmailLog::select('id','attachments')->find($id);
        $attachmentFullPath = explode(',',$email->attachments)[$attachment];

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