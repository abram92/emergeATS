<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use Illuminate\Database\Eloquent\Builder;

class Client extends Model implements Auditable
{
    //
	use SoftDeletes;
	use \OwenIt\Auditing\Auditable;
	
	public $edit_comment = '';
	
	protected $fillable = [
        'name', 'status_id', 'agencynotes_id', 'techenvironment_id', 'prospect', 'consultant_id'
    ];

	protected $dates = ['deleted_at'];

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('name', 'ASC');
		} );
	}
	
	public function status()
    {
        return $this->belongsTo('App\ClientStatus', 'status_id');
    }

	public function consultant()
    {
        return $this->belongsTo('App\User', 'consultant_id');
    }
	
	public function contactfields()
    {
        return $this->morphMany('App\ContactField', 'contactable');
    }
	
	public function addresses()
    {
        return $this->morphMany('App\Address', 'addressable');
    }

	public function staff()
    {
        return $this->hasMany('App\ClientContact', 'client_id', 'id')->with('contactfields')->with('comments');
    }
	
/*	public function techenvironment()
    {
          return $this->belongsTo('App\LongFullText', 'techenvironment_id', 'id');
    }

	public function agencynotes()
    {
          return $this->belongsTo('App\LongFullText', 'agencynotes_id', 'id');
    }
*/	
	public function techenvironment()
    {
        return $this->morphOne('App\LongFullText', 'longtextable')->where('field_type', '=', 'techenvironment');
    }

	public function agencynotes()
    {
        return $this->morphMany('App\LongFullText', 'longtextable')->where('field_type', '=', 'agencynotes')->with('editor');
    }
	
	public function jobs()
    {
        return $this->hasMany('App\JobAd', 'client_id', 'id')->with('consultant');
    }

//	public function jobapplications()
//    {
//       return $this->hasManyThrough('App\JobApplication', 'App\JobAd')->with(['jobad'])->with('candidate')->with('emailevents')->whereNotIn('job_ads.status_id', [0, 10, 4, 3])->orderBy('job_ads.activated_at', 'desc')->orderBy('job_ads.jobtitle_text', 'desc');
//    }
	
	public function jobapplications()
    {
        return $this->hasManyThrough('App\JobApplication', 'App\JobAd', null, 'applicationable_id')->with('applicationable')->where('applicationable_type', 'App\JobAd')->with('candidate')->with('emailevents')->whereNotIn('job_ads.status_id', [0, 10, 4, 3])->orderBy('job_ads.activated_at', 'desc')->orderBy('job_ads.jobtitle_text', 'desc');
    }

	public function directapplications()
    {
        return $this->morphMany('App\JobApplication', 'applicationable')->with('applicationable')->where('applicationable_type', 'App\Client')->with('candidate')->with('emailevents')->whereNotIn('job_applications.status_id', [0, 10, 4, 3])->orderBy('job_applications.updated_at', 'desc');
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
