<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

use Auth;

class CalendarEvent extends Model
{
//	use SoftDeletes;
	use HasRelationships;
	
	protected $fillable = ['type_id', 'user_id', 'created_user_id', 'title', 'time_start', 'time_end', 'is_all_day', 'background_colour', 'comments'];
	
//	protected $dates = ['deleted_at'];

    protected $appends = ['editable'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
//			if (empty($builder->getQuery()->orders))
	//		$builder->orderBy('created_at', 'DESC');
		} );
	}		
	
    //
	
	public function owner()
    {
        return $this->belongsTo('App\User', 'user_id')->withTrashed();
    }
	
	/**
     * Get the Document records associated with the event.
     *
     * @return HasMany
     */
    public function documents()
    {
		return $this->morphedByMany('App\DataFile', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\DataFile', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }

    public function users()
    {
		return $this->morphedByMany('App\User', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\User', 
//		            ['App\CalendarEventEntity as ent1' ],
 //           ['event_id'],
 //           [null, ['entityable_type', 'entityable_id']],
 //       );
    }

    public function candidates()
    {
		return $this->morphedByMany('App\Candidate', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id')->with('user');
//        return $this->hasManyDeep('App\Candidate', 
//		            ['App\CalendarEventEntity as ent1' ],
 //           ['event_id'],
 //           [null, ['entityable_type', 'entityable_id']],
 //       );
    }

    public function clientcontacts()
    {
		return $this->morphedByMany('App\ClientContact', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\ClientContact', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }

    public function jobs()
    {
		return $this->morphedByMany('App\JobAd', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id')->with('client');
//        return $this->hasManyDeep('App\JobAd', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }

    public function clients()
    {
		return $this->morphedByMany('App\Client', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\Client', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }

    public function emails()
    {
		return $this->morphedByMany('App\LoggedEmail', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\LoggedEmail', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }
	
    public function jobapplications()
    {
		return $this->morphedByMany('App\JobApplication', 'entityable', 'calendar_event_entities', 'event_id', 'entityable_id');
//        return $this->hasManyDeep('App\JobApplication', 
//		            ['App\CalendarEventEntity as ent1' ],
//            ['event_id'],
//            [null, ['entityable_type', 'entityable_id']],
//        );
    }
	
	public function type()
    {
        return $this->belongsTo('App\EventType', 'type_id');
    }
	
	public function getEditableAttribute()
	{
		return (($this->created_user_id > 0) && ($this->user_id == Auth::user()->id));
	}

	
	public function getStartAttribute($value) 
	{
		$dateStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
		$timeStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('H:i:s');
		return $this->time_start = (($timeStart  == '00:00:00') ? $dateStart : $value);
	}

	public function getEndAttribute($value) 
	{
		$dateEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
		$timeEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('H:i:s');
		return $this->time_end = (($timeEnd  == '00:00:00') ? $dateEnd : $value);
	}
}
