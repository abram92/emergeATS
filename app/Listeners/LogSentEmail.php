<?php

namespace App\Listeners;

use Auth;

use DB;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\LoggedEmail;
use App\Datafile;

use Illuminate\Support\Facades\Storage;

use App\Http\Traits\CalendarEventTrait;

class LogSentEmail
{
	
	use CalendarEventTrait;
	
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageSending  $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
         $message = $event->message;
// dd($event);
        $messageId = strtok($message->getId(), '@');
		
        $attachments = [];
        foreach ($message->getChildren() as $child) {
            //docs for this below: http://phpdox.de/demo/Symfony2/classes/Swift_Mime_SimpleMimeEntity/getChildren.xhtml
            if(in_array(get_class($child),['Swift_EmbeddedFile','Swift_Attachment'])) {
				$filename = $child->getFilename();
				$filetype = $child->getContentType();
                $attachmentPath = date('Ymd').'/' . $messageId . '/' . $filename;
				$savePath = config('email_log.folder') . '/'. $attachmentPath;
                Storage::disk('uploads')->put($savePath, $child->getBody());
				$filesize = Storage::disk('uploads')->size($savePath); //$child->getSize();
				
                $attachments[] = ['filename'=>$filename, 'filetype'=>$filetype, 'filesize'=>$filesize, 'host'=>config('email_log.folder'), 'location'=>$attachmentPath];
            }
        }
//
		DB::beginTransaction();
$success = false;
		try {
        $emailLog = LoggedEmail::create([
            'date' => date('Y-m-d H:i:s'),
            'address_from' => optional(Auth::user())->id, //$this->formatAddressField($message, 'From'),  // Auth::user()->id
            'address_to' => $this->formatAddressField($message, 'To'),
            'address_cc' => $this->formatAddressField($message, 'Cc'),
            'address_bcc' => $this->formatAddressField($message, 'Bcc'),
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'headers' => (string)$message->getHeaders(),
//            'attachments' => empty($attachments) ? null : implode(', ', $attachments),
            'messageId' => $messageId,
            'mail_driver' => config('mail.driver'),
        ]);
		
		foreach($attachments as $attachment) {
			DataFile::create([
				'filename' => $attachment['filename'],
				'filetype' => $attachment['filetype'],
				'size' => $attachment['filesize'],
				'host' => $attachment['host'],
				'location' => $attachment['location'],
				'datafileable_type' => 'App\LoggedEmail',
				'datafileable_id' => $emailLog->id,
			]);
		}
		

		if(optional($event->message)->calendarProperties) {
			$calendarProperties = $event->message->calendarProperties;

			$calendarProperties['email'] = [$emailLog->id];
		
			$user_id = isset($calendarProperties['user_id']) ? $calendarProperties['user_id'] : Auth::user()->id;
			$type_id = isset($calendarProperties['type_id']) ? $calendarProperties['type_id'] : 5;
			$event_time = date('Y-m-d H:i:s');

			$eventProps = ['type_id'=>$type_id, 
						'user_id'=> $user_id, //$calendarProperties['user_id'], 
						'created_user_id'=>-1, //$calendarProperties['user_id'], 
						'title'=>$calendarProperties['title'], 
						'time_start'=>$event_time, 
						'time_end'=>$event_time,
						'background_colour'=>'#ffaacc', 
						];
		
			$this->saveCalendarEvent($eventProps, $calendarProperties);
		}
		
			$success = true;
		} catch (\Exception $e) {
			
			dd($e);
		}
		if ($success) {		
			DB::commit();
		} else {
			DB::rollback();
		}
   }
   
    /**
     * Format address strings for sender, to, cc, bcc.
     *
     * @param $message
     * @param $field
     * @return null|string
     */
    function formatAddressField($message, $field)
    {
        $headers = $message->getHeaders();

        if (!$headers->has($field)) {
            return null;
        }

        $mailboxes = $headers->get($field)->getFieldBodyModel();

        $strings = [];
        foreach ($mailboxes as $email => $name) {
            $mailboxStr = $email;
            if (null !== $name) {
                $mailboxStr = $name . ' <' . $mailboxStr . '>';
            }
            $strings[] = $mailboxStr;
        }
        return implode(', ', $strings);
    }
   
}
