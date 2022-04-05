@if (isset($model) && $model)
			$(".chk").change(function () {
				var value = $(this).val();
				var is_checked = $(this).is(':checked');
				$.ajax({
					type: "GET",
					url: "/setCheckBox",
					async: true,
					data: {
						id: value,  
						state: is_checked,
						matchid: {{ $model->id }},
						matchtype: '{{ $matchtype }}'
					},
					success: function (msg) {
			$(".dt1").attr("data-count", msg);
						if (msg != '0') {
			$(".btncheckbtn").attr("disabled", false);
			$(".btnchecklnk").removeClass('disableClick');
//			$(".btncheck").show();
			//							alert('Fail');
						} else {
			$(".btncheckbtn").attr("disabled", true);
			$(".btnchecklnk").addClass('disableClick');
//			$(".btncheck").hide();
						}
					}
				});
			});		

			$("#deselectall").click(function () {
				$.ajax({
					type: "GET",
					url: "/setCheckBox",
					async: true,
					data: {
						id: -1,  
						state: false,
						matchid: {{ $model->id }},
						matchtype: '{{ $matchtype }}'
					},
					success: function (msg) {
			$(".dt1").attr("data-count", msg);
						if (msg != '0') {
			$(".btncheckbtn").attr("disabled", false);
			$(".btnchecklnk").removeClass('disableClick');			
//			$(".btncheck").show();
			//							alert('Fail');
						} else {
			$(".btncheckbtn").attr("disabled", true);
			$(".btnchecklnk").addClass('disableClick');
			
//			$(".btncheck").hide();
			$(".chk").prop("checked", false);
						}
					}
				});
			});				
@endif