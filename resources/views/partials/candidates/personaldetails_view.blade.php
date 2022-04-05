		<div class="card personaldetails">
		
{{-- <div class="card-header text-light bg-dark pt-1 pb-1">Personal Details</div> --}}
<div class="card-body pt-0 pl-0 pr-0 pb-1">
<div class="container noborder row no-gutters">
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'ID Number',
															'fieldvalue'=> $candidate->idnumber ])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Birth Date',
															'fielddate'=> $candidate->birthdate ])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Gender',
															'fieldstatus'=> optional($candidate->gender) ])
</div>
</div>
</div>
</div>