<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactField extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];
	
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = [];



 
    /**
     * Get the contact record associated with the contact field.
     *
     * @return BelongsTo
     */
    public function contactFieldType()
    {
        return $this->belongsTo(ContactFieldType::class);
    }

    /**
     * Scope a query to only include contact field of email type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmail($query)
    {
        return $query->whereHas('contactFieldType', function ($query) {
            $query->where('type', '=', ContactFieldType::EMAIL);
        });
    }

    /**
     * Scope a query to only include contact field of phone type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhone($query)
    {
        return $query->whereHas('contactFieldType', function ($query) {
            $query->where('type', '=', ContactFieldType::PHONE);
        });
    }
	
    /**
     * Get the contact record associated with the contact field.
     *
     * @return BelongsTo
     */
    public function clientcontact()
    {
        return $this->belongsTo(ClientContact::class);
    }

    /**
     * Get the contact record associated with the contact field.
     *
     * @return BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * Get the contact record associated with the contact field.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
	
	public function contactable()
	{
		return $this->morphTo();
	}

}
