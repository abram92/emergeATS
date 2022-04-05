@extends('layouts.admin')

@section('contentsearch')
       <form class="form-horizontal searchForm report" method="post" role="form" action="{{ url('reports/useractivity') }}">
			@csrf
						
@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[username]',
														'filterVar'=> ((isset($q['username'])) ? $q['username'] : null),
														'filterPlaceholder'=> 'Filter Username',
														'advancedFilter'=>true, 
														'filterPrefix'=>'filter', 
														'isExpanded'=>$data->isEmpty(),
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> Auth::user()->hasPermissionTo('data-export')])						

			
		<div id="filterBody" class="searchFilters searchForm report collapse @if($data->isEmpty()) show @endif" aria-labelledby="filterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-12">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])
					</div>
				</div>		
			</div>	
				<div class="card-header w-100">Dates</div>
				<div class="card card-body w-100">
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Activity Date', 
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
		<div class="card card-header user" >
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __('User Activity') }}</h3>
				</div>
			</div>
		</div>

@include('reports.useractivityresults')


@endsection 

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
@parent
    <script>
		$("document").ready(function() {

@include('scripts.ready_select2')
		$("#filterHeading").click(function() {

					
		});			
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