<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;

use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class JobApplication extends Model implements Auditable
{
	use SoftDeletes;
	use \OwenIt\Auditing\Auditable;
	use HasRelationships;
	
	protected $dates = ['deleted_at'];
	
    //
	protected $fillable = ['applicationable_id', 'applicationable_type', 'candidate_id', 'status_id', 'comments'];
	
	
	protected $auditInclude = ['applicationable_id', 'applicationable_type', 'candidate_id', 'status_id', 'comments' ];
	
    /**
     * 
     *
     * @return BelongsTo
     */
	 /*
public function jobad()
{
   return $this->hasOne(JobAd::class, 'id', 'applicationable_id')
       ->where('applicationable_type', JobAd::class)->with('client')->with('clientcontacts')->with('status')->with('cvsendinstructions');
}
public function client()
{
   return $this->hasOne(Client::class, 'id', 'applicationable_id')
        ->where('applicationable_type', Client::class)->with('staff')->with('status');
}
	 */
	public function jobad()
	{
		if($this->applicationable_type == 'App\JobAd') 
			return $this->applicationable()->with('client')->with('clientcontacts')->with('status')->with('cvsendinstructions');
		return null;
	}

	public function client()
	{
		if($this->applicationable_type == 'App\Client') 
			return $this->applicationable()->with('staff')->with('status');
		return null;
	}

	public function applicationable()
	{
		return $this->morphTo('applicationable');
	}
	 
//    public function jobad()
//    {
//        return $this->belongsTo(JobAd::class, 'job_ad_id')->with('client')->with('clientcontacts')->with('status')->with('cvsendinstructions');
//    }	
	
	public function status()
    {
        return $this->belongsTo(JobApplicationStatus::class, 'status_id');
    }

	public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id')->with('emailaddresses')->with('user');
    }

	
	/**
     * Get the Email records linked to the job application.
     *
     * @return HasMany
     */
    public function emails()
    {
        return $this->hasManyDeep('App\LoggedEmail', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        );
    }
	
	/**
     * Get the Email Events linked to the job application.
     *
     * @return HasMany
     */
    public function emailevents()
    {
         return $this->hasManyDeep('App\CalendarEvent', 
		            ['App\CalendarEventEntity as ent1' ],
            [['entityable_type', 'entityable_id']],
            [null, 'event_id']
        )->with('clientcontacts')->orderBy('calendar_events.created_at', 'desc'); //->where('type_id', '7'); 
		
 //	   return $this->hasManyThrough('App\CalendarEvent','App\CalendarEventEntity', 'entityable_id', 'id')->with('contacts')->where('entityable_type', 'App\JobApplication');		
    }
	


	public function transformAudit(array $data): array
    {
        return $data;
    }
}
