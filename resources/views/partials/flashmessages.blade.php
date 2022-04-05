@if (Session::has('success_message'))
@php
$messages = is_array(Session::get('success_message')) ? Session::get('success_message') : [Session::get('success_message')];
@endphp
@foreach ($messages as $message)
<div class="alert alert-success alert-block">

	<button type="button" class="close" data-dismiss="alert">&times;</button>	

        <strong>{{ $message }}</strong>

</div>
@endforeach
{{ Session::forget('success_message') }}
@endif


@if (Session::has('error_message'))
@php
$messages = is_array(Session::get('error_message')) ? Session::get('error_message') : [Session::get('error_message')];
@endphp
@foreach ($messages as $message)
<div class="alert alert-danger alert-block">

	<button type="button" class="close" data-dismiss="alert">&times;</button>	

        <strong>{{ $message }}</strong>

</div>
@endforeach
{{ Session::forget('error_message') }}
@endif


@if (Session::has('warning_message'))
@php
$messages = is_array(Session::get('warning_message')) ? Session::get('warning_message') : [Session::get('warning_message')];
@endphp
@foreach ($messages as $message)
<div class="alert alert-warning alert-block">

	<button type="button" class="close" data-dismiss="alert">&times;</button>	

	<strong>{{ $message }}</strong>

</div>
@endforeach
{{ Session::forget('warning_message') }}
@endif


@if (Session::has('info_message'))
@php
$messages = is_array(Session::get('info_message')) ? Session::get('info_message') : [Session::get('info_message')];
@endphp
@foreach ($messages as $message)
<div class="alert alert-info alert-block">

	<button type="button" class="close" data-dismiss="alert">&times;</button>	

	<strong>{{ $message }}</strong>

</div>
@endforeach
{{ Session::forget('info_message') }}
@endif


@if ($errors->any())

<div class="alert alert-danger">

	<button type="button" class="close" data-dismiss="alert">&times;</button>	

	Please check the form below for errors
    <ul>
	@foreach ($errors->all() as $error)
	  <li>{{ $error }}</li>
	@endforeach
	</ul>

</div>

@endif