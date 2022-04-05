<div class="card salary">
<div class="card-header noborder pt-1 pb-1">Salary</div>
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Category',
															'fieldstatus'=> $candidate->salarycategory ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Expected Gross',
															'fieldvalue'=> $candidate->salary ])
						</div>
</div>