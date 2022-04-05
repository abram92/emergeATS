<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StaticWorkAlertCandidatePivot extends Pivot
{
    //
    protected $table = 'static_work_alert_candidate';

    public function status()
    {
        return $this->belongsTo('App\CandidateStatus', 'status_id');
    }
}
