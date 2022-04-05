@section('js')
@parent
<script>
    function childRowFormat ( dataSource ) {
		var html = '';
		for (var key in dataSource){
			html += '<div class="'+key+' dtchild text-wrap">'+
                    dataSource[key].replace(/\n/g, "<br />") +
                '</div>';
		}        
		return html;  
	}

	$(function () {

		var table = $('#@php echo $tableid; @endphp').DataTable();

		$('#@php echo $tableid; @endphp').on('click', 'span.shortlistcomment, span.shortlistsendcv, span.shortlistagnotes', function () {
			var tr = $(this).closest('tr');
			var row = table.row(tr);
			if ($(this).hasClass('shortlistcomment'))
				tr.toggleClass("showcomment");
		  if ($(this).hasClass('shortlistsendcv'))
		    tr.toggleClass("showcommentsendcv");
			if ($(this).hasClass('shortlistagnotes'))
				tr.toggleClass("showagnotes");

			sh1 = tr.hasClass('showcomment');
			sh2 = tr.hasClass('showcommentsendcv');
			sh3 = tr.hasClass('showagnotes');
			
			arr = [];
			if (sh1){
			   arr['childcomment'] = tr.data('child-value-0');
			}
			if (sh2){
			   arr['childsendcv'] = tr.data('child-value-1');
			}
			if (sh3){
			   arr['childagnote'] = tr.data('child-value-2');
			}			
              // Open this row
			if (sh1 || sh2 || sh3)  
              row.child(childRowFormat(arr), 'dtchild').show();
			else  
              row.child.hide();
      });


  });

</script>
@stop