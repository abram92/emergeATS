@section('contentbuttons')			
	@include('partials.footer.button_start')
		@yield('formbuttons')
	@include('partials.footer.button_end')	
@endsection	

@section('contentfooter')			
	@include('partials.footer.padding')
@endsection