<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class LoggedEmail extends Model
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
        'date',
        'address_from',
        'address_to',
        'address_cc',
        'address_bcc',
        'subject',
        'body',
        'headers',
        'messageId',
        'mail_driver',
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
     * The Events belonging to this Email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->belongsTo('CalendarEvent','messageId','messageId');
    }
	
	
    /**
     * The Sender belonging to this Email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sender()
    {
        return $this->belongsTo('App\User','address_from','id')->withTrashed();
    }
	
	/**
     * Get the Document records attached to the email.
     *
     * @return HasMany
     */
    public function documents()
    {
        return $this->morphMany('App\DataFile', 'datafileable');
    }

	/**
     * Get the Candidate records attached to the email.
     *
     * @return HasMany
     */
    public function candidates()
    {
        return $this->hasManyDeep('App\Candidate', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        )->with('user');
    }

	/**
     * Get the Job Ad records attached to the email.
     *
     * @return HasMany
     */
    public function jobads()
    {
        return $this->hasManyDeep('App\JobAd', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        )->with('client');
    }

	/**
     * Get the Client records attached to the email.
     *
     * @return HasMany
     */
    public function clients()
    {
        return $this->hasManyDeep('App\Client', 
		            ['App\CalendarEventEntity as ent1', 'App\CalendarEvent', 'App\CalendarEventEntity as ent2', ],
            [['entityable_type', 'entityable_id'], null, 'event_id'],
            [null, 'event_id', null, ['entityable_type', 'entityable_id']]
        );
    }
	
    public function calendar_events()
    {
            return $this->morphToMany('App\CalendarEvent', 'entityable');
    }
	
}
