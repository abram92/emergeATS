@extends('layouts.admin')

@section('title', 'Candidate Search')

@php
$isExpanded = (isset($query['search_id']) || !$data->isEmpty()) ? false : true;
@endphp

@section('contentsearch')
       <form class="form-horizontal searchForm candidate" method="post" role="form" action="{{ url('candidates/search') }}">
			@csrf
 				@if ($jobad)
					<input type="hidden" name="jobid" value="{{ $jobad->id }}">
				@endif

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[name]',
														'filterVar'=> ((isset($q['name'])) ? $q['name'] : null),
														'filterPlaceholder'=> 'Filter Candidate Name',
														'advancedFilter'=>true, 
														'filterPrefix'=>'cndFilter', 
														'isExpanded'=>$isExpanded,
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> Auth::user()->hasPermissionTo('data-export')])							


	<div id="cndFilterBody" class="searchFilters searchForm candidate collapse @if($isExpanded) show @endif" aria-labelledby="cndFilterHeading">
		<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">
			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants', 
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])
					</div>
					<div class="col-md-3">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Statuses', 
									'fieldplaceholder'=>'Choose Statuses', 
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])
					</div>
					<div class="col-md-3">
@include('partials.select2_filter_dropdown_single', ['fieldname'=>'q[availability]', 'fieldlabel'=>'Availability', 
									'fieldplaceholder'=>'Choose Availability', 
									'options'=>$allavailabilities,
									'selectedoptions'=>old('q[availability]', isset($q['availability']) ? $q['availability'] : null)])
					</div>
					
				</div>	
				<div class="row">
					<div class="col-md-3">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[eestatuses][]', 'fieldlabel'=>'EE Statuses', 
									'fieldplaceholder'=>'Choose EE Statuses', 
									'options'=>$alleestatuses,
									'selectedoptions'=>old('q[eestatuses]', isset($q['eestatuses']) ? $q['eestatuses'] : null)])
					</div>					
					<div class="col-md-3">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[levels][]', 'fieldlabel'=>'Candidate Levels', 
									'fieldplaceholder'=>'Choose Levels', 
									'options'=>$alllevels,
									'selectedoptions'=>old('q[levels]', isset($q['levels']) ? $q['levels'] : null)])
					</div>					
					<div class="col-md-2">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[genders][]', 'fieldlabel'=>'Gender', 
									'fieldplaceholder'=>'Choose Genders', 
									'options'=>$allgenders,
									'selectedoptions'=>old('q[genders]', isset($q['genders']) ? $q['genders'] : null)])
					</div>					
					
					<div class="col-md-2">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[ratings][]', 'fieldlabel'=>'Candidate Ratings', 
									'fieldplaceholder'=>'Choose Ratings', 
									'options'=>$allratings,
									'selectedoptions'=>old('q[ratings]', isset($q['ratings']) ? $q['ratings'] : null)])
					</div>
					
					<div class="col-md-2">

@include('partials.select2_filter_dropdown_single', ['fieldname'=>'q[interviewed]', 'fieldlabel'=>'Interviewed', 
									'fieldplaceholder'=>'', 
									'options'=>[''=>'--','t'=>'Yes', 'f'=>'No'],
									'selectedoptions'=>old('q[interviewed]', isset($q['interviewed']) ? $q['interviewed'] : null)])
					</div>
				</div>	
				
				<div class="row search-contact">
					<div class="col-md-6">				
@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile', 
									'fieldplaceholder'=>'Filter Telephone or Mobile', 
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])
					</div>
					<div class="col-md-6">				

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email', 
									'fieldplaceholder'=>'Filter Email', 
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
					</div>
				</div>
				<div class="row search-location">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[locations][]', 'fieldlabel'=>'Current Locations', 
									'fieldplaceholder'=>'Choose Current Locations', 
									'options'=>$alllocations,
									'selectedoptions'=>old('q[locations]', isset($q['locations']) ? $q['locations'] : null)])
					</div>
					<div class="col-md-6">				
									
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[preflocations][]', 'fieldlabel'=>'Preferred Locations', 
									'fieldplaceholder'=>'Choose Preferred Locations', 
									'options'=>$alllocations,
									'selectedoptions'=>old('q[preflocations]', isset($q['preflocations']) ? $q['preflocations'] : null)])
					</div>
				</div>
				
				<div class="row search-jobtitle">
					<div class="col-md-4">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[jobtitles][]', 'fieldlabel'=>'Job Titles', 
									'fieldplaceholder'=>'Choose Job Titles', 
									'options'=>$alljobtitles,
									'selectedoptions'=>old('q[jobtitles]', isset($q['jobtitles']) ? $q['jobtitles'] : null)])
					</div>
					<div class="col-md-4">				
									
@include('partials.filter_textarea', ['fieldname'=>'q[actualjobtitles]', 'fieldlabel'=>'Actual Job Titles (1 per line)', 
									'fieldplaceholder'=>'Filter Job Titles', 
									'fieldvalue'=>(isset($q['actualjobtitles'])) ? $q['actualjobtitles']:''])
					</div>
					<div class="col-md-4">
									
@include('partials.filter_textarea', ['fieldname'=>'q[jobtitleexclude]', 'fieldlabel'=>'Job Titles to exclude (1 per line)', 
									'fieldplaceholder'=>'Filter Excluded Job Titles', 
									'is_exclude' => true,
									'fieldvalue'=>(isset($q['jobtitleexclude'])) ? $q['jobtitleexclude']:''])
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
			</div>	
			<div class="card w-100">
@include('partials.filter_longtext_note')
				<div class="card-body">



@include('partials.filter_text_input', ['fieldname'=>'q[idealjob]', 'fieldlabel'=>'Ideal Job', 
									'fieldplaceholder'=>'Filter Ideal Job', 
									'fieldvalue'=>(isset($q['idealjob'])) ? $q['idealjob']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[summary]', 'fieldlabel'=>'Summary', 
									'fieldplaceholder'=>'Filter Summary', 
									'fieldvalue'=>(isset($q['summary'])) ? $q['summary']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[agencynotes]', 'fieldlabel'=>'Agency Notes', 
									'fieldplaceholder'=>'Filter Agency Notes', 
									'fieldvalue'=>(isset($q['agencynotes'])) ? $q['agencynotes']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[skills]', 'fieldlabel'=>'Core Skills', 
									'fieldplaceholder'=>'Filter Skills', 
									'fieldvalue'=>(isset($q['skills'])) ? $q['skills']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[textcv]', 'fieldlabel'=>'CV', 
									'fieldplaceholder'=>'Filter CV', 
									'fieldvalue'=>(isset($q['textcv'])) ? $q['textcv']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[interviewnotes]', 'fieldlabel'=>'Interview Notes', 
									'fieldplaceholder'=>'Filter Interview Notes', 
									'fieldvalue'=>(isset($q['interviewnotes'])) ? $q['interviewnotes']:''])
						
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
		<div class="card card-header candidate">
			<div class="row">
				<div class="col-md-8">
				
					<h3>{{ __('Candidate Search') }}</h3>
				@if ($jobad)
					<h5>Selected Job : <span class="text-bold">{{ $jobad->jobref }}</span></h5>
					
					@endif
				</div>
				<div class="col-md-4 float-right">
				</div>
			</div>
		</div>

		
<div class="container col-md-12 p-0">
@include('candidates.results')
</div>


@endsection 

@push('scripts')
<script src="{{ asset('js/jquery.collapser.min.js') }}" rel="javascript"></script>
@include('scripts.src_select2')
@include('scripts.src_markjs')
@endpush

@section('js')

    <script>
		$("document").ready(function() {

@include('scripts.ready_select2')

			$("#baseSearch").on("keyup", function () {
				var value = $(this).val().toLowerCase();
				$("#baseTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});

	@include('partials.searchfilters.ajax_checkbox', ['model'=>isset($jobad) ? $jobad : null, 'matchtype'=>'cand'])
			
				$('.p1').collapser({
		mode: 'lines',
		truncate: 20
	});
			
	@include('scripts.ready_select2_form_reset');
	
	@include('scripts.ready_markjs', ['fields'=>"['skills', 'agencynotes', 'summary', 'idealjob']"]);
		});
		
    </script>
	
@endsection