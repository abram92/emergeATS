<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

class Team extends Model
{
	use SoftDeletes;
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'colour_hex'
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

	public function leaders()
    {
        return $this->belongsToMany(User::class, 'team_leader');
    }

	public function members()
    {
        return $this->belongsToMany(User::class, 'team_member');
    }	
	
}
