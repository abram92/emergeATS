<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sqits\UserStamps\Concerns\HasUserStamps;

use Illuminate\Database\Eloquent\Builder;

class DataFile extends Model
{
    //
	use HasUserStamps;

    protected $fillable = [
            'filename',
            'filetype',
            'size',
            'host',
            'location',
			'datafileable_type',
			'datafileable_id',
    ];
	
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['path'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('created_at', 'DESC');
		} );
	}		
	
	
	public function candidate()
	{
		return $this->belongsTo(Candidate::class);
	}
	
	
	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function job()
	{
		return $this->belongsTo(Job::class);
	}

	public function email()
	{
		return $this->belongsTo(Email::class);
	}

	public function calendarevent()
	{
		return $this->belongsTo(CalendarEvent::class);
	}

    public function datafileable()
	{
		return $this->morphTo();
	}
	
	public function getPathAttribute()
	{
		return "{$this->host}/{$this->location}";
	}

    public function calendar_events()
    {
            return $this->morphToMany('App\CalendarEvent', 'entityable');
    }
	
}
