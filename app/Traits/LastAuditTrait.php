<?php

namespace App\Traits;

trait LastAuditTrait 
{
		
	public function lastaudit() 
	{
		return $this->audits()->with('user')->latest()->first();
	}

	
	
	
}
