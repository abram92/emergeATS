@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top mb-1 client">
		<h3>@if (isset($note->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Client Agency Note') }}<div class="float-right">
				<span class=" text-md-right fa fa-building"></span>
							{{ __($client->name ) }}
                            </div>
		</h3>
	</div>

@endsection

@section('content')

	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

@if (isset($note->id))				
					{!! Form::model($note, ['method' => 'PATCH','route' => ['clientnotes.update', $note->id]]) !!}
								
@else
					{!! Form::open(array('route' => ['clientnotes.store', $client->id],'method'=>'POST')) !!}
@endif

				<input type="hidden" name="client_id" value="{{ $client->id }}">

@if (isset($note->id))
<div class="row mb-2">
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Editor',
														   'is_trashed'=> $note->editor->trashed(),
															'fieldvalue'=> old('listname', optional($note->editor)->listname ) ])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Date Created',
															'fielddate'=> old('created_at', $note->created_at )
															])
</div>
<div class="col-4">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Edited',
															'fielddate'=> old('updated_at', $note->updated_at )
															])
</div>
</div>
<hr>	
@endif

@include('partials.longtext.longtext_edit', ['ltfieldname'=>'agencynotes', 'ltfieldlabel'=>'Agency Note', 'ltfieldmodel'=> isset($note) ? $note : null , 'ltrequired'=>'true'])


@section('formbuttons')
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection			
@include('partials.footer.formbuttonsSection', ['tag_textarea'=>true])
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