<div class="card client-outline">
<div class="card-header client">
<h6>{{ __('Client') }}</h6>
</div>
<div class="card-body pb-0">
@include('partials.select2_dropdown_single', ['fieldname'=>'client_id', 
									'fieldlabel'=>'Client', 
									'fieldplaceholder'=>'Choose Client', 
									'options'=>$clients,
									'selectedid'=>isset($client_id) ? $client_id : null])
									
@include('partials.select2_dropdown_multiple', ['fieldname'=>'contacts[]', 
									'fieldlabel'=>'Contacts', 
									'select2_class'=>'select-select2-optional',
									'fieldplaceholder'=>'Select Contacts', 
									'options'=>$allcontacts,
									'selectedoptions'=>old('contacts', isset($contacts) ? $contacts : null)])

</div>
</div>