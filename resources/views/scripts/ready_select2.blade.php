		function styleOption (data, container) {
			$(container).css('background-color', '');
			$(container).css('color', '');
//			console.log(container);
			if (data.element)
			{
				colour = $(data.element).attr('data-color');
				if (colour != "undefined") {
					$(container).css('background-color', colour);
				}
				if ((typeof colour !== "undefined") && (colour != '')) {
					txtcolour = invertColor(colour, true);
					$(container).css('color', txtcolour);
				}			
			} 

			return data.text;
		}

@if (isset($elementselector))
		$('@php echo $elementselector;  @endphp').select2({
			templateResult: styleOption,
			templateSelection: styleOption,
			allowClear: true
		});	
@endif		
		$('.select-select2').select2({
			templateResult: styleOption,
			templateSelection: styleOption
		});	

		$('.select-select2-optional').select2({
			templateResult: styleOption,
			templateSelection: styleOption,
			allowClear: true
		});			
		
		function invertColor(hex, bw) {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }
    var r = parseInt(hex.slice(0, 2), 16),
        g = parseInt(hex.slice(2, 4), 16),
        b = parseInt(hex.slice(4, 6), 16);
    if (bw) {
        return (r * 0.299 + g * 0.587 + b * 0.114) > 186
            ? '#000000'
            : '#FFFFFF';
    }
    // invert color components
    r = (255 - r).toString(16);
    g = (255 - g).toString(16);
    b = (255 - b).toString(16);
    // pad each with zeros and return
    return "#" + padZero(r) + padZero(g) + padZero(b);
}

function padZero(str, len) {
    len = len || 2;
    var zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}