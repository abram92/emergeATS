@if (($contacts) && ($contacts->count() > 0))
	@php  $current = ""; @endphp	
	@foreach ($contacts as $key => $contact)
		@if ($current != optional($contact->contactFieldType)->fontawesome_icon)
			@if ($current)
			</div>	
		</div>
	</td>
</tr>		 
			@endif
<tr >
	<td>
		<div class="row">
			<div class="col-md-2 my-auto text-center {{ $contact->contactFieldType->fontawesome_icon }}" data-toggle="tooltip" title="{{ $contact->contactFieldType->name }}">
			
			</div>
			<div class="col-md-10">
		@endif
			<div>{{ $contact->data }}</div>
		@php  $current = optional($contact->contactFieldType)->fontawesome_icon; @endphp
	@endforeach
</div>	
		</div>
	</td>
</tr>	
@endif
