<div class="card salary">
<div class="card-header">
<h6 class="">{{ __('Salary') }}</h6>
</div>
<div class="card-body pb-0">
<div class="row">
<div class="col-md-5">
@include('partials.select2_dropdown_single', ['fieldname'=>'salary_category_id', 
									'fieldlabel'=>'Category', 
									'fieldplaceholder'=>'Choose Salary Category', 
									'options'=>$salarycategories,
									'selectedid'=>isset($candidate) ? $candidate->salary_category_id : null])
</div>
<div class="col-md-5 offset-md-1">
@include('partials.form_number', ['fieldname'=>'salary', 
								'fieldlabel'=>'Expected Gross Salary', 
								'fielddefault'=> old('salary', optional($candidate)->salary ),
								'fieldmin'=>0])		
</div>								
</div>
</div>
</div>