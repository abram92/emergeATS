<div class="card salary">
<div class="card-header">
<h6>{{ __('Salary') }}</h6>
</div>
<div class="card-body mb-0">
<div class="row">
<div class="col-md-5">
@include('partials.select2_dropdown_single', ['fieldname'=>'salary_category_id', 
									'fieldlabel'=>'Category', 
									'fieldplaceholder'=>'Choose Salary Category', 
									'options'=>$salarycategories,
									'selectedid'=>old('salary_category_id', isset($jobad) ? $jobad->salary_category_id : null)])
</div>
<div class="col-md-5 offset-md-1">
@include('partials.form_number', ['fieldname'=>'salary_from', 
								'fieldlabel'=>'Salary', 
								'fielddefault'=> old('salary_from', isset($jobad) ? $jobad->salary_from : null),
								'fieldmin'=>0])		
</div>								
</div>
</div>
</div>