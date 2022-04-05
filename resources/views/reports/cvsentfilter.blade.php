@extends('layouts.admin')

@section('contentsearch')
       <form class="form-horizontal searchForm" method="post" role="form" action="{{ url('reports/cvsent') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[subject]',
														'filterVar'=> ((isset($q['subject'])) ? $q['subject'] : null),
														'filterPlaceholder'=> 'Filter Subject',
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
					</div>
				</div>		
				<div class="row">
					<div class="col-md-6">
									
@include('partials.filter_textarea', ['fieldname'=>'q[candidatenames]', 'fieldlabel'=>'Candidates (1 per line)', 
									'fieldplaceholder'=>'Filter Candidates', 
									'fieldvalue'=>(isset($q['candidatenames'])) ? $q['candidatenames']:''])
					</div>
					<div class="col-md-6">
									
@include('partials.filter_textarea', ['fieldname'=>'q[clientnames]', 'fieldlabel'=>'Clients (1 per line)', 
									'fieldplaceholder'=>'Filter Clients', 
									'fieldvalue'=>(isset($q['clientnames'])) ? $q['clientnames']:''])
					</div>
				</div>
			</div>	
				<div class="card-header w-100">Dates</div>
				<div class="card card-body w-100">
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Sent Date', 
									'fieldname_from'=>'q_sent_from', 
									'fieldvalue_from'=>(isset($q['sent_from'])) ? $q['sent_from']:null,
									'fieldname_to'=>'q_sent_to', 
									'fieldvalue_to'=>(isset($q['sent_to'])) ? $q['sent_to']:null])				
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

		<div class="card card-header email" >
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __('CV Sent Report') }}</h3>
				</div>
			</div>
		</div>

@include('reports.cvsentresults')



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