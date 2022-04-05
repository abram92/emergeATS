@section('js')
@parent
<script>
	function toggleComment(identifier, field){     
           id = '#'+field+$(identifier).data('id')+'comment';	
			$(id).toggleClass("d-none d-table-row");
			return false;	   
        }

</script>
@php
$toggledJS = true;
@endphp
@stop