
        <div class="input-group-btn">
            <div class="btn-group" role="group">
@if (isset($query) && isset($query['search_id']))
     <a class="btn " id="savebutton" href="{{ route('savesearch.edit',$query['search_id']) }}" target="savedsearch{{ $query['search_id'] }}"><i class="fas fa-save"></i>Save Search</a>
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
            <form class="form-horizontal bg-white" method="post" role="form" action="{{ url('candidates/search') }}">
			<div class="modal-header candidate sticky-top">
				<h4 class="modal-title">Candidate Search Filter</h4>
					<button type="submit" class="btn btn-primary fas fa-search float-right" >Search</button>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
				<div class="modal-body">@csrf
					<div class="card-deck">
				@if ($jobad)
					<input type="hidden" name="jobid" value="{{ $jobad->id }}">
				@endif
						<div class="card col-md-6">
						<div class="card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[name]', 'fieldlabel'=>'Name',
									'fieldplaceholder'=>'Filter Name',
									'fieldvalue'=>(isset($q['name'])) ? $q['name']:''])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[consultants][]', 'fieldlabel'=>'Consultants',
									'fieldplaceholder'=>'Select Consultants',
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[consultants]', isset($q['consultants']) ? $q['consultants'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[statuses][]', 'fieldlabel'=>'Statuses',
									'fieldplaceholder'=>'Choose Statuses',
									'options'=>$allstatuses,
									'selectedoptions'=>old('q[statuses]', isset($q['statuses']) ? $q['statuses'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[eestatuses][]', 'fieldlabel'=>'EE Statuses',
									'fieldplaceholder'=>'Choose EE Statuses',
									'options'=>$alleestatuses,
									'selectedoptions'=>old('q[eestatuses]', isset($q['eestatuses']) ? $q['eestatuses'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[ratings][]', 'fieldlabel'=>'Candidate Ratings',
									'fieldplaceholder'=>'Choose Ratings',
									'options'=>$allratings,
									'selectedoptions'=>old('q[ratings]', isset($q['ratings']) ? $q['ratings'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[levels][]', 'fieldlabel'=>'Candidate Levels',
									'fieldplaceholder'=>'Choose Levels',
									'options'=>$alllevels,
									'selectedoptions'=>old('q[levels]', isset($q['levels']) ? $q['levels'] : null)])

@include('partials.select2_filter_dropdown_single', ['fieldname'=>'q[interviewed]', 'fieldlabel'=>'Interviewed',
									'fieldplaceholder'=>'',
									'options'=>[''=>'--','t'=>'Yes', 'f'=>'No'],
									'selectedoptions'=>old('q[interviewed]', isset($q['interviewed']) ? $q['interviewed'] : null)])

						</div>
							<div class="card-header">Job Title</div>
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[jobtitles][]', 'fieldlabel'=>'Job Titles',
									'fieldplaceholder'=>'Choose Job Titles',
									'options'=>$alljobtitles,
									'selectedoptions'=>old('q[jobtitles]', isset($q['jobtitles']) ? $q['jobtitles'] : null)])

@include('partials.filter_textarea', ['fieldname'=>'q[actualjobtitles]', 'fieldlabel'=>'Actual Job Titles (1 per line)',
									'fieldplaceholder'=>'Filter Job Titles',
									'fieldvalue'=>(isset($q['actualjobtitles'])) ? $q['actualjobtitles']:''])
						</div>

						<div class="card col-md-6">
							<div class="card-header">Contact Info</div>
							<div class="card-body">
@include('partials.filter_text_input', ['fieldname'=>'q[contact][phone]', 'fieldlabel'=>'Contact Telephone or Mobile',
									'fieldplaceholder'=>'Filter Telephone or Mobile',
									'fieldvalue'=>(isset($q['contact']['phone'])) ? $q['contact']['phone']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[contact][email]', 'fieldlabel'=>'Contact Email',
									'fieldplaceholder'=>'Filter Email',
									'fieldvalue'=>(isset($q['contact']['email'])) ? $q['contact']['email']:''])
							</div>


							<div class="card-header">Location</div>
							<div class="card-body">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[locations][]', 'fieldlabel'=>'Current Locations',
									'fieldplaceholder'=>'Choose Current Locations',
									'options'=>$alllocations,
									'selectedoptions'=>old('q[locations]', isset($q['locations']) ? $q['locations'] : null)])

@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[preflocations][]', 'fieldlabel'=>'Preferred Locations',
									'fieldplaceholder'=>'Choose Preferred Locations',
									'options'=>$alllocations,
									'selectedoptions'=>old('q[preflocations]', isset($q['preflocations']) ? $q['preflocations'] : null)])
							</div>

							<div class="card-header">Salary</div>
							<div class="card-body">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[salarycategories][]', 'fieldlabel'=>'Salary Categories',
									'fieldplaceholder'=>'Choose Salary Categories',
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


@include('partials.filter_text_input', ['fieldname'=>'q[idealjob]', 'fieldlabel'=>'Ideal Job',
									'fieldplaceholder'=>'Filter Ideal Job',
									'fieldvalue'=>(isset($q['idealjob'])) ? $q['idealjob']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[summary]', 'fieldlabel'=>'Summary',
									'fieldplaceholder'=>'Filter Summary',
									'fieldvalue'=>(isset($q['summary'])) ? $q['summary']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[agencynotes]', 'fieldlabel'=>'Agency Notes',
									'fieldplaceholder'=>'Filter Agency Notes',
									'fieldvalue'=>(isset($q['agencynotes'])) ? $q['agencynotes']:''])

@include('partials.filter_text_input', ['fieldname'=>'q[skills]', 'fieldlabel'=>'Skills',
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
			</form>
        </div>
    </div>
</div>

@push('scripts')
@include('scripts.src_select2')
@include('scripts.src_collapser')
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

	@include('partials.searchfilters.ajax_checkbox', ['model'=>isset($jobad) ? $jobad : null, 'matchtype'=>'cand'])

				$('.p1').collapser({
		mode: 'lines',
		truncate: 20
	});


		});


    </script>
@stop
