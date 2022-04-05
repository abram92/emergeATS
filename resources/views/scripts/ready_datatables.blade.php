@include('scripts.src_datatables', ['scr'=>'1.js'])

	 $('.filterable').DataTable( {
			"lengthMenu" : [[10, -1],[10, "All"]]
	 } );
	 $('.filterable').each(function(i, obj) {
		 if ($(obj).attr('data-page-length')) {
	 $('#DataTables_Table_'+i+'_length select').prepend( '<option value="'+$(obj).attr('data-page-length')+'">Active</option>' );
		 }
    //test
});