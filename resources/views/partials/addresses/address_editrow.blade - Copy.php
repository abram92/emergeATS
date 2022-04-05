		<div class="row">
			@if (isset($address) && $address->id)
				<input type="hidden" name="addresses[{{ $key }}][id]" id="addresses[{{ $key }}][id]"  value="{{ $address->id }}">
			@endif
			
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][address1]" class="form-control" id="addresses[{{ $key }}][address1]"  placeholder="Address 1"  value="@if(isset($address)){{ $address->address1 }}@endif">
				<span></span>
				<a href="" data-toggle="tooltip" title="Remove" class="removecontact float-right text-danger fa fa-minus-circle"></a>

			</div>
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][address2]" class="form-control" id="addresses[{{ $key }}][address2]" placeholder="Address 2" value="@if(isset($address)){{ $address->address2 }}@endif">
				<span></span>
	
			</div>	
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][city]" class="form-control" id="addresses[{{ $key }}][city]"  placeholder="City"  value="@if(isset($address)){{ $address->city }}@endif">
				<span></span>
	
			</div>
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][province]" class="form-control" id="addresses[{{ $key }}][province]"  placeholder="Province"  value="@if(isset($address)){{ $address->province }}@endif">
				<span></span>

			</div>
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][country]" class="form-control" id="addresses[{{ $key }}][country]"  placeholder="Country"  value="@if(isset($address)){{ $address->country }}@endif">
				<span></span>

			</div>
			<div class="input-group-append col-md-12">
				<input type="text" name="addresses[{{ $key }}][postal_code]" class="form-control" id="addresses[{{ $key }}][postal_code]"  placeholder="Post Code"  value="@if(isset($address)){{ $address->postal_code }}@endif">
				<span></span>

			</div>
			
		</div>
