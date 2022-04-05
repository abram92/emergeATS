<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class JobAd extends Model implements Auditable
{
	use SoftDeletes;
	use \OwenIt\Auditing\Auditable;
	use HasRelationships;
	
	protected $dates = ['deleted_at'];
	
	public $edit_comment = '';
    //
	protected $fillable = ['client_id', 'client_contact_id', 'consultant_id', 'jobref', 'jobdescription', 
				'start_date', 'duration', 'gender_id', 'jobtype_id', 'jobtitle_text', 'jobtitle_id', 'location_id', 
				'salary_category_id', 'salary_from', 'salary_to', 'rate_per_hour', 'rate_per_day', 'currency_id', 'ee_status_id', 
				'status_id', 'activated_at'];
	
	
	protected $auditInclude = ['status_id', 'consultant_id', 'jobref', ];
	
    /**
     * Get the client who owns the job.
     *
     * @return BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }	

	public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }
	
	public function status()
    {
        return $this->belongsTo(JobStatus::class, 'status_id');
    }

	public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

	public function salarycategory()
    {
        return $this->belongsTo(SalaryCategory::class, 'salary_category_id');
    }
	
	public function eestatus()
    {
        return $this->belongsTo(EeStatus::class, 'ee_status_id');
    }

//	public function location()
//    {
//        return $this->belongsTo(Location::class, 'location_id');
//    }

	public function locations()
    {
        return $this->belongsToMany(Location::class, 'job_ad_location');
    }
	
	public function clientcontacts()
	{
		return $this->belongsToMany('App\ClientContact', 'client_contact_job_ad')->with('contactfields');
	}
	
	public function cvsendinstructions()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'cvsendinstructions');;
    }

	public function projectplan()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'projectplan');;
    }

	public function summary()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'summary');
    }
	
	public function agencynotes()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'agencynotes');;
    }

	public function skills()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'skills');;
    }
	
	public function technicalarea()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'technicalarea');;
    }
	
	public function fulldescription()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'fulldescription');;
    }
	
	/**
     * Get the Document records associated with the client.
     *
     * @return HasMany
     */
    public function documents()
    {
        return $this->morphMany('App\DataFile', 'datafileable');
    }

	public function jobapplications()
	{
		return $this->morphMany('App\JobApplication', 'applicationable')->with('candidate')->with('status')->orderBy('job_applications.created_at', 'DESC');
	}

	public function jobapplicationsLinked()
    {
        return $this->morphMany('App\JobApplication', 'applicationable')->with('candidate')
					->whereHas('emailevents', function ($q) { 
//								$query->where('type_id', 3);					
											$q->where('title', 'like', 'Emailed CV%'); 
										})
											->with(['emailevents' => function($q) { 
//								$query->where('calendar_events.type_id', 3);											
																$q->where('calendar_events.title', 'like', 'Emailed CV%'); 
															}])
															->where('status_id', 1)
															->orderBy('created_at', 'desc');
    }


	public function jobapplicationsProspect()
    {
        return $this->morphMany('App\JobApplication', 'applicationable')->with('candidate')
																		->with(['emailevents' => function($q) { 
//								$query->where('type_id', 2);																		
																			$q->where('title', 'like', 'Emailed Job Spec%'); 
																			}])
																			->whereIn('status_id', [8, 15, 14, 6, 9]) 
//																			->where('status_id', '!=', 6)
																			->orderBy('created_at', 'desc');
// ->whereHas('emailevents', function ($q) { 
//																		$q->where('title', 'like', 'Emailed Job Spec%'); 
//																		}) 
 }

	public function emails()
    {
        return $this->hasManyDeep('App\LoggedEmail', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        )->orderBy('date', 'DESC');
//		'candidate_id')->orderBy('created_at', 'DESC');
    }
	
    public function calendar_events()
    {
            return $this->morphToMany('App\CalendarEvent', 'entityable');
    }

	public function transformAudit(array $data): array
    {
        if ($this->edit_comment) {
            $data['old_values']['comments'] = null;
            $data['new_values']['comments'] = $this->edit_comment;
        }

        return $data;
    }
}
