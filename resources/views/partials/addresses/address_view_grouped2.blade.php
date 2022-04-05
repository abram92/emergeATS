@if (($addresses) && ($addresses->count() > 0))
	@foreach ($addresses as $key => $address)

<div class="row @if(!$loop->last)border-bottom @endif">
		<div class="row col-xs-12 col-sm-12 col-md-12">
		@if($address->address1)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->address1 }}</div>@endif
		@if($address->address2)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->address2 }}</div>@endif
		@if($address->city)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->city }}</div>@endif
		@if($address->province)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->province }}</div>@endif
		@if($address->country)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->country }}</div>@endif
		@if($address->postal_code)<div class="row col-xs-12 col-sm-12 col-md-12">{{ $address->postal_code }}</div>@endif
		</div>
</div>	
	@endforeach
@endif
