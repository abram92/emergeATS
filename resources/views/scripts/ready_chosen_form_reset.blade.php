		$(':reset').click(function(){
	var select = $(".chosen-select");
        select.prop("selectedIndex", -1); 
        select.trigger("chosen:updated");
});