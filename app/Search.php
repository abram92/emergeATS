<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    //
	protected $fillable = ['user_id', 'description', 'search_type', 'parameters', 'filtercriteria', 'saved_at'];

}
