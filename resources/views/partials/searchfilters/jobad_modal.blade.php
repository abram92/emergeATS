
        <div class="input-group-btn">
            <div class="btn-group" role="group">
@if (isset($query) && isset($query['search_id']))
     <a class="btn" id="savebutton" href="{{ route('savedsearch.edit',$query['search_id']) }}" target="savesearch{{ $query['search_id'] }}">Save Search</a>
@endif	
                <div class="dropdown dropdown-sm">
                  <button type="button" class="btn btn-navbar dropdown-toggle dropdown-toggle-split" data-toggle="modal" data-target="#searchform" id="searchbutton">
                  	<i class="fas fa-search"></i>
				  </button>  
				</div>
			</div>
		</div>	

<div class="modal fade " id="searchform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Job Search Filter</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
            <form class="form-horizontal bg-white" method="post" role="form" action="{{ url('jobs/search') }}">
				<div class="modal-body">@csrf
				@if (isset($candidate))
					<input type="hidden" name="candidateid" value="{{ $candidate->id }}">
				@endif
				
					<div class="card-deck">
						<div class="card card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[clientname]', 'fieldlabel'=>'Client Name', 
									'fieldplaceholder'=>'Filter Client Name', 
									'fieldvalue'=>(isset($q['clientname'])) ? $q['clientname']:''])
									
@include('partials.filter_textarea', ['fieldname'=>'q[clientexclude]', 'fieldlabel'=>'Clients to exclude (1 per line)', 
									'fieldplaceholder'=>'Filter Excluded Clients', 
									'fieldvalue'=>(isset($q['clientexclude'])) ? $q['clientexclude']:''])
									
@include('partials.filter_text_input', ['fieldname'=>'q[refcode]', 'fieldlabel'=>'Reference Code', 
									'fieldplaceholder'=>'Filter Reference Code', 
									'fieldvalue'=>(isset($q['refcode'])) ? $q['refcode']:''])
									
@include('partials.filter_textarea', ['fieldname'=>'q[jobtitles]', 'fieldlabel'=>'Job Titles (1 per line)', 
									'fieldplaceholder'=>'Filter Job Titles', 
									'fieldvalue'=>(isset($q['jobtitles'])) ? $q['jobtitles']:''])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants',
									'fieldplaceholder'=>'Select Consultants', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Job Statuses',
									'fieldplaceholder'=>'Choose Statuses', 
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[eestatuses][]', 'fieldlabel'=>'EE Statuses',
									'fieldplaceholder'=>'Select EE Statuses', 
									'options'=>$alleestatuses,
									'selectedoptions'=>old('q[eestatus]', isset($q['eestatuses']) ? $q['eestatuses'] : null)])

						</div>
						
						<div class="card col-md-6">
							<div class="card-header">Contact</div>
							<div class="card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[contact][name]', 'fieldlabel'=>'Contact Name', 
									'fieldplaceholder'=>'Filter Contact Name', 
									'fieldvalue'=>(isset($q['contact']['name'])) ? $q['contact']['name']:''])


@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile', 
									'fieldplaceholder'=>'Filter Telephone or Mobile', 
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email', 
									'fieldplaceholder'=>'Filter Email', 
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
							</div>
							<div class="card-body">

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[locations][]', 'fieldlabel'=>'Locations',
									'fieldplaceholder'=>'Select Locations', 
									'options'=>$alllocations,
									'selectedoptions'=>old('q[locations]', isset($q['locations']) ? $q['locations'] : null)])


@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[salarycategories][]', 'fieldlabel'=>'Salary Categories',
									'fieldplaceholder'=>'Select Salary Categories', 
									'options'=>$allsalarycategories,
									'selectedoptions'=>old('q[salarycategories]', isset($q['salarycategories']) ? $q['salarycategories'] : null)])

@include('partials.filter_number', ['fieldname'=>'q[minsalary]', 'fieldlabel'=>'Minimum', 
									'fieldplaceholder'=>'Filter Minimum Salary', 
									'fieldvalue'=>(isset($q['minsalary'])) ? $q['minsalary']:''])

@include('partials.filter_number', ['fieldname'=>'q[maxsalary]', 'fieldlabel'=>'Maximum', 
									'fieldplaceholder'=>'Filter Maximum Salary', 
									'fieldvalue'=>(isset($q['maxsalary'])) ? $q['maxsalary']:''])

							</div>
						</div>
					</div>
					
					<div class="card">
					<div class="card-body">
<div class="form-group">
							<label class="col-form-label text-md-right">Upload Date</label>
							<div class="row">
							<label class="col-form-label text-md-right col-md-2">From</label>
								{!! Form::date('q_upload_from', isset($q['upload_from']) ? $q['upload_from'] : null, array('placeholder' => '','class' => 'form-control col-md-4', '', '', 'autocomplete')) !!}
							<label class="col-form-label text-md-right col-md-2">To </label>
								{!! Form::date('q_upload_to', isset($q['upload_to']) ? $q['upload_to'] : null, array('placeholder' => '','class' => 'form-control col-md-4', '', '', 'autocomplete')) !!}
							</div>
					</div>
					</div>
					</div>
					
					<div class="card">
						<div class="card-body">
@include('partials.filter_longtext_note')


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
					</div>				</div>	
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary fas fa-search" >Search</button>
				</div>
			</form>
        </div>
    </div>
</div>

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
@parent

    <script>
	
		$("document").ready(function() {
			
@include('scripts.ready_select2')
			
			$("input[name='q_upload_from']").change(function () {
				$("input[name='q_upload_to']").attr('min', $(this).val());
			});

			$("input[name='q_upload_to']").change(function () {
				$("input[name='q_upload_from']").attr('max', $(this).val());
			});
			
	@include('partials.searchfilters.ajax_checkbox', ['model'=>isset($candidate) ? $candidate : null, 'matchtype'=>'job'])
					
		});
		
		
    </script>
@stop