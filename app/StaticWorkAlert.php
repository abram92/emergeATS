<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class StaticWorkAlert extends Model
{
	use HasRelationships;

    /**
     * Disable timestamps for the model.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * The Consultant belonging to this alert.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function consultant()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
	

	/**
     * Get the Candidate records attached to the alert.
     *
     * @return HasMany
     */
    public function candidates()
    {
		return $this->belongsToMany('App\Candidate', 'static_work_alert_candidates', 'alert_id', 'candidate_id')->using('App\StaticWorkAlertCandidatePivot')->withPivot(['alert_level', 'status_id'])->with('status');
    }

	/**
     * Get the Job Ad records attached to the alert.
     *
     * @return HasMany
     */
    public function jobs()
    {
		return $this->belongsToMany('App\JobAd', 'static_work_alert_job_ads', 'alert_id', 'job_ad_id')->using('App\StaticWorkAlertJobAdPivot')->withPivot(['alert_level', 'status_id'])->with('status');
    }

	
}
