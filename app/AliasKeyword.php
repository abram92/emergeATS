<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AliasKeyword extends Model
{
    //
	protected $fillable = ['alias_id', 'keyword'];
	
	protected $primaryKey = null;
	public $incrementing = false;
	public $timestamps = false;
}
