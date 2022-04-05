<div class="card mb-3 job-outline" id={{ $job->id }}>
	<div class="card-header job">
		<div class="row p-0">
		<div class="col-6">
		<div class="row">
	@if (isset($candidate) && $candidate)
	    @if ($candidate->jobapplications->contains('job_ad_id', $job->id))
			<i class="fa fa-check text-secondary" title="Selected"></i>
		@else	
		<input type="checkbox" name="jobIds[]" class="chk"  value="{{ $job->id }}" @if (is_array(session()->get('search_job_'.$candidate->id)) && in_array($job->id, session()->get('search_job_'.$candidate->id))) checked @endif>
		@endif
		@endif
		<span class="ml-2">
		<div class="float-left">
		<h4>{{ $job->jobref }}	
		</h4>
		</div>
		</span>				
		<span class=" ml-auto mr-1">
		    <span class="badge1 shadow client "  title="Client">
				<span class=" fa fa-building"></span>
				{{ $job->client->name }}
				</span>
			</span>		
		</div>
		<div class="row">
				<h5>{{ $job->jobtitle_text }}</h5>

			</div>
			<div class="row">
				@include('partials.list_job_actions',[])
		<div class="ml-auto mr-auto">
		<div class="position-relative ml-2" >
@if($job->cv_sent_before_update)
<div class="position-relative bottom-0 end-100 badge rounded-pill bg-dark text-white" title="Before Update">{{ $job->cv_sent_before_update }}</div>		
@endif
		<i class="text-candidate fa fa-envelope fa-2x" title="CVs Emailed"></i>
<div class="position-absolute start-50 top-0 badge rounded-pill {{ $job->cv_sent_current ? 'bg-success' : 'bg-danger' }} text-white" title="Current">{{ $job->cv_sent_current }}</div>		
		</div>		
</div>				
			</div>			
			</div>
			<div class="col-6 p-0">
				@include('partials.jobads.jobcontacts_list', ['contacts'=>$job->clientcontacts])
			</div>
		</div>
	</div>
	
		<div class="card-group job">
		@if($job->gender)
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Gender',
															'fieldstatus'=> optional($job->gender) ])
		</div>	
		@endif
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'EE Status',
															'fieldstatus'=> $job->eestatus ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Date Uploaded',
															'fielddate'=> $job->activated_at ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Consultant',
															'fieldconsultant'=> $job->consultant ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Salary Category',
				                                          'promptclass'=>'salaryheader', 
															'fieldstatus'=> $job->salarycategory ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Salary',
				                                          'promptclass'=>'salaryheader', 
															'fieldvalue'=> $job->salary_from ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Locations',
				                                          'promptclass'=>'locationheader', 
															'fieldvalue'=> implode(PHP_EOL,optional($job->locations)->pluck('description')->toArray()) ])
		</div>
		<div class="card job card-body pt-1 pb-1 border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Status',
															'fieldstatus'=> $job->status ])
		</div>


		</div>	
	<div class="card-body m-0 p-0">

		@include('partials.collapse_textarea', ['field'=>'cvsend'.$job->id, 'field_title'=>'CV Send Instructions', 'start_expanded'=>'true', 'field_body'=>isset($job->cvsendinstructions) ? $job->cvsendinstructions->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'summary'.$job->id, 'field_title'=>'Summary', 'start_expanded'=>'true', 'field_body'=>isset($job->summary) ? $job->summary->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'agencynotes'.$job->id, 'field_title'=>'Agency Notes', 'field_min'=>true, 'start_expanded'=>'true', 'field_body'=>isset($job->agencynotes) ? $job->agencynotes->chunk : '',
															'edit_lt'=>true, 
															'edit_lt_route'=>route('notes.edit',['model'=>'jobs','modelid'=>$job->id]), 
															'edit_lt_target'=>'jobad'.$job->id])
		@include('partials.collapse_textarea', ['field'=>'projectplan'.$job->id, 'field_title'=>'Project Plan', 'start_expanded'=>'false', 'field_body'=>isset($job->projectplan) ? $job->projectplan->chunk : ''])
		@include('partials.collapse_textarea', ['field'=>'skills'.$job->id, 'field_title'=>'Skills', 'start_expanded'=>'true', 'field_body'=>isset($job->skills) ? $job->skills->chunk : ''])
	</div>
</div>	