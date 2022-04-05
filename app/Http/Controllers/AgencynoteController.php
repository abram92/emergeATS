<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\JobAd;
use App\Candidate;
use App\LongFullText;
use DB;

use App\Http\Traits\LongFullTextTrait;
use App\Http\Traits\SearchTrait;

//use Hash;
//use Arr;

class AgencynoteController extends Controller  //UserController
{
	use LongFullTextTrait;
	use SearchTrait;
	



	private function getModelObject($model, $id) {
		
		switch ($model) {
			case 'candidates' : return Candidate::with('user')->with('agencynotes')->findOrFail($id);
			   break;
			case 'jobs' : return JobAd::with('agencynotes')->findOrFail($id);
               break;
		}		
	}
	
	private function getModelTable($model) {
		
		switch ($model) {
			case 'candidates' : return 'candidates';
			   break;
			case 'jobs' : return 'job_ads';
               break;
		}		
	}
	
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($model, $id)
    {

		$object = $this->getModelObject($model, $id);

		
        return view('agencynotes.edit',compact('object', 'model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $model, $id)
    {
        //
        $this->validate($request, [
			'modelid' => 'required|exists:'.$this->getModelTable($model).',id',			
            'agencynotes' => 'required',
        ]);

		$input = $request->input();
		$success = false;
		DB::beginTransaction();
		try {
					$object = $this->getModelObject($model, $id);
			if ($object) {

				$longtextfields = [];
				if ($request->has('agencynotes')){
					$longtextfields['agencynotes'] = ['relation'=>'agencynotes', 'value'=>strval($request->input('agencynotes'))];
				}		
				$this->longtextfullmodel = $object;
				$this->updateOrCreateLongFullTextFields($longtextfields);
				
				$success = true;
			}

		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route($model.'.show', $id)
                        ->with('success_message','Agency Note updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Agency Note not updated');
		}
		
     }


}
