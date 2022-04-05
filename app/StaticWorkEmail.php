<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class StaticWorkEmail extends Model
{
	use HasRelationships;

    /**
     * Disable timestamps for the model.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consultant_id',
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
     * Get the Recipient records attached to the alert.
     *
     * @return HasMany
     */
    public function recipients()
    {
		return $this->belongsToMany('App\User', 'static_work_email_recipients', 'email_id', 'user_id');
    }
	
	/**
     * Get the Candidate records attached to the alert.
     *
     * @return HasMany
     */
    public function candidates()
    {
		return $this->belongsToMany('App\Candidate', 'static_work_email_candidates', 'email_id', 'candidate_id');
    }

	/**
     * Get the Job Ad records attached to the alert.
     *
     * @return HasMany
     */
    public function jobs()
    {
		return $this->belongsToMany('App\JobAd', 'static_work_email_job_ads', 'email_id', 'job_ad_id');
    }
	
}
