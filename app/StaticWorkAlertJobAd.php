<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

use App\StaticWorkAlert;

class StaticWorkAlertJobAd extends Model
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
        'job_id',
		'alert_level',
		'level_ordinal',
		'status_id'
		
    ];


    /**
     * The Alert this record belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staticworkalert()
   {
        return $this->belongsTo('App\StaticWorkAlert','alert_id','id');
    }
	
	public function status()
    {
        return $this->belongsTo('App\JobStatus', 'status_id');
    }

	
}
