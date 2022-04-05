@include('scripts.src_datatables_group')

    $('.filterableGroup').DataTable( {
        order: [[1, 'desc'],[5, 'desc']],
			"lengthMenu" : [[10, -1],[10, "All"]],
        rowGroup: {
            dataSrc: 0
        },
		  "columnDefs": [
			{ "visible": false, "targets": [0,1] }
		]
    } );