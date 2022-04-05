<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\ContactField;

trait ContactFieldTrait
{

	private function updateOrCreateContactFields(Array $request) 
	{
		$contactfields = $request; //->all(['contacts']);
/*		$all_ids = array_map(function($contactfield){

			$user->contactfields()->updateOrCreate(['id' => $contactfield['id']], 
								['data' => $contactfield['data'],
								 'contact_field_type_id' => $contactfield['type']]);
				return $contactfield['id'];

		}, $contactfields); */
		
		$all_ids = [];
		foreach ($contactfields as $contactfield) {
			$id = isset($contactfield["id"]) ? $contactfield["id"] : null;
			$data = isset($contactfield["data"]) ? $contactfield["data"] : null;
			$type = isset($contactfield["type"]) ? $contactfield["type"] : null;

			$newrecord = $this->contactfieldmodel->contactfields()->updateOrCreate(['id' => $id], 
								['data' => $data,
								 'contact_field_type_id' => $type]);
			$all_ids[] = $newrecord->id;
			
		}

//		ContactField::whereNotIn('id', $all_ids)->delete();
		$this->contactfieldmodel->contactfields()->whereNotIn('id', $all_ids)->delete();
		return ['success' => true];
	}
	
	private function extractSubmittedFieldType(Array $contactfields, int $typeid)
	{
		$values = [];
		foreach ($contactfields as $contactfield) {
			if ($contactfield['type'] == $typeid)
				$values[] = $contactfield['data'];			
		}		
		return $values;
		
	}
	
	protected function extractSubmittedEmailFields(Array $contactfields) 
	{
		return $this->extractSubmittedFieldType($contactfields, 1);
	}
	

}