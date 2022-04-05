@if ($dt)
@if (is_string($dt))	
{{ \Carbon\Carbon::parse($dt)->format('j M Y') }}
@else
{{ optional($dt)->format('d M Y') }}
@endif
@endif