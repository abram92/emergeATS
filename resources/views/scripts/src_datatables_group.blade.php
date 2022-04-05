@include('scripts.src_datatables', ['scr'=>'2.js'])

@prepend('scripts')
  @if(!isset($datatableGroupScriptAlreadyIncluded))
	<script src="{{ asset('js/dataTables.rowGroup.min.js') }}" rel="javascript"></script>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/rowGroup.dataTables.min.css') }}">
    @php 
      $datatableGroupScriptAlreadyIncluded = true;
    @endphp
  @endif 
@endprepend
