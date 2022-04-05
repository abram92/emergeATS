@extends('layouts.admin')

@section('contentsearch')
       <form class="form-horizontal searchForm" method="post" role="form" action="{{ url('reports/candidates') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[name]',
														'filterVar'=> ((isset($q['name'])) ? $q['name'] : null),
														'filterPlaceholder'=> 'Filter Candidate Name',
														'advancedFilter'=>true, 
														'filterPrefix'=>'cndFilter', 
														'isExpanded'=>$data->isEmpty(),
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> Auth::user()->hasPermissionTo('data-export')])									

		<div id="cndFilterBody" class="searchFilters searchForm collapse @if($data->isEmpty()) show @endif" aria-labelledby="cndFilterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants', 
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
			</div>	
				<div class="card-header w-100">Dates</div>
				<div class="card card-body w-100">
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Upload Date', 
									'fieldname_from'=>'q_upload_from', 
									'fieldvalue_from'=>(isset($q['upload_from'])) ? $q['upload_from']:null,
									'fieldname_to'=>'q_upload_to', 
									'fieldvalue_to'=>(isset($q['upload_to'])) ? $q['upload_to']:null])				
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

		<div class="card card-header candidate" >
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __('Candidate Report') }}</h3>
				</div>
			</div>
		</div>

@include('reports.candidateresults')



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