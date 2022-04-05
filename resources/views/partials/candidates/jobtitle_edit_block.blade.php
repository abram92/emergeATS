<div class="card jobtitle">
<div class="card-header">
<h6 class="">{{ __('Job Title') }}</h6>
</div>
<div class="card-body pb-0">
<div class="row">
<div class="col-md-5">
@include('partials.select2_dropdown_single', ['fieldname'=>'jobtitle_id', 
									'fieldlabel'=>'System', 
									'fieldplaceholder'=>'Choose Job Title', 
									'options'=>$jobtitles,
									'selectedid'=>isset($candidate) ? $candidate->jobtitle_id : null])
</div>
<div class="col-md-5 offset-md-1">

@include('partials.form_text', ['fieldname'=>'jobtitle_text', 
								'fieldlabel'=>'Actual Job Title', 
								'fielddefault'=> old('jobtitle_text', optional($candidate)->jobtitle_text )])		
</div>						
</div>
</div>
</div>