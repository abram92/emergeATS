<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alias extends Model
{
	use SoftDeletes;
    //
	protected $fillable = ['description', 'alias_category_id', 'minimum_parser_matches'];
	
	protected $dates = ['deleted_at'];

	public function category()
    {
        return $this->belongsTo(AliasCategory::class, 'alias_category_id');
    }
	
	public function keywords()
	{
		return $this->hasMany(AliasKeyword::class);
	}
	
}
