@php

$is_editable = !isset($editable) || $editable;
@endphp

@if ($documents->count() > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 1, "desc" ]]'  data-column-defs='[{"sortable": false, "targets": [2,3]}]'>
		<thead class="table-dark">
			<tr>
				<th>Filename</th>
				<th>Upload Date</th>
				<th>File Size</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($documents as $key => $document)
			<tr id="file{{ $document['id'] }}">
				<td id="filename{{ $document['id'] }}">{{ $document['filename'] }}</td>
				<td>@if($document['updated_at']){{ $document['updated_at'] }}@else {{ $document['created_at'] }}@endif</td>
				<td>{{ $document['size'] }}</td>
				<td>
							<a href="{{ url('/file/'.$document['id'].'_'.$model.'_'.$modelid.'/download') }}" data-toggle="tooltip" title="Download" class="btn btn-sm text-info bootpopup fa fa-file-download actionbtn" target="popupModal2"></a>
@if ($is_editable)							
						<button type="button" class="btn btn-sm text-primary fa fa-edit actionbtn" data-toggle="modal" data-target="#fileEdit" title="Rename" data-content="{{ $document['filename'] }}" data-fileid="{{ $document['id'] }}" data-url="{{url('/file/'.$document['id'].'_'.$model.'_'.$modelid.'/rename') }}">
					</button>
						<a href="javascript:deleteFile('{{url('/file/'.$document['id'].'_'.$model.'_'.$modelid.'/delete') }}', 'file{{ $document['id'] }}')" data-toggle="tooltip" title="Delete" class="btn btn-sm text-danger fa fa-trash actionbtn"></a> 
@endif
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
</div>	
	

@else
	@include('partials.emptytable')
@endif

@if ($is_editable)
	
<div class="modal fade" id="fileEdit" tabindex="-1" role="dialog" aria-labelledby="fileEditLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
		<form>
      <div class="modal-header document">
		<h4 class="modal-title" id="fileEditLabel"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
	  <input type="hidden" id="renameaction" name="renameaction">
	  <input type="hidden" id="renameid" name="renameid">
      <div class="modal-body text-wrap">
          <pre id="modal-text" class="wraptext">
          </pre>
		  <input placeholder="New Filename" class="form-control" required autofocus name="filename" type="text" pattern="[^\\/:\x22*?<>|]+" id="filename" oninvalid="this.setCustomValidity('Not a valid filename')" oninput="this.setCustomValidity('')">

      </div>
      <div class="modal-footer">
	    <button type="submit" class="btn  btn-success">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>
	<script src="{{ asset('js/ajaxcalls.js') }}" rel='javascript'></script>	

@endif
@section('js')
@parent

@if ($is_editable)
    <script>
	
		$("document").ready(function() {
			
var opener;

$('#fileEdit').on('show.bs.modal', function (event) {

    opener = document.activeElement;
  var button = $(event.relatedTarget) // Button that triggered the modal
  var content = button.attr('data-content') 

//  bgcolor = button.css( "background-color" );

  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
//  modal.find('.modal-header').css("background-color", bgcolor)
  modal.find('.modal-title').text('Rename file')
   modal.find('#filename').val('')
  modal.find('#renameaction').attr('value', button.data('url'))
  modal.find('#renameid').attr('value', button.data('fileid'))
  modal.find('.modal-body input').text(content)  
  modal.find('.modal-body pre').text(content.replace(/\r\n/g,'\n'))
})

  $("#fileEdit").submit(function(event){
	  newfilename = $('#filename').val()
	 $.ajax({
		type: "POST",
		url: $('#renameaction').val(),
		cache:false,
		data: {_token: "{{ csrf_token() }}",
				filename: newfilename
		},
		success: function(response){
//		console.log(opener);
//		$(opener.relatedTarget).data('content') = newfilename
//			alert(opener.siblings().first().value())
			$("#filename"+$('#renameid').val()).html(response);
			$('button[data-fileid=' + $('#renameid').val() + ']').attr("data-content", response);			
//			$(event.relatedTarget).data('content') =  newfilename;
			$("#fileEdit").modal('hide');
		},
		error: function(){
			alert("Rename failed");
		}
	});
		return false;
	});

					
		});
		
		
        var f = function(e)
        {
            var srcElement = e.srcElement? e.srcElement : e.target;

            if ($.inArray('Files', e.dataTransfer.types) > -1)
            {
                e.stopPropagation();
                e.preventDefault();

                e.dataTransfer.dropEffect = (srcElement.id == 'dropzone') ? 'copy' : 'none';


            }
        };

        document.body.addEventListener("dragleave", f, false);
        document.body.addEventListener("dragover", f, false);
        document.body.addEventListener("drop", f, false);
		
@include('scripts.init_popover')		
    </script>

@endif	
@stop	