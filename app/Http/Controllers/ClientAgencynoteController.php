<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Client;
use App\LongFullText;
use DB;

use App\Http\Traits\LongFullTextTrait;
use App\Http\Traits\SearchTrait;

//use Hash;
//use Arr;

class ClientAgencynoteController extends Controller  //UserController
{
	use LongFullTextTrait;
	use SearchTrait;
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($clientid)
    {
        //
//		parent::create();
		$client = Client::findOrFail($clientid);  
        return view('clients.agencynotes.create',compact('client'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
//		parent::store($request);
        $this->validate($request, [
 			'client_id' => 'required|exists:clients,id',			
            'agencynotes' => 'required',
        ]);

        $input = $request->all();
		$clientid = $request->input('client_id');
		
		$success = false;
		DB::beginTransaction();
		try {
		
			$client = Client::findOrFail($clientid);
			if ($client) {
				
				$longtextfields = [];
				if ($request->has('agencynotes')){
					$note_id = $request->has('note_id') ? $request->input('note_id') : 0;
					$longtextfields['agencynotes'] = ['relation'=>'agencynotes', 'value'=>strval($request->input('agencynotes')), 'multi' => ['id'=>$note_id]];
				}		
				$this->longtextfullmodel = $client;
				$this->updateOrCreateLongFullTextFields($longtextfields);
				
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.show', $request->input('client_id'))
                        ->with('success_message','Agency Note created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Agency Note not created');
		}	

		

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $note = LongFullText::with('editor')->findOrFail($id);
		$client = $note->longtextable()->get()->first();

        return view('clients.agencynotes.show',compact('client', 'note'));
		
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $note = LongFullText::findOrFail($id);
		$client = $note->longtextable()->get()->first();

        return view('clients.agencynotes.edit',compact('note', 'client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
			'client_id' => 'required|exists:clients,id',			
            'agencynotes' => 'required',
        ]);

		$input = $request->input();
		$clientid = $request->input('client_id');
		$success = false;
		DB::beginTransaction();
		try {
			$client = Client::findOrFail($clientid);
			if ($client) {
				
				$longtextfields = [];
				if ($request->has('agencynotes')){
					$longtextfields['agencynotes'] = ['relation'=>'agencynotes', 'value'=>strval($request->input('agencynotes')), 'multi' => ['id'=>$id]];
				}		
				$this->longtextfullmodel = $client;
				$this->updateOrCreateLongFullTextFields($longtextfields);
				
				$success = true;
			}

		} catch (\Exception $e) {
dd($e);			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.show', $request->input('client_id'))
                        ->with('success_message','Agency Note updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Agency Note not updated');
		}
		
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
		parent::destroy($id);
    }
}
