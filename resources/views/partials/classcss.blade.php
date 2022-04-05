
@php
 preg_match('/(client|candidate|job)*/', $var1, $matches);
   $cust = (isset($matches[0])) ? $matches[0] :  "";
@endphp

@section('css')
   @if ($cust)
	<link href="{{ asset('css/'.$cust.'.css') }}" rel="stylesheet" />
@endif

@endsection
