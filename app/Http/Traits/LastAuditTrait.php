<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;

trait LastAuditTrait implements Auditable
{
	
	use \OwenIt\Auditing\Auditable;
	
	protected function lastaudit() 
	{
		return $this->audits()->with('user')->latest()->first();
	}

	
	
	
}
