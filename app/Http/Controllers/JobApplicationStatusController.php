<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\JobApplicationStatus;
use Illuminate\Support\Facades\Validator;

class JobApplicationStatusController extends BasedataController
{
	
    //
	
    public function __construct(JobApplicationStatus $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 0;
		$id = $request->route('jobapplicationstatus');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:job_application_statuses,description'.$validatorID,
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
        return 'jobapplicationstatus';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Job Application Status';
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
