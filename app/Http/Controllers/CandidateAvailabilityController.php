<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\CandidateAvailability;
use Illuminate\Support\Facades\Validator;

class CandidateAvailabilityController extends BasedataController
{
	
    //
	
    public function __construct(CandidateAvailability $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 0;
		$id = $request->route('candidateavailability');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:candidate_availabilities,description'.$validatorID,
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
        return 'candidateavailabilities';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Candidate Availabilities';
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
