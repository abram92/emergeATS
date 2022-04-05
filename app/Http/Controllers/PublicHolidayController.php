<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\PublicHoliday;

class PublicHolidayController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		
//		$q = $request->get('q');
		
        $result = PublicHoliday::orderBy('holiday_date','ASC')->get();

//		if (isset($q['name']) && ($q['name'])) {
//			 $result->where('description', 'ILIKE', '%'.$q['name'].'%');
//		}

 //       $data = $result->paginate(25);
		
		list($recurring, $specific) = $result->partition(function($item){
			return $item->recurring;
		});
		
		$yearSpecific = $specific->groupBy(function($item, $key) {
			return substr($item->holiday_date, 0, 4);
		});
//		dd($yearSpecific);
        return view('admin.publicholidays.index',compact('recurring', 'yearSpecific'))
            ->with('i', ($request->input('page', 1) - 1) * 25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		
        return view('admin.publicholidays.create');
		
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
        $this->validate($request, [
            'description' => 'required|iunique:public_holidays,description',
        ]);


		$request->merge(array("recurring" => $request->has("recurring") ? true :false));
        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$publicholiday = PublicHoliday::create($input);
			$success = true;
		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.publicholidays.index')
                        ->with('success_message','PublicHoliday created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','PublicHoliday not created');
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
        $publicholiday = PublicHoliday::find($id);
		
	
        return view('admin.publicholidays.show',compact('publicholiday'));
		
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
        $publicholiday = PublicHoliday::find($id);
		
        return view('admin.publicholidays.edit',compact('publicholiday'));
		
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
            'description' => 'required|iunique:public_holidays,description,'.$id,
        ]);

		$request->merge(array("recurring" => $request->has("recurring") ? true :false));
        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$publicholiday = PublicHoliday::find($id);
				
			if ($publicholiday->update($input)) {
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.publicholidays.index')
                        ->with('success_message','PublicHoliday updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','PublicHoliday not updated');
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
        PublicHoliday::find($id)->delete();
        return redirect()->route('admin.publicholidays.index')
                        ->with('success_message','PublicHoliday deleted successfully');
		
    }
	
	
	

	
}