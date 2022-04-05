@extends('layouts.tab')

@section('tabheader')
		@if($object instanceOf App\JobAd)
            <div class="card-header job sticky-top">
				<h3>Job Agency Note</h3>
				<div class="row">
					<div class="col-3">
						<h4>{{ __($object->jobref) }}</h4>
						<h5>{{ __($object->jobtitle_text) }}</h5>
					</div>
				</div>
		   
			</div>
		@else
            <div class="card-header candidate sticky-top">
				<h3>Candidate Agency Note</h3>
				<div class="row">
					<h4>{{ __($object->user->listname) }}</h4>
				</div>
		   	</div>
		@endif
@endsection

@section('content')

	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

					{!! Form::model($object, ['method' => 'POST','route' => ['notes.update', $model, $object->id]]) !!}
								
<input type="hidden" name="modelid" value="{{ $object->id }}">
@if (isset($object->agencynotes->id))
<div class="row mb-2">
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Editor',
														   'is_trashed'=> optional($object->agencynotes->editor)->trashed(),
															'fieldvalue'=> old('listname', optional($object->agencynotes->editor)->listname ) ])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Date Created',
															'fielddate'=> old('created_at', $object->agencynotes->created_at )
															])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Edited',
															'fielddate'=> old('updated_at', $object->agencynotes->updated_at )
															])
</div>
</div>
<hr>	
@endif

@include('partials.longtext.longtext_edit', ['ltfieldname'=>'agencynotes', 'ltfieldlabel'=>'Agency Note', 'ltfieldmodel'=> isset($object->agencynotes) ? $object->agencynotes : null , 'ltrequired'=>'true'])


@section('formbuttons')
	<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
	<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')	


{!! Form::close() !!}
        </div>
    </div>
	
@endsection

@push('scripts')
@endpush

	
@section('js')

    <script>
	
		$("document").ready(function() {
			
    $(document).on("click", ".btn-close" , function(){
        window.close();
    });
	
		});
		
		
    </script>
@endsection