$("document").ready(function(){
$(':submit').on('click', function(e){
//	$(this).attr('disabled', true);

	var wasSet = $('#busyanim');
	if (wasSet.length) {
		e.preventDefault();
		alert('Waiting for server response');
	} else {
	
	var anim = document.createElement("i");
	anim.setAttribute("id", "busyanim");
//	anim.addClass( "fa fa-spinner fa-spin" );
    	anim.classList.add( "fa", "fa-spinner", "fa-spin" );
	$(this).prepend(anim);
	}
});

$('#editModal').on("hide.bs.modal", function() {
	
	
	var wasSet = $('#busyanim');
	if (wasSet.length) {
		wasSet.parent().attr('disabled', false);
		wasSet.remove();
	}
})


});