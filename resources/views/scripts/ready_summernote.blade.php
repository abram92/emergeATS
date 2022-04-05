
  $('.summernote').summernote({
	  inheritPlaceholder: true,
	  followingToolbar: false,
				toolbar: [
  ['font', ['bold', 'underline', 'italic', 'clear']],
  ['fontname', ['fontname', 'fontsize']],
  ['color', ['color']],
  ['para', ['ul', 'ol', 'paragraph']],
  ['height', ['height']],
  ['table', ['table']],
  ['insert', ['link', 'picture']],
],
lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],
    codemirror: {
      mode: 'text/html',
      htmlMode: true,
      lineNumbers: true,
      theme: 'cosmo'
    },

  });
  
$('.summernote').summernote('fontName', 'Arial');
$('.summernote').summernote('fontSize', '14');  
$('.summernote').summernote('foreColor', 'black');  
$('.summernote').summernote('lineHeight', '1.0');  
