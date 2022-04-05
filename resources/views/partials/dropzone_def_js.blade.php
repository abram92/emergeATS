@push('scripts')
	<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
<script src="{{ asset('js/dropzone.js') }}"></script>
<script type="text/javascript">
        // Immediately after the js include
//        Dropzone.autoDiscover = false;     
</script>
@endpush
 <script>
         Dropzone.options.dropzone = {	
            maxFilesize: 12,
			previewsContainer: ".dropzone-previews",
			previewTemplate: document.getElementById("preview-template").innerHTML,
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
               return time+file.name;
            },
            acceptedFiles: "",
            addRemoveLinks: true,
            timeout: 50000,
            removedfile: function(file) 
            {
                var name = file.upload.filename;
//				console.log(file);
                $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                    type: 'GET',
                    url: file.deleteUrl,
                    success: function (data){
                        console.log("File has been successfully removed!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
               init: function() {
            this.on("complete", function(file) {
                $(".dz-remove").html("<span data-toggle='tooltip' title='Delete' class='btn btn-sm text-danger fa fa-trash actionbtn' ></span>");
            });
        },
            success: function(file, response) 
            {
//                console.log(response);
				      if(response != 0){
         // Download link
		 file.deleteUrl = response.deleteurl;
		 file.previewElement.id = 'file'+response.id;
		 $(file.previewElement).find(".name").attr("id", 'filename'+response.id);
		 var actionscol = $(file.previewTemplate).find(".dz-actions");
         var anchorEl = document.createElement('a');
         anchorEl.setAttribute('href',response.url);
         anchorEl.setAttribute('target','_blank');
         anchorEl.innerHTML = "<span data-toggle='tooltip' title='Download' class='btn btn-sm text-info bootpopup fa fa-file-download actionbtn' ></span>";
//		 file.previewTemplate.insertBefore(anchorEl, file.previewTemplate.lastChild);		 
		 actionscol.append(anchorEl);		 
 //        file.previewTemplate.appendChild(anchorEl);
         // Rename link 
         var anchorEl = document.createElement('a');
         anchorEl.setAttribute('data-toggle',"modal");
         anchorEl.setAttribute('data-target','#fileEdit');
		 anchorEl.setAttribute('title','Rename');
		 anchorEl.setAttribute('data-content',file.name);
		 anchorEl.setAttribute('data-url',response.renameurl);
		 anchorEl.setAttribute('data-fileid',response.id);
         anchorEl.innerHTML = "<span data-toggle='tooltip' title='Rename' class='btn btn-sm text-primary fa fa-edit actionbtn' ></span>";
//		 file.previewTemplate.insertBefore(anchorEl, file.previewTemplate.lastChild);		 
		 actionscol.append(anchorEl);	
		 
		actionscol.append($(file.previewTemplate).find(".dz-remove"));		 

      }
            },
            error: function(file, response)
            {
               return false;
            }
};


</script>
<style>
.dropzone {
	min-height:20px;
	height : 30px;
	padding: 0;
}
.dropzone .dz-message{
	text-align:top;
			margin:0;
			}
</style>

