<div class="card-group" >
@if(isset($contactperson))
	<div class="card my-auto border-0">
		<div class="row border-0">
			<div class="col-md-3 my-auto text-center flex fa fa-user" data-toggle="tooltip" title="Contact Person">
			
			</div>
			<div class="col-md-9 flex">
				<div>{{ $contactperson }}</div>
			</div>
		</div>
	</div>	
@endif
@if ($contacts)
	@php  $current = ""; @endphp	
	@foreach ($contacts as $key => $contact)
		@if ($current != $contact->contactFieldType->fontawesome_icon)
			@if ($current)
			</div>	
		</div>
	</div>		 
			@endif
	<div class="card my-auto border-0">
		<div class="row border-0">
			<div class="col-md-3 my-auto text-center flex {{ $contact->contactFieldType->fontawesome_icon }}" data-toggle="tooltip" title="{{ $contact->contactFieldType->name }}">
			
			</div>
			<div class="col-md-9 flex">
		@endif
			<div>{{ $contact->data }}</div>
		@php  $current = $contact->contactFieldType->fontawesome_icon; @endphp
	@endforeach
</div>	
		</div>
	</div>		
@endif
</div>
 