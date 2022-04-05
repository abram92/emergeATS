	$( ".select2-fetch" ).select2({
		minimumInputLength: 1,
		allowClear: true,
        ajax: { 
			url: function() {
				var id = $(this).attr("id");
				return '/'+$(this).attr("name");
			},
			type: "post",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					_token: "{{ csrf_token() }}",
					term: params.term, // search term
					page: params.page || 1
				};
			},
			cache: true
        }
    });