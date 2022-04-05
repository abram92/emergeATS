<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use App\User;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Candidate extends Model implements Auditable //User
{
	
	use SoftDeletes;
	use \OwenIt\Auditing\Auditable;
	use HasRelationships;
	
	public $edit_comment = '';
	
	protected $fillable =['id', 'birthdate', 'idnumber', 'gender_id', 'consultant_id', 'duplicate', 'current_location_id', 'status_id',
							'jobtitle_id', 'jobtitle_text', 'salary', 'job_ref_codes', 'job_ref_codes_max',
							'candidate_level_id', 'candidate_rating_id', 'salary_category_id', 'ee_status_id', 'availability_id', 'interviewed', 'activated_at'];
	
	protected $dates = ['deleted_at'];
	

	public function user()
    {
    //    return $this->belongsTo(User::class, 'id', 'id')->withTrashed();
        return $this->morphOne('App\User', 'userable');
    }
	

	public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }
	
	public function status()
    {
        return $this->belongsTo(CandidateStatus::class, 'status_id');
    }

	public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

	public function jobtitle()
    {
        return $this->belongsTo(JobTitle::class, 'jobtitle_id');
    }

	public function eestatus()
    {
        return $this->belongsTo(EeStatus::class, 'ee_status_id');
    }

	public function level()
    {
        return $this->belongsTo(CandidateLevel::class, 'candidate_level_id');
    }
	
	public function rating()
    {
        return $this->belongsTo(CandidateRating::class, 'candidate_rating_id');
    }

	public function salarycategory()
    {
        return $this->belongsTo(SalaryCategory::class, 'salary_category_id');
    }

	public function availability()
    {
        return $this->belongsTo(CandidateAvailability::class, 'availability_id');
    }
	
	public function location()
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

	public function preferredlocations()
    {
		return $this->belongsToMany('App\Location', 'candidate_preferred_location');
    }

	public function addresses()
    {
        return $this->morphMany('App\Address', 'addressable');
    }
	
	public function contactfields()
    {
        return $this->morphMany('App\ContactField', 'contactable');
    }
	
	public function emailaddresses()
    {
        return $this->morphMany('App\ContactField', 'contactable')->email();
    }

	public function directapplications()
	{
		return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\Client')->orderBy('created_at', 'DESC');
	}
	
	public function jobapplications()
	{
		return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\JobAd')->orderBy('created_at', 'DESC');
	}

	public function jobapplicationsShortlisted()
    {
        return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\JobAd')->doesntHave('emailevents')->orderBy('created_at', 'desc'); //->with(['jobad'])->orderBy('created_at', 'desc');
    }

//->loadMorph('job_application.applicationable', [
//                JobAd::class => ['jobad'],
//            ])
			
	public function jobapplicationsSentTo()
    {
        return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\JobAd')->withCount(['emailevents' => function ($query) {
        $query->where('time_start', '>', date('Y-m-d', strtotime('-120 days')));
    }])->has('emailevents')->with('emailevents')->orderBy('created_at', 'desc'); //->with(['jobad'])->with('emailevents')->orderBy('created_at', 'desc');
    }

	public function eventparticipant()
	{
		return $this->morphMany('App\CalendarEventEntity', 'entityable');
	}

    public function calendar_events()
    {
            return $this->morphToMany('App\CalendarEvent', 'entityable');
    }
	
/*	public function event()
	{
		return $this->->morphMany('App\CalendarEventEntity', 'entityable');
	}
*/	
	/**
     * Get the Document records associated with the candidate.
     *
     * @return HasMany
     */
    public function documents()
    {
        return $this->morphMany('App\DataFile', 'datafileable');
    }
	
	
	public function sellme()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'sellme');
    }
	
	public function textcv()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'textcv');
    }

	public function interviewnotes()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'interviewnotes');
    }

	public function agencynotes()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'agencynotes');
    }

	public function idealjob()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'idealjob');
    }
	
	public function summary()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'summary');
    }
	


//	public function jobsShortlisted()
//    {
//        return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\JobAd')->with('jobad')->with('status')->orderBy('created_at', 'DESC');
//    }

//	public function jobsCandidateSentTo()
//    {
//        return $this->hasMany('App\JobApplication', 'candidate_id')->where('applicationable_type', 'App\JobAd')->orderBy('created_at', 'DESC');
//    }

	public function emails()
    {
        return $this->hasManyDeep('App\LoggedEmail', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        )->orderBy('date', 'DESC');
//		'candidate_id')->orderBy('created_at', 'DESC');
    }


	public function transformAudit(array $data): array
    {
        if ($this->edit_comment) {
            $data['old_values']['comments'] = null;
            $data['new_values']['comments'] = $this->edit_comment;
        }

        return $data;
    }
	
	public function delete(){
		$this->user()->delete();
		$this->sellme()->delete();
		$this->textcv()->delete();
		$this->interviewnotes()->delete();
		$this->agencynotes()->delete();
		$this->idealjob()->delete();
		$this->summary()->delete();
		$this->delete();
	}
}
