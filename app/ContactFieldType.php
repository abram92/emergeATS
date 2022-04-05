<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactFieldType extends Model
{
	
	use SoftDeletes;
	
	protected $dates = ['deleted_at'];
	
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'contact_field_types';

    /**
     * Email type contact field.
     *
     * @var string
     */
    public const EMAIL = 'email';

    /**
     * Phone type contact field.
     *
     * @var string
     */
    public const PHONE = 'phone';


}
