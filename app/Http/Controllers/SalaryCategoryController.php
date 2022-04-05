<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\SalaryCategory;
use Illuminate\Support\Facades\Validator;

class SalaryCategoryController extends BasedataController
{
	
    //
	
    public function __construct(SalaryCategory $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();
		
		$this->recordsPerPage = 0;
		$id = $request->route('salarycategory');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
        'description' => 'required|iunique:salary_categories,description'.$validatorID,
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
        return 'salarycategories';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Salary Categories';
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
