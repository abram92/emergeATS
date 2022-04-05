		$('#clearfilter').click(function(){
	var select = $(".searchForm .select-select2-optional");
        select.prop("selectedIndex", -1); 
        select.trigger('change.select2');
		$('.searchForm textarea').val('');		
		$('.searchForm :text').val('');
		$(".searchForm input[type='date']").val('');
		$(".searchForm input[type='search']").val('');
});