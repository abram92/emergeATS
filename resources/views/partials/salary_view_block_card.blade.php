@php $textprompt = (isset($is_candidate) && ($is_candidate)) ? 'Expected Gross' : 'Salary' @endphp
<div class="card salary">
<div class="card-header pt-1 pb-1">Salary</div>
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Category',
															'fieldstatus'=> $obj->salarycategory ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>$textprompt,
															'fieldvalue'=> (isset($is_candidate) && ($is_candidate)) ? $obj->salary : $obj->salary_from ])
						</div>
</div>