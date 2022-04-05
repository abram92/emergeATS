<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Address;

trait AddressTrait
{
	
	
	protected function updateOrCreateAddresses(Array $request) 
	{
		$addresses = $request; 
		
		$all_ids = [];
		foreach ($addresses as $address) {
			$id = isset($address["id"]) ? $address["id"] : null;
			/*
			$data = isset($address["data"]) ? $address["data"] : null;
			$type = isset($address["type"]) ? $address["type"] : null;

			$newrecord = $client->addresses()->updateOrCreate(['id' => $id], 
								['data' => $data,
								 'contact_field_type_id' => $type]); */
			unset($address['id']);					 
			$newrecord = $this->addressmodel->addresses()->updateOrCreate(['id' => $id], 
								$address); 
			$all_ids[] = $newrecord->id;
			
		}

		$this->addressmodel->addresses()->whereNotIn('id', $all_ids)->delete();
		return ['success' => true];
	}

	
	
	
}
