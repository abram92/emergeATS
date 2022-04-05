<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\Gender;
use Illuminate\Support\Facades\Validator;

class GenderController extends BasedataController
{
	
    //
	
    public function __construct(Gender $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 0;
		$id = $request->route('gender');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:genders,description'.$validatorID,
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
        return 'genders';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Genders';
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
