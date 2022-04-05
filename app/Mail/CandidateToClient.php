<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Storage;

class CandidateToClient extends Mailable
{
    use Queueable, SerializesModels;

	public $data;
	public $calendarProperties;
	public $from;
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $calendarProperties, $from=null)
    {
        //
		$this->data = $data;
		$this->calendarProperties = $calendarProperties;
		$this->from = $from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$email = $this->view('sendmail.standard');
		$ts = $this->calendarProperties;
		
		if ($this->from)
			$email->from($this->from);		
		$email->withSwiftMessage(function ($message) use($ts){
        $message->calendarProperties = $ts;
    });
		if (isset($this->data['subject']))
		$email->subject($this->data['subject']);
		if (isset($this->data['attachments'])) {
			foreach ($this->data['attachments'] as $attachment) {
			
			$pathtofile = Storage::disk('uploads')->path($attachment['path']);
			if (file_exists($pathtofile))	{
//				$email->attachFromStorage($pathtofile, $attachment['filename'], [
				$email->attach($pathtofile, ['as'=>$attachment['filename'], 
				'mime' => \GuzzleHttp\Psr7\mimetype_from_filename($attachment['filename'])
				]);
			}	
			}
		}
        return $email;
    }
}
