<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\JobTitle;
use Illuminate\Support\Facades\Validator;

class JobTitleController extends BasedataController
{
	
    //
	
    public function __construct(JobTitle $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 10;
		$id = $request->route('jobtitle');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:job_titles,description'.$validatorID
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
        return 'jobtitles';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Job Title';
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
