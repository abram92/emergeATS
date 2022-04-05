	function registerInterest(url, linkid) {
         
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
                        url: url,
                        type: "GET",
                        success: function (data) {
                            displayMessage("Linked Successfully", 'alert-success', linkid);
						},
						fail: function (data) {
                            displayMessage("Link Failed", 'alert-danger', linkid);
                        }
                    });
    }
	function displayMessage(message, divclass, el) {
    $('#'+el).html("<div class='"+divclass+" linked'>"+message+"</div>");
		setTimeout(function() { 
			$(".linked").fadeOut(); 
			if (divclass.indexOf('success') >= 0)
			  $('#'+el).hide(); 
			}
			, 2000);
	
    }
	
	function deleteFile(url, linkid) {
         
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
                        url: url,
                        type: "GET",
                        success: function (data) {
							var table = $('#'+linkid).parents("table").DataTable();
  //                          displayMessage("File Deleted", 'alert-success', linkid);
							table
			.row('#'+linkid)
			.remove()
		.draw();
						},
						fail: function (data) {
                            displayMessage("Delete Failed", 'alert-danger', linkid);
                        }
                    });
    }