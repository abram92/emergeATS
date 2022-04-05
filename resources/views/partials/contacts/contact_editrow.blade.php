@php $selected_icon = ""; @endphp		
		<div class="row">
			<div class="col-md-3">

			    <select class="form-inline {{ $selectclass }} contact-type-select" style="width:100%;" tabindex="4" name="contacts[{{ $key }}][type]" id="contacts[{{ $key }}][type]">
				    @if(!empty($contact_types))
			          @foreach($contact_types as $rolename => $contact_type)
@php
if (is_array($contact)) {
$contact_field_type = $contact['type'];
$contact_field_data = $contact['data'];
$contact_field_id = isset($contact['id']) ? $contact['id'] : null;
} elseif (is_object($contact) && $contact instanceof App\ContactField) {
$contact_field_type = $contact->contact_field_type_id;
$contact_field_data = $contact->data;
$contact_field_id = $contact->id;
} else {
$contact_field_type = null;
$contact_field_data = null;
$contact_field_id = null;	
}
@endphp
					       <option value="{{ $contact_type['id'] }}" @if(isset($contact) && ($contact_type['id'] == $contact_field_type)) 
							   @php $selected_icon = $contact_type['fontawesome_icon']; @endphp	
							   selected="selected" @endif  >{{ $contact_type['name'] }}</option>  
                      @endforeach
                    @endif
				</select>
			</div>
			<div class="input-group col-md-8">
				<div class="input-group-prepend">
					<span class="@if($selected_icon){{ $selected_icon }}@endif input-group-text"></span>
				</div>
				<input type="text" name="contacts[{{ $key }}][data]" class="form-control" id="contacts[{{ $key }}][data]"   value="@if(isset($contact)){{ $contact_field_data }}@endif">
			@if ($contact_field_id)
				<input type="hidden" name="contacts[{{ $key }}][id]" id="contacts[{{ $key }}][id]"  value="{{ $contact_field_id }}">
			@endif
			</div>
			<div class="col-md-1">
				<a href="" data-toggle="tooltip" title="Remove" class="removecontact text-md-right text-danger fa fa-minus-circle"></a>

			</div>	
		</div>
