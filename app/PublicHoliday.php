<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

class PublicHoliday extends Model
{
//	use SoftDeletes;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
		'holiday_date',
		'recurring'
    ];
	

    //
//	protected $dates = ['deleted_at'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('holiday_date', 'ASC');
		} );
	}		
	
}
