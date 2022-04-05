		<div class="card">
		
{{-- <div class="card-header text-light bg-dark pt-1 pb-1">Rating</div> --}}
<div class="card-body pt-0 pl-0 pr-0 pb-1">
<div class="container noborder row no-gutters">
<div class="col-6">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Level',
															'fieldstatus'=> $candidate->level ])
</div>

<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'EE Status',
															'fieldstatus'=> $candidate->eestatus ])
</div>
<div class="col-2">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Rating',
															'fieldstatus'=> $candidate->rating ])
</div>
</div>
</div>						
</div>
