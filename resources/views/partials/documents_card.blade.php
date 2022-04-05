			<div class="card document-outline">
				<div class="card-header document">
				<div class="row">
					<div class="col-md-4">
						<h4>Documents</h4>
					</div>
					<div class="col-md-8">
						@include('partials.dropzone_def_form', ['modelurl'=>$model.'/'.$modelid])
					</div>
				</div>	
				</div>
				<div class="card-body">
						@include('partials.dropzone_def_preview', ['modelurl'=>$model.'/'.$modelid])
			@include('partials.documentlist')
				</div>
			</div>
