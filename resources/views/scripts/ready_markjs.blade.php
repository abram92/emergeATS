	function createKeywords(str) {
		var myRegexp = /[^\s"]+|"([^"]*)"/gi;
		var myArray = [];
		var excludeArray = ['and', 'or'];

		do {
			//Each call to exec returns the next regex match as an array
			var match = myRegexp.exec(str);
			if (match != null)	{
				//Index 1 in the array is the captured group if it exists
				//Index 0 is the matched text, which we use if no captured group exists
				myArray.push(match[1] ? match[1] : match[0]);
			}
		} while (match != null);
		return myArray.filter(function(item) {
			return !excludeArray.includes(item.toLowerCase()); 
		});
	}	
	
    var highlightfields = @php echo($fields) @endphp	
	$.each(highlightfields, function( index, value ) {
		fld = $('input[name="q['+value+']"]').val();
		$("pre[id^="+value+"]").mark(createKeywords(fld), {separateWordSearch: false});
	});

