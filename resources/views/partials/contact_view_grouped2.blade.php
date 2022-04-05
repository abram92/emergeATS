@if (isset($contacts) && ($contacts->count() > 0))
	@php  $current = ""; @endphp
	@foreach ($contacts as $key => $contact)
		@if ($current != $contact->contactFieldType->fontawesome_icon)
			@if ($current)
			</div>
		</div>
	</div>
			@endif
<div class="row @if(!$loop->last)border-bottom @endif">
		<div class="row col-xs-12 col-sm-12 col-md-12">
			<div class="col-md-2 my-auto  {{ $contact->contactFieldType->fontawesome_icon }}" data-toggle="tooltip" title="{{ $contact->contactFieldType->name }}">

			</div>
			<div class="col-md-10">
		@endif
			<div>@if($contact->contactFieldType->protocol) <a href="{{ $contact->contactFieldType->protocol }}{{ $contact->data }}"> @endif{{ $contact->data }} @if($contact->contactFieldType->protocol) </a>@endif</div>
		@php  $current = $contact->contactFieldType->fontawesome_icon; @endphp
	@endforeach
</div>
		</div>
	</div>
@endif
