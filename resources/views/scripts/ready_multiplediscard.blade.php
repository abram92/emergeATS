			$('.discardchk').click(function() {
				if (this.checked)
					$('.detail'+this.value).hide();
				else
					$('.detail'+this.value).show();
					
			});
			
			$('.discardchk:checked').each(function () {
				if (this.checked)
					$('.detail'+$(this).val()).hide();
			});			