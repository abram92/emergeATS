<div class="card location">
<div class="card-header">
<h6 class="">{{ __('Location') }}</h6>
</div>
<div class="card-body pb-0">
<div class="row">
<div class="col-md-5">

@include('partials.select2_dropdown_single', ['fieldname'=>'current_location_id', 
									'fieldlabel'=>'Current Location', 
									'fieldplaceholder'=>'Choose Current Location', 
									'options'=>$locations,
									'selectedid'=>isset($candidate) ? $candidate->current_location_id : null])
</div>
<div class="col-md-5 offset-md-1">
@include('partials.select2_dropdown_multiple', ['fieldname'=>'preferredlocations[]', 
									'fieldlabel'=>'Preferred Locations', 
									'fieldplaceholder'=>'Choose Locations', 
									'options'=>$locations,
									'selectedoptions'=>old('preferredlocations', isset($preferredlocations) ? $preferredlocations : null)])
</div>
</div>
</div>
</div>