<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Search;
class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		$user_id = Auth::user()->id;
//		$search_type = $request->input('search_type');
//        $data = Search::where([['search_type', '=', $search_type],['user_id', '=', $user_id]])->orderBy('saved_at','DESC');
		$data = Search::whereNotNull('saved_at')->where([['user_id', '=', $user_id]])->orderBy('search_type','ASC')->orderBy('saved_at','DESC')->get();

        return view('search.index',compact('data'));
//            ->with('i', ($request->input('page', 1) - 1) * 25);
				

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $search = Search::find($id);
		
        return view('search.show',compact('search'));
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
        //
        $search = Search::findorFail($id);
		
        return view('search.edit',compact('search'));
		
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
            'description' => 'required|',
        ]);

        $input = $request->all();
		$success = false;
		
		try {

			$search = Search::find($id);
			$search->description = $input['description'];
			$search->saved_at = date('Y-m-d H:i:s');
			
			if ($search->update()) {
				$success = true;
			}
		} catch (\Exception $e) {
			dd($e);
			
		}
		if ($success) {		
			return redirect()->route('savedsearches.index')
                        ->with('success_message','Search saved successfully');
		} else {
			return redirect()->back()
                        ->with('error_message','Search not saved');
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
        Search::find($id)->delete();
        return redirect()->route('savedsearches.index')
                        ->with('success_message','Search deleted successfully');

    }
	
	
	public function setcheckbox() {
        $id = \Request::get('id');
        $state = \Request::get('state');
        $matchid = \Request::get('matchid');
		$matchtype = \Request::get('matchtype');
	$str="";
		if ($state == 'true')
			\Session::push('search_'.$matchtype.'_'.$matchid, $id);
		else {
			if ($id < 0) {
						\Session::forget('search_'.$matchtype.'_'.$matchid); 				
			} elseif (\Session::has('search_'.$matchtype.'_'.$matchid)) {
				foreach (\Session::get('search_'.$matchtype.'_'.$matchid) as $key => $value) {

					if ($value === $id) {
						$str .= $value;
						\Session::forget('search_'.$matchtype.'_'.$matchid.'.'.$key); 
//						break;
					}
				}
			}
		}

			if (\Session::has('search_'.$matchtype.'_'.$matchid)) 
			    return count(\Session::get('search_'.$matchtype.'_'.$matchid));
        return 0;
 
	}

}
