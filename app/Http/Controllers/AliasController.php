<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Alias;
use App\AliasCategory;
use App\AliasKeyword;

class AliasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		$queryFilter = [];
		$q = $request->get('q');

		$srchid = $request->get('srch');
		if ($srchid) {
			$q = $this->decryptFilter($srchid);
		}
		
		$categories = AliasCategory::get()->pluck('description', 'id');
		
        $result = Alias::with('category')->with('keywords');

		if (isset($q['alias']) && ($q['alias'])) {
			 $result->where('description', 'ILIKE', '%'.$q['alias'].'%');
			 $queryFilter['Alias'] = $q['alias'];
		}

		if (isset($q['keyword']) && ($q['keyword'])) {
//			 $result->where(DB::raw("phrase"), 'ILIKE', '%'.str_replace(' ', '%', $q['keyword']).'%');
			$r = $q['keyword'];
			$queryFilter['Keywords'] = $r;					
			$result->WhereHas("keywords", function ($q) use ($r) {
					$q->where('keyword', 'ILIKE', '%'.$r.'%');
			});
		}
		if (isset($q['categories']) && (!empty($q['categories']))) {
			$r = $q['categories'];
			$queryFilter['Categories'] = $categories->only($r)->implode(', ');						
			$result->WhereHas("category", function ($q) use ($r) {
					$q->whereIn('id', $r); //$q['roles']);
    });
		}

        $data = $result->orderBy('description','ASC')->paginate(25);
//		$query = $q ? ['q'=>$q] : [];

		$query = $q ? ['srch'=>$this->encryptFilter($q)] : [];

        return view('admin.aliases.index',compact('data', 'categories', 'queryFilter', 'q'))
            ->withQuery($query); //('i', ($request->input('page', 1) - 1) * 25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		$categories = AliasCategory::get()->pluck('description', 'id');

			
        return view('admin.aliases.create',compact('categories'));
		
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
            'description' => 'required|iunique:aliases,description',
			'alias_category_id' => 'exists:alias_categories,id',
			'keywords' => 'required'
        ]);


        $input = $request->all();
		
		$success = false;
		DB::beginTransaction();
		try {

			$alias = Alias::create($input);
			if ($alias) {
				$this->setKeywords($request, $alias);
		
				$success = true;
			}
		} catch (\Exception $e) {
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.aliases.index')
                        ->with('success_message','Alias created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Alias not created');
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
        $alias = Alias::with('category')->with('keywords')->find($id);
		
	
        return view('admin.aliases.show',compact('alias'));
		
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
        $alias = Alias::with('keywords')->find($id);
		$categories = AliasCategory::get()->pluck('description', 'id');
		
        return view('admin.aliases.edit',compact('alias', 'categories'));
		
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
            'description' => 'required|iunique:aliases,description,'.$id,
			'alias_category_id' => 'exists:alias_categories,id',
			'keywords' => 'required'
        ]);

        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$alias = Alias::find($id);
			
			
			if ($alias->update($input)) {
				$this->setKeywords($request, $alias);
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('admin.aliases.index')
                        ->with('success_message','Alias updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Alias not updated');
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
        Alias::find($id)->delete();
        return redirect()->route('admin.aliases.index')
                        ->with('success_message','Alias deleted successfully');
		
    }
	
	
    public function export(Request $request, $lastupdate=null)
    {
        //
		$queryFilter = [];
		$q = $request->get('q');

		
        $result = Alias::with(['keywords'=>function($query){
        $query->select('alias_id', 'keyword');
    }]);

		if ($lastupdate) {
			 $result->where('updated_at', '>=', $lastupdate);
		}

		return response()->json($result->get()->makeHidden('alias_category_id', 'created_at'), 200);

    }
	
	private function setKeywords(Request $request, Alias $alias)
	{
		$keywords = explode("\r\n", $request->input('keywords'));
		
		$alias->keywords()->delete();
		$allkeywords = [];
		foreach($keywords as $keyword) {
			if (!trim($keyword))
				continue;
			$n = new AliasKeyword;
			$n->alias_id = $alias->id;
			$n->keyword = $keyword;
			$allkeywords[] = $n->attributesToArray();
		}
		$alias->keywords()->insert($allkeywords);

		return true;
	}	
}
