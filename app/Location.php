<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

class Location extends Model
{
	use SoftDeletes;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description'
    ];
	

    //
	protected $dates = ['deleted_at'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function(Builder $builder) {
			if (empty($builder->getQuery()->orders))
				$builder->orderBy('description', 'ASC');
		} );
	}		
	
}
