<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

use DB;

use App\Http\Traits\LookupListTrait;
use App\Http\Traits\DataFileTrait;
use App\Http\Traits\AddressTrait;
use App\Http\Traits\ContactFieldTrait;
use App\Http\Traits\LongFullTextTrait;
use App\Http\Traits\SearchTrait;
use App\Http\Traits\ExportTrait;

use App\Helpers\PaginationHelper;

use App\Candidate;

class ClientController extends Controller
{	
	use LookupListTrait;
	use DataFileTrait;
	use ContactFieldTrait;
	use AddressTrait;
	use LongFullTextTrait;
	use SearchTrait;
	
    public function __construct()
	{
		$this->model_class = 'App\Client';
	}	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		$queryFilter = [];
		$isSaved = false;
		
		$q = $request->get('q');

		$isSearch = $request->get('search');
		$isExport = $request->get('export');

		$saveSearch = false;
		if ($q) {
			$saveSearch = true;
		} else {
			$searchid = $request->get('search_id');
			if ($searchid) {
				$isSearch = true;
				list($q, $isSaved) = $this->getSavedSearch($searchid);
			}
		}

		// get drop downs
        $allstatuses = $this->getClientStatuses();
		$allconsultants = $this->getConsultants();

		if (!$q) {
			$data = collect([]);
		} else {
			if (is_array($q))
				$q = $this->removeEmptyCriteria($q);				
					
			$result = Client::with('status')->with('consultant');

			if (isset($q['name']) && ($q['name'])) {
				$result->where('name', 'ILIKE', '%'.str_replace(' ', '%', $q['name']).'%');
				$queryFilter['Client Name'] = $q['name'];
			}

			if (isset($q['consultants']) && (!empty($q['consultants']))) {
				$result->whereIn('consultant_id', $q['consultants']); 
				$queryFilter['Consultants'] = $allconsultants->only($q['consultants'])->implode(', ');
			}
		
			if (isset($q['statuses']) && (!empty($q['statuses']))) {
				$result->whereIn('status_id', $q['statuses']); 
				$queryFilter['Statuses'] = $allstatuses->pluck('description', 'id')->only($q['statuses'])->implode(', ');
			}

			if (isset($q['contact'])) {

				$name = isset($q['contact']['name']) ? $q['contact']['name'] : null;
				$position = isset($q['contact']['position']) ? $q['contact']['position'] : null;
				$phone = isset($q['contact']['phone']) ? $q['contact']['phone'] : null;
				$email = isset($q['contact']['email']) ? $q['contact']['email'] : null;

				if ($name || $position || $phone || $email) {
					
					$result->whereHas('staff', function($contactQuery) use ($name, $position, $phone, $email, &$queryFilter) {
						
						$this->clientContactConditional($contactQuery, $name, $position, $phone, $email, $queryFilter);

					})->with(['staff' => function($contactQuery) use ($name, $position, $phone, $email)	{
						$tmp = [];
						$this->clientContactConditional($contactQuery, $name, $position, $phone, $email, $tmp);
					
					}] );
				}
			}
		
	
// long text	
			$fulltextfields = ['techenvironment'=>['var'=>'techenvironment', 'label'=>'Tech Environment'],
							'agencynotes'=>['var'=>'agencynotes', 'label'=>'Agency Notes'],
							];
							
			foreach ($fulltextfields as $relation => $html) {
				$htmlvar = $html['var'];
				$htmllabel = $html['label'];
				if (isset($q[$htmlvar]) && (!empty($q[$htmlvar]))) {
					$r = $this->FTS_setLongTextQueryString($q[$htmlvar]);
					$result->WhereHas($relation, function ($q) use ($r) {
//						$q->where(DB::raw("chunk_tokens @@ to_tsquery(".$r.")"), '=', 'true');
						$q->whereRaw($this->FTS_getFullTextClauseSyntax($r));
					});
					$queryFilter[$htmllabel] = $q[$htmlvar];
					
				}
			}			
 		
			$result->orderBy('name','ASC');

			if($isSearch) {
				$data = $result->paginate(25);
			}
//dd($result->toSql());
			if ($isExport) {	
				$data = $result->get();
			}
 
		}
		
		$candidateid = $request->get('candid');
		
		if ($candidateid)
			$candidate = Candidate::with('user')->with('agencynotes')->find($candidateid);
		else
			$candidate = null;

		if ($saveSearch)
			$searchid = $this->storeSearch($q, $queryFilter, 'client');
		
		$query = $searchid ? ['search_id'=>$searchid] : [];
						
        return view('clients.index',compact('allstatuses', 'allconsultants', 'data', 'searchid', 'q', 'candidate', 'queryFilter', 'isSaved'))->withQuery($query);
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
        $statuses = $this->getClientStatuses();
		$consultants = $this->getConsultants();

		$contact_types = $this->getContactFieldTypes();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
			
        return view('clients.create',compact('statuses', 'consultants', 'contact_types'));
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
		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
 
		$this->validate($request, [
            'name' => 'required|iunique:clients,name',
			'status_id' => 'required|exists:client_statuses,id',
			'consultant_id' => 'required|exists:users,id',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'techenvironment' => 'required'
        ]);


        $input = $request->all();
		
		$success = false;
		DB::beginTransaction();
		try {

			$client = Client::create($input);
			if ($client) {
				$this->setLongFullTextFields($request, $client);
		
				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $client;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}

				$addressData = $request->input('addresses');
				if (!empty($addressData)) {
					$this->addressmodel = $client;
					$this->updateOrCreateAddresses($request->input('addresses'), $client);
				}
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.show', $client->id)
                        ->with('success_message','Client created successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Client not created');
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
		
        $client = Client::with('status')->with('consultant')->find($id);
        $icons = $this->getContactFieldTypeIcons();
		$documents = $client->documents()->get();
		$staff = $client->staff()->get()->sortBy('id');
		$jobs = $client->jobs()->with('clientcontacts')->with('locations')->with('consultant')->with('status')->get()->sortBy('id');

		$contacts = $client->contactfields()->get()->sortBy('contact_field_type_id');
		
		$addresses = $client->addresses()->get()->sortBy('id');
		
		$jobapplications = $client->jobapplications;
		
		$directapplications = $client->directapplications;
		
		$audit = $client->audits()->with(array('user'=>function($query){
												$query->withTrashed();
											}))->orderBy('created_at', 'DESC')->limit(10)->get();
											
        $statuses = $this->getClientStatuses();

        return view('clients.show',compact('client', 'icons', 'documents', 'contacts', 'staff', 'addresses', 'jobs', 'jobapplications', 'directapplications', 'audit', 'statuses'));
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
        $client = Client::with('agencynotes')->with('techenvironment')->find($id);
        $statuses = $this->getClientStatuses();
		$consultants = $this->getConsultants();
		$contact_types = $this->getContactFieldTypes();
 		$contact_types = $contact_types->map->only(["id","name","fontawesome_icon","type"]);
//		$agencynotes = $client->agencynotes();
//		dd($agencynotes);
		$contacts = $client->contactfields()->get()->sortBy('contact_field_type_id')->sortBy('contact_field_type_id');
		$addresses = $client->addresses()->get()->sortBy('id');
		
        return view('clients.edit',compact('client', 'statuses', 'consultants', 
											'contact_types', 
											'contacts', 'addresses'));
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

		$contactfields = ($request->input('contacts') !== null) ? $request->input('contacts') : [];
		$request->merge(['emailfields' => $this->extractSubmittedEmailFields($contactfields)]);
 
		$this->validate($request, [
            'name' => 'required|iunique:clients,name,'.$id,
			'status_id' => 'required|exists:client_statuses,id',
			'consultant_id' => 'required|exists:users,id',
            'emailfields.*' => 'email:'.config('constants.emailvalidrules'),
			'techenvironment' => 'required'
        ]);
		
        $input = $request->all();

		$success = false;
		DB::beginTransaction();
		try {

			$client = Client::find($id);
			
			$client->edit_comment = isset($input['comments']) ? $input['comments'] : "";
			
			if ($client->update($input)) {
		
				$this->setLongFullTextFields($request, $client);
		

				$contactData = $request->input('contacts');
				if (!empty($contactData)) {
					$this->contactfieldmodel = $client;
					$this->updateOrCreateContactFields($request->input('contacts'));
				}

				$addressData = $request->input('addresses');
				if (!empty($addressData)) {
					$this->addressmodel = $client;
					$this->updateOrCreateAddresses($request->input('addresses'));
				}
				$success = true;
			}
		} catch (\Exception $e) {
			
			
		}
		if ($success) {		
			DB::commit();
			return redirect()->route('clients.index')
                        ->with('success_message','Client updated successfully');
		} else {
			DB::rollback();
			return redirect()->back()
                        ->with('error_message','Client not updated');
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
        Client::find($id)->delete();
        return redirect()->route('clients.index')
                        ->with('success_message','Client deleted successfully');
    }
	
	private function setLongFullTextFields(Request $request, Client $client)
	{
		$longtextfields = [];
		if ($request->has('techenvironment')){
			$longtextfields['techenvironment'] = ['relation'=>'techenvironment', 'value'=>strval($request->input('techenvironment'))];
		}
		if (($request->has('agencynotes')) && !empty($request->input('agencynotes'))){
			$longtextfields['agencynotes'] = ['relation'=>'agencynotes', 'value'=>strval($request->input('agencynotes')), 'multi' => ['created_at'=>null]];
		}		
		$this->longtextfullmodel = $client;
		return $this->updateOrCreateLongFullTextFields($longtextfields);
	}


    public function getAutocompleteData(Request $request){
   
        if($request->has('term')){
			$r = $request->input('term');

            $clients = Client::select(['id', 'name as text'])->where('name', 'ILIKE', str_replace(' ', '%', $r).'%')->orderBy('name')->paginate(100)->toArray();

			$response = [];
			$response['results'] = $clients['data'];
			$response['pagination']['more'] = ($clients['current_page'] < $clients['last_page']);

			return json_encode($response);

        }
    }
	
	public function getJobApplications($id, Request $request){
		
		$pageNo = $request->has('page') ? $request->input('page') : 1;
		$perPage = $request->has('perpage') ? $request->input('perpage') : 10;
		
		$data = Client::find($id)->jobapplications; // ->forPage($pageNo, $perPage);
		
		$paginated = PaginationHelper::paginate($data, $perPage);

    return view('test.clients', compact('paginated'));
		return json_encode($data);
	}
	
}
