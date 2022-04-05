@if ($contacts)
	@foreach ($contacts as $key => $contact)
<div>{{ $contact->listname }} </div>
	@endforeach
@endif
