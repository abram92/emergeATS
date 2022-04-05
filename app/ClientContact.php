<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

class ClientContact extends Model //User
{
	use SoftDeletes;
	
	protected $fillable =['firstname', 'lastname', 'client_id', 'position'];
    //
	protected $dates = ['deleted_at'];

	public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
	
	
	public function contactfields()
    {
        return $this->morphMany('App\ContactField', 'contactable');
    }

	public function emailaddresses()
    {
        return $this->morphMany('App\ContactField', 'contactable')->email();
    }

	public function comments()
    {
        return $this->morphOne('App\LongFullText', 'longtextable');
    }
	
	public function getListnameAttribute()
	{
		return "{$this->firstname} {$this->lastname}";
	}
	
    public function calendar_events()
    {
            return $this->morphToMany('App\CalendarEvent', 'entityable');
    }

	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('created_at', 'DESC');
		} );
	}		


}
