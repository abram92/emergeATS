<div class="float-right">
<button id="tagButton" class="btn btn-primary" onclick="return false;" title="Add Timestamp Tag"><i class="fas fa-clock"></i><i class="fas fa-font"></i></button>
</div>
<script>

$("#tagButton").on('mousedown',function(e) { // handle the mousedown event
	e.preventDefault(); 
	var focusedelem;
	if ($("textarea").length == 1)
		$("textarea").focus();
		
	focusedelem = $(":focus");
	
	if (focusedelem.is("textarea")) {
		var elemid = focusedelem.attr('id');
		var initials = '{{ Auth::user()->initials }}';
		var curPos = document.getElementById(elemid).selectionStart;
                
		let x = $("#"+elemid).val();
                
		var date = new Date();

		var day = date.getDate();
		var month = date.getMonth() + 1;
		var year = date.getFullYear();
		var hour = date.getHours();
		var minutes = date.getMinutes();
		if (month < 10) month = "0" + month;
		if (day < 10) day = "0" + day;
		if (minutes < 10) minutes = "0" + minutes;

		var today = year + "-" + month + "-" + day + ' ' + hour + ":" + minutes;
				
        let text_to_insert = today+' '+initials+'\r\n';
		newCurPos = curPos+text_to_insert.length-1;
        $("#"+elemid).val(x.slice(0, curPos) + text_to_insert + x.slice(curPos));	
		document.getElementById(elemid).setSelectionRange(newCurPos, newCurPos);
	}
});    
</script>
