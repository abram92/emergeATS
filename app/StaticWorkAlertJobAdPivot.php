<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StaticWorkAlertJobAdPivot extends Pivot
{
    //
    protected $table = 'static_work_alert_job_ad';

    public function status()
    {
        return $this->belongsTo('App\JobStatus', 'status_id');
    }
}
