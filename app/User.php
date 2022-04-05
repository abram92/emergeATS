<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\SoftDeletes;



class User extends Authenticatable
{
    use Notifiable, HasRoles, HasPushSubscriptions, SoftDeletes;

	protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'password', 'userable_id', 'jobcode', 'emailsignature', 'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
//	protected $appends = ['listname'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC');
		} );
	}		
	
	public function contactfields()
    {
        return $this->morphMany('App\ContactField', 'contactable');
    }
	
	public function emailaddresses()
    {
        return $this->morphMany('App\ContactField', 'contactable')->email();
    }
	
	public function getFullnameUsernameAttribute()
	{
		return "{$this->listname} ({$this->username})";
	}

	public function getListnameAttribute()
	{
		return "{$this->firstname} {$this->lastname}";
	}
	
/*	public function clientcontact()
    {
        return $this->belongsTo(ClientContact::class, 'id', 'user_id');
    } */
	
	public function userable()
	{
		return $this->morphTo();
	}
	
	public function audited() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->orderBy('created_at', 'DESC');
	}
	
	/*
	public function assignedcandidates()
    {
        return $this->hasMany(Candidate::class, 'id', 'consultant_id');
    }	
	
	public function assignedjobs()
    {
        return $this->hasMany(JobAd::class, 'id', 'consultant_id');
		return $this->belongsToMany(JobAd::class, 'consultant_job_ad');
    }	
	
	public function assignedclients()
    {
        return $this->hasMany(Client::class, 'id', 'consultant_id');
    }	*/
	
	
	public function candidatesEdited() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\Candidate')->where('event', 'updated')->orderBy('created_at', 'DESC');
	}

	public function candidatesLoaded() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\Candidate')->whereNotNull('new_values->activated_at')->orderBy('created_at', 'DESC');
	}

	public function candidatesActive() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\Candidate')->whereJsonContains('new_values->status_id', 2)->orderBy('created_at', 'DESC');
	}
	
	public function cvSent() 
	{
		return $this->hasMany('\App\CalendarEvent', 'user_id')->where('type_id', '7')->orderBy('created_at', 'DESC');
	}	
	
	public function jobsActive() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\JobAd')->whereJsonContains('new_values->status_id', 2)->orderBy('created_at', 'DESC');
	}
	
	public function jobsTotal() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\JobAd'); //->whereNotNull('new_values->activated_at')->orderBy('created_at', 'DESC');
	}
	
	public function clientsLoaded() 
	{
		return $this->hasMany('\OwenIt\Auditing\Models\Audit', 'user_id')->where('auditable_type', 'App\Client')->whereJsonContains('new_values->status_id', 2)->orderBy('created_at', 'DESC');
	}	


	public function staticAlerts() 
	{
		return $this->hasMany('App\StaticAlert', 'user_id')->orderBy('created_at', 'DESC');
	}	
	
	public function getInitialsAttribute()
	{
		if ($this->jobcode)
			return strtoupper($this->jobcode);
		$initials = '';
		$words = preg_split("/[\s,_-]+/", $this->firstname.' '.$this->lastname);
		foreach ($words as $w) {
			$initials .= $w[0];
		}
		return $initials;
	}

}
