@prepend('scripts')
 @if(!isset($datatablescriptAlreadyIncluded))

    <script src="{{ asset('js/jquery.dataTables.min.js') }}" rel="javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}" rel="javascript"></script>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    @php 
      $datatablescriptAlreadyIncluded = true;
    @endphp

  @endif 
@endprepend
