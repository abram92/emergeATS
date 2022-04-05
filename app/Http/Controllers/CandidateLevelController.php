<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\CandidateLevel;
use Illuminate\Support\Facades\Validator;

class CandidateLevelController extends BasedataController
{
	
    //
	
    public function __construct(CandidateLevel $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 0;
		$id = $request->route('candidatelevel');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:candidate_levels,description'.$validatorID,
		'colour_hex' => 'nullable|regex:/^#([A-Fa-f0-9]{6})$/'
    ]
);
    }
    /**
     * @return string
     */
    protected function userRole()
    {
        return 'admin';
    }
    /**
     * @return string
     */
    protected function basedataName()
    {
        return 'candidatelevels';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Candidate Levels';
    }
	
    /**
     * @param  null       $entity_id
     * @return array|void
     */
    protected function formData($entity_id = null)
    {   
        return [
            '' => ''
        ];
    }	
}
