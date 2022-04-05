			$('body').on("change", "select.contact-type-select", function() {
				var rowid = this.id.replace(/[^0-9]/g, "");
				var typeid = $(this).find("option:selected").val();
				var type = "";
				var font = "";
				$.each(contacttypes, function(index, contacttypes) {
					if (contacttypes.id == typeid) {
						font = contacttypes.fontawesome_icon;
						type = contacttypes.type;
					}
				});
				if (!type)
					type = "text";
				$(this).closest('td').find('span.input-group-text').attr("class", font+' input-group-text');

			});
			
			// remove contact
			$(document).on('click', 'a.removecontact', function() {
				if (confirm('Are you sure?'))
					$(this).closest('tr').remove();
				return false;
			});
			
			$('#contactClone :input').prop('disabled', 'disabled');
			
			$('#contactbtn').click(function() {
				var rows = $("#contactTable tr").length;
				var rowid =  0;
				if (rows) {
					rowid = parseInt($("#contactTable tr").last().attr("id")) + 1;
				}

				var td1 = $("<td></td>");
				newRow = $('#contactClone').children().clone();

				newRow.find('[id^=contacts]').prop('id', function(index, id) {
					      return id.replace('key',rowid);
				});
				newRow.find('[name^=contacts]').prop('name', function(index, id) {
					      return id.replace('key',rowid);
				});				
				newRow.find(':input').prop('disabled', function(index, id) {
					      return false;
				});
				
				td1.append(newRow);
				var tr1 = $("<tr></tr>").attr("id", rowid);
				tr1.append(td1);
//                var input = $("<input/>", {type: "text", id: "contacts[][data]"});
//							var input = "<input type='text' id='contacts[][data]'>";
				$('#contactTable').append(tr1);
				newRow.find('select').select2({allowClear: false});
				newRow.find('select').find('option:eq(0)').prop('selected', true);
				newRow.find('.selection').css("width","100%");
								newRow.find('select').change();
			});
	
			$('.contact-type-btn').click(function() {
				var elparts = $(this).attr('id').split('_');
				var elid = elparts[1];
				var rows = $("#contactTable tr").length;
				var rowid =  0;
				if (rows) {
					rowid = parseInt($("#contactTable tr").last().attr("id")) + 1;
				}

				var td1 = $("<td></td>");
				newRow = $('#contactClone').children().clone();

				newRow.find('[id^=contacts]').prop('id', function(index, id) {
					      return id.replace('key',rowid);
				});
				newRow.find('[name^=contacts]').prop('name', function(index, id) {
					      return id.replace('key',rowid);
				});				
				newRow.find(':input').prop('disabled', function(index, id) {
					      return false;
				});
				
				td1.append(newRow);
				var tr1 = $("<tr></tr>").attr("id", rowid);
				tr1.append(td1);
//                var input = $("<input/>", {type: "text", id: "contacts[][data]"});
//							var input = "<input type='text' id='contacts[][data]'>";
				$('#contactTable').append(tr1);
				newRow.find('select').select2({allowClear: false});
				newRow.find('select').find('option:eq('+elid+')').prop('selected', true);

//newRow.find('select option[id='+elid+']').prop('selected', true);

//newRow.find('select').find('option#'+elid).prop('selected', true);
//alert(newRow.find('select option').filter('#'+elid).attr("id"));
				newRow.find('.selection').css("width","100%");
								newRow.find('select').change();
			});
			
			$('.savedcontact').select2({allowClear: false});
