@extends('layouts.admin')

@section('title', 'Job Report')

@php
$isExpanded = (!$data->isEmpty()) ? false : true;
@endphp

@section('contentsearch')
       <form class="form-horizontal searchForm" method="post" role="form" action="{{ url('reports/jobs') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[jobref]',
														'filterVar'=> ((isset($q['jobref'])) ? $q['jobref'] : null),
														'filterPlaceholder'=> 'Filter Job Ref',
														'advancedFilter'=>true, 
														'filterPrefix'=>'jbdFilter', 
														'isExpanded'=>$data->isEmpty(),
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> Auth::user()->hasPermissionTo('data-export')])									

		<div id="jbdFilterBody" class="searchFilters searchForm collapse @if($isExpanded) show @endif" aria-labelledby="jbdFilterHeading">
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
				<div class="row">
					<div class="col-md-4 search-jobtitle">

@include('partials.filter_textarea', ['fieldname'=>'q[jobtitles]', 'fieldlabel'=>'Job Titles (1 per line)', 
									'fieldplaceholder'=>'Filter Job Titles', 
									'fieldvalue'=>(isset($q['jobtitles'])) ? $q['jobtitles']:''])

					</div>
					<div class="col-md-4 search-location">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[locations][]', 'fieldlabel'=>'Locations',
									'fieldplaceholder'=>'Select Locations', 
									'options'=>$alllocations,
									'selectedoptions'=>old('q[locations]', isset($q['locations']) ? $q['locations'] : null)])
					</div>
					<div class="col-md-4">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[eestatuses][]', 'fieldlabel'=>'EE Statuses', 
									'fieldplaceholder'=>'Choose EE Statuses', 
									'options'=>$alleestatuses,
									'selectedoptions'=>old('q[eestatuses]', isset($q['eestatuses']) ? $q['eestatuses'] : null)])
					</div>
				</div>	
				<div class="row search-client">
					<div class="col-md-6">				
@include('partials.filter_text_input', ['fieldname'=>'q[clientname]', 'fieldlabel'=>'Client Name', 
									'fieldplaceholder'=>'Filter Client Name', 
									'fieldvalue'=>(isset($q['clientname'])) ? $q['clientname']:''])
					</div>
					<div class="col-md-6">
									
@include('partials.filter_textarea', ['fieldname'=>'q[clientexclude]', 'fieldlabel'=>'Clients to exclude (1 per line)', 
									'fieldplaceholder'=>'Filter Excluded Clients', 
									'is_exclude' => true,
									'fieldvalue'=>(isset($q['clientexclude'])) ? $q['clientexclude']:''])
					</div>
				</div>
				
				<div class="row search-contact">
					<div class="col-md-4">				
@include('partials.filter_text_input', ['fieldname'=>'q[contact][name]', 'fieldlabel'=>'Contact Name', 
									'fieldplaceholder'=>'Filter Contact Name', 
									'fieldvalue'=>(isset($q['contact']['name'])) ? $q['contact']['name']:''])
					</div>
					<div class="col-md-4">				
@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile', 
									'fieldplaceholder'=>'Filter Telephone or Mobile', 
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])
					</div>
					<div class="col-md-4">				

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email', 
									'fieldplaceholder'=>'Filter Email', 
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
					</div>
				</div>
				

				<div class="row search-salary">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[salarycategories][]', 'fieldlabel'=>'Salary Categories', 
									'fieldplaceholder'=>'Choose Salary Categories', 
									'options'=>$allsalarycategories,
									'selectedoptions'=>old('q[salarycategories]', isset($q['salarycategories']) ? $q['salarycategories'] : null)])
					</div>
					<div class="col-md-3">
									
@include('partials.filter_number', ['fieldname'=>'q[minsalary]', 'fieldlabel'=>'Minimum Salary', 
									'fieldplaceholder'=>'Filter Minimum Salary', 
									'fieldvalue'=>(isset($q['minsalary'])) ? $q['minsalary']:''])
					</div>
					<div class="col-md-3">

@include('partials.filter_number', ['fieldname'=>'q[maxsalary]', 'fieldlabel'=>'Maximum Salary', 
									'fieldplaceholder'=>'Filter Maximum Salary', 
									'fieldvalue'=>(isset($q['maxsalary'])) ? $q['maxsalary']:''])
					</div>
				</div>
				<div class="row search-dates">
@include('partials.filter_daterange', ['fieldlabel'=>'Upload Date', 
									'fieldname_from'=>'q_upload_from', 
									'fieldvalue_from'=>(isset($q['upload_from'])) ? $q['upload_from']:null,
									'fieldname_to'=>'q_upload_to', 
									'fieldvalue_to'=>(isset($q['upload_to'])) ? $q['upload_to']:null])				
				</div>						

			<div class="card w-100">
@include('partials.filter_longtext_note')
				<div class="card-body">

@include('partials.filter_text_input', ['fieldname'=>'q[summary]', 'fieldlabel'=>'Summary', 
									'fieldplaceholder'=>'Filter Summary', 
									'fieldvalue'=>(isset($q['summary'])) ? $q['summary']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[agencynotes]', 'fieldlabel'=>'Agency Notes', 
									'fieldplaceholder'=>'Filter Agency Notes', 
									'fieldvalue'=>(isset($q['agencynotes'])) ? $q['agencynotes']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[projectplan]', 'fieldlabel'=>'Project Plan', 
									'fieldplaceholder'=>'Filter Project Plan', 
									'fieldvalue'=>(isset($q['projectplan'])) ? $q['projectplan']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[skills]', 'fieldlabel'=>'Skills', 
									'fieldplaceholder'=>'Filter Skills', 
									'fieldvalue'=>(isset($q['skills'])) ? $q['skills']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[fulldescription]', 'fieldlabel'=>'Description', 
									'fieldplaceholder'=>'Filter Description', 
									'fieldvalue'=>(isset($q['fulldescription'])) ? $q['fulldescription']:''])
				</div>
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
					<h3>{{ __('Job Report') }}</h3>
				</div>

			</div>
		</div>


@include('reports.jobresults')



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