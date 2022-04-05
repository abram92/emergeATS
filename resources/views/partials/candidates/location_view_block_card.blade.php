<div class="card location">
<div class="card-header noborder pt-1 pb-1">Location</div>		
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Current',
															'fieldvalue'=> isset($candidate->location) ? optional($candidate->location)->description : null ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Preferred',
															'fieldvalue'=> optional($candidate->preferredlocations)->implode('description', ', ') ])
						
</div>
		</div>