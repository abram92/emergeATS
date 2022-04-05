	<div style="height:20px">
	<form method="post" action="{{url($modelurl.'/fileupload')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
						<div class="dz-message" style="height:30px">Drop files here or click to upload</div>
						@csrf
	</form>
</div>