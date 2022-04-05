		<div class="card jobtitle">
		
<div class="card-header noborder p-1 pb-1">Job Title</div>
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'System',
															'fieldvalue'=> optional($candidate->jobtitle)->description ])

				@include('partials.staticdisplay.field', ['fieldprompt'=>'Actual',
															'fieldvalue'=> $candidate->jobtitle_text ])
</div>
</div>