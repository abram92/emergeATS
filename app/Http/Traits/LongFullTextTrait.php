<?php

namespace App\Http\Traits;

use Auth;

use Illuminate\Http\Request;
use App\LongFullText;
use App\Http\Traits\SearchTrait;

trait LongFullTextTrait
{
	use SearchTrait;
	
	private function updateOrCreateLongFullTextFields(Array $fields) 
	{
		$obj = [];
		$editor_id = (Auth::check()) ? Auth::user()->id : 1;
		foreach ($fields as $fieldname => $fieldproperties){
			
			$relation = $fieldproperties['relation'];
			$value = $fieldproperties['value'];
			$search_transl = $this->FTS_mapSpecialChars($value);
			$key = ['field_type' => $fieldname];
			if(isset($fieldproperties['multi']) && is_array($fieldproperties['multi'])){
				$key += $fieldproperties['multi'];
			}
			$obj[$fieldname] = $this->longtextfullmodel->$relation()->updateOrCreate($key, 
								['chunk' => $value,
								 'search_transl' => $search_transl,
								 'editor_id' => $editor_id,
//								 'chunk_tokens' => "to_tsvector('simple',".$chunk_tokens.")",
								 'field_type' => $fieldname]);
		}
		return ['success' => true];
	}
	
	
	private function getLongText(Object $obj) {
		if ($obj)
			return $obj->chunk;
		
		return '';
	}
	
	
}
