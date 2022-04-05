<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\CalendarEvent;

trait CalendarEventTrait
{
	
	
	public function saveCalendarEvent(Array $properties, Array $relations) 
	{

		$cal = CalendarEvent::create($properties);
		
//		dd($relations);
		if ($cal) {
			$this->saveCalendarEventEntities($cal, $relations);
		}
	}
	
	public function saveCalendarEventEntities(CalendarEvent $cal, Array $relations)
	{
				if (isset($relations['document']) && is_array($relations['document']) && !empty($relations['document']))
					$cal->documents()->sync($relations['document']);

				if (isset($relations['user']) && is_array($relations['user']) && !empty($relations['user']))
					$cal->users()->sync($relations['user']);

				if (isset($relations['candidate']) && is_array($relations['candidate']) && !empty($relations['candidate']))
					$cal->candidates()->sync($relations['candidate']);

				if (isset($relations['clientcontact']) && is_array($relations['clientcontact']) && !empty($relations['clientcontact']))
					$cal->clientcontacts()->sync($relations['clientcontact']);

				if (isset($relations['job']) && is_array($relations['job']) && !empty($relations['job']))
					$cal->jobs()->sync($relations['job']);

				if (isset($relations['client']) && is_array($relations['client']) && !empty($relations['client']))
					$cal->clients()->sync($relations['client']);

				if (isset($relations['email']) && is_array($relations['email']) && !empty($relations['email']))
					$cal->emails()->sync($relations['email']);
				
				if (isset($relations['jobapplication']) && is_array($relations['jobapplication']) && !empty($relations['jobapplication']))
					$cal->jobapplications()->sync($relations['jobapplication']);		
	}
	
}
