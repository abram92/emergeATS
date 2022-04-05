@extends('layouts.admin')
@if ($candidate)
@section('title', __('Link Client to '.$candidate->user->listname))	
@else
@section('title', 'Client Search')
@endif	

@php
$isExpanded = (isset($query['search_id']) || !$data->isEmpty()) ? false : true;
@endphp
@section('contentsearch')
		<form class="form-horizontal searchForm client" method="post" role="form" action="{{ url('clients/search') }}">
				@csrf
 				@if ($candidate)
					<input type="hidden" name="candid" value="{{ $candidate->id }}">
				@endif
@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[name]',
														'filterVar'=> ((isset($q['name'])) ? $q['name'] : null),
														'filterPlaceholder'=> 'Filter Client Name',
														'advancedFilter'=>true, 
														'filterPrefix'=>'clntFilter', 
														'isExpanded'=>$isExpanded,
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> Auth::user()->hasPermissionTo('data-export')])									

		
		<div id="clntFilterBody" class="searchFilters searchForm client collapse @if($isExpanded) show @endif" aria-labelledby="clntFilterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-sm-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])
					</div>
					<div class="col-sm-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Statuses', 
									'fieldplaceholder'=>'Choose Statuses', 
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])
					</div>
				</div>		
				<div class="row search-contact">
					<div class="col-md-6">
@include('partials.filter_text_input', ['fieldname'=>'q[contact][name]', 'fieldlabel'=>'Contact Name', 
									'fieldplaceholder'=>'Filter Contact Name', 
									'fieldvalue'=>(isset($q['contact']['name'])) ? $q['contact']['name']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][position]', 'fieldlabel'=>'Contact Job Title', 
									'fieldplaceholder'=>'Filter Job Title', 
									'fieldvalue'=>(isset($q['contact']['position'])) ? $q['contact']['position']:''])
					</div>
					<div class="col-md-6">				
@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile', 
									'fieldplaceholder'=>'Filter Telephone or Mobile', 
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email', 
									'fieldplaceholder'=>'Filter Email', 
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
					</div>
				</div>
			</div>	
			<div class="card w-100">
@include('partials.filter_longtext_note')
				<div class="card-body">

@include('partials.filter_text_input', ['fieldname'=>'q[techenvironment]', 'fieldlabel'=>'Tech Environment', 
									'fieldplaceholder'=>'Filter Tech Environment', 
									'fieldvalue'=>(isset($q['techenvironment'])) ? $q['techenvironment']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[agencynotes]', 'fieldlabel'=>'Agency Notes', 
									'fieldplaceholder'=>'Filter Agency Notes', 
									'fieldvalue'=>(isset($q['agencynotes'])) ? $q['agencynotes']:''])
				</div>
			</div>
						</div>
						@include('partials.form_reset')
		</div>
		</form>
<div class="topbar-divider d-none d-sm-block"></div>
		
@stop
@section('content')

@include('partials.flashmessages')
		<div class="card card-header client" >
			<div class="row">
				<div class="col-md-8">
					<h3>{{ __('Client Search') }}</h3>
				@if ($candidate)
					<h5>Selected Candidate : <span class="text-bold">{{ $candidate->user->listname }}</span></h5>				
				@if ($candidate->agencynotes)
					<button class="btn fa fa-sticky-note" data-container="body" data-placement="bottom" data-toggle="popover" title="" data-content="{{ $candidate->agencynotes->chunk }}" title="Agency Notes"></button>
					@endif
					@endif					
				</div>
				<div class="col-md-4">
				</div>
			</div>
		</div>



@include('clients.results')



@endsection 

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
@parent
    <script>
		$("document").ready(function() {

@include('scripts.ready_select2')
	
			$("#baseSearch").on("keyup", function () {
				var value = $(this).val().toLowerCase();
				$("#baseTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
			
@include('scripts.ready_select2_form_reset');
		});
		
		
    </script>
	
@stop