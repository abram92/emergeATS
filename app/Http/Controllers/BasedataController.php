<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class BasedataController extends Controller
{
    protected $model;
    protected $validator;
	protected $sortorder;
	protected $recordsPerPage;
	
    public function index(Request $request)
    {
		$queryFilter = [];
		$q = $request->get('q');
		
//        return \View::make($this->getFullRouteName('index'))
//            ->with('items', $this->model->orderBy('sort_seq','ASC')->orderBy('description','ASC')->paginate(10));
          $result = $this->model->newQuery();
		 $sortable = false;
		 if (!$q && $this->model->isFillable('sort_seq'))
			 $sortable = true;
//echo $sortable;		 
//		  dd();
		  if ($q) {
			 $result->where('description', 'ILIKE', '%'.$q.'%');
			 $queryFilter['Description'] = $q;
		  }
//		  if ($request->

		  
		  if ($this->recordsPerPage)
			  $items = $result->paginate($this->recordsPerPage);
		  else
			  $items = $result->get();

//		  dd($res);
$query = $q ? ['q'=>$q] : [];
//         return \View::make($this->getFullRouteName('index'))
//            ->with('items', $res)->withQuery($query);
			return view($this->getFullRouteName('index'), compact('items', 'sortable', 'queryFilter'))->withQuery($query);
    }
	
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->model->find($id);

        return view($this->getFullRouteName('show'),compact('item'));
    }
	
    public function edit($id)
    {
        $data = array_merge($this->formData($id), [
            'item' => $this->model->find($id)
        ]);
        return \View::make($this->getFullRouteName('edit'), $data);
    }
	
    public function create()
    {
        $data = array_merge($this->formData(), [
            'item' => new $this->model
        ]);
        return \View::make($this->getFullRouteName('create'), $data);
    }
	
    public function store()
    {

        if ($this->validator->fails()) {
            return $this->redirectToSubRoute()
                ->withErrors($this->validator->errors());
        }
		$fields = $this->data();
		if ($this->model->isFillable('sort_seq')) {
			$sortseq = $this->model->max('sort_seq') + 1;
			$fields['sort_seq'] = $sortseq;
		}
		if (\Request::has('colour_no')) {
			$fields['colour_hex'] = '';
		}		
        $this->model->create($fields);
        return $this->redirectToSubRoute('index')
            ->with('success_message', 'Saved '.$this->basedataClass());
    }
	
    public function update($id)
    {

        if ($this->validator->fails()) {
            return \Redirect::back()
                ->withErrors($this->validator->errors());
        }
		$fields = $this->data();
		if (\Request::has('colour_no')) {
			$fields['colour_hex'] = '';
		}		
           $this->model->find($id)->update($fields);

           return $this->redirectToSubRoute('index')
            ->with('success_message', 'Updated '.$this->basedataClass());
    }
	
    public function destroy($id)
    {
		if ($this->model->find($id)->delete())
        return $this->redirectToSubRoute('index')
            ->with('success_message', 'Deleted '.$this->basedataClass());
		else	
        return $this->redirectToSubRoute('index')
            ->with('error_message', 'Unable to delete '.$this->basedataClass());
    }
	
	
	
	public function updateOrder()
    {
 //       $items = $this->model->orderBy('sort_seq', 'ASC')->get();
        $id = \Request::get('id');
        $sort_seq = \Request::get('sort_seq');
//		var_dump($id);
//		var_dump($sort_seq);
 //       foreach ($items as $item) {
            return $this->model->find($id)->update(array('sort_seq' => $sort_seq)) ? '1' : '0';
 //       }
    }
    /**
     * Returns a structured route name based on the user and basedata types.
     *
     * @param $subroute
     * @return \Illuminate\Routing\Redirector
     */
    protected function redirectToSubRoute($subroute = null)
    {
        if (is_null($subroute)) {
            return \Redirect::back();
        }
        return \Redirect::route($this->getFullRouteName($subroute));
    }
    /**
     * @param $subroute
     * @return string
     */
    protected function getFullRouteName($subroute)
    {
        $role = $this->userRole();
        $basedata_name = $this->basedataName();
        return "{$role}.{$basedata_name}.{$subroute}";
    }
    /**
     * Inject any values needed by the form here
     *
     * @param $entity_id
     * @return array
     */
    protected function formData($entity_id = null)
    {
        return [];
    }
    /**
     * @return string
     */
    abstract protected function userRole();
    /**
     * @return string
     */
    abstract protected function basedataName();
	
	abstract protected function basedataClass();
	
	protected function data() {
		$rules = $this->validator->getRules();
	
		return \Request::only(array_keys($rules));
	}
}