<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BasedataController;
use App\ClientStatus;
use Illuminate\Support\Facades\Validator;

class ClientStatusController extends BasedataController
{
	
    //
	
    public function __construct(ClientStatus $model, Request $request)
    {
        $this->model = $model;
        $this->repo = $this->model->all();

		$this->recordsPerPage = 0;
		$id = $request->route('clientstatus');
		$validatorID = $id ? ','.$id : '';
        $this->validator = Validator::make(
    $request->all(),
    [
	'description' => 'required|iunique:client_statuses,description'.$validatorID,
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
        return 'clientstatus';
    }
	
	/**
     * @return string
     */
    protected function basedataClass()
    {
        return 'Client Status';
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
