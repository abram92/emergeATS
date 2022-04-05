<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class CalendarEventEntity extends MorphPivot
{
	use \Staudenmeir\EloquentHasManyDeep\HasTableAlias;	
	
	protected $table = 'calendar_event_entities';
}
