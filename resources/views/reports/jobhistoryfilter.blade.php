@extends('layouts.admin')

@section('contentsearch')
       <form class="form-horizontal searchForm" method="post" role="form" action="{{ url('reports/jobhistory') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[jobref]',
														'filterVar'=> ((isset($q['jobref'])) ? $q['jobref'] : null),
														'filterPlaceholder'=> 'Filter Job Ref',
														'advancedFilter'=>true, 
														'filterPrefix'=>'jbdFilter', 
														'isExpanded'=>$data->isEmpty(),
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> false])									


		<div id="jbdFilterBody" class="searchFilters searchForm collapse @if($data->isEmpty()) show @endif" aria-labelledby="jbdFilterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Actions Performed By', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])
					</div>
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Statuses', 
									'fieldplaceholder'=>'Choose Statuses', 
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])
					</div>
				</div>		
				<div class="row">
					<div class="col-md-6">
@include('partials.filter_textarea', ['fieldname'=>'q[clientnames]', 'fieldlabel'=>'Actions Involving Clients (1 per line)', 
									'fieldplaceholder'=>'Filter Clients', 
									'fieldvalue'=>(isset($q['clientnames'])) ? $q['clientnames']:''])
					</div>
					<div class="col-md-6">
									
					</div>
				</div>				
			</div>	
				<div class="card-header w-100">Dates</div>
				<div class="card card-body w-100">
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Status Changed Date', 
									'fieldname_from'=>'q_changed_from', 
									'fieldvalue_from'=>(isset($q['changed_from'])) ? $q['changed_from']:null,
									'fieldname_to'=>'q_changed_to', 
									'fieldvalue_to'=>(isset($q['changed_to'])) ? $q['changed_to']:null])				
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

		<div class="card card-header job" >
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __('Job History Report') }}</h3>
				</div>
			</div>
		</div>

@include('reports.jobhistoryresults')



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