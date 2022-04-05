@extends('layouts.tab')

@section('tabheader')
    <div class="card-header sticky-top client mb-1">
		<h3>{{ __('Client Agency Note') }}
			<div class="float-right">
				<span class=" text-md-right fa fa-building"></span>
							{{ old('clientname', $client->name ) }}
            </div>
		</h3>
	</div>

@endsection
@section('content')

    <div class="card">
        <div class="card-body">
                    @csrf

<div class="card-group">
<div class="card card-body border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Editor',
														   'is_trashed'=> $note->editor->trashed(),
															'fieldvalue'=> old('listname', optional($note->editor)->listname ) ])
</div>
<div class="card card-body border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Date Created',
															'fielddate'=> old('created_at', $note->created_at )
															])
</div>
<div class="card card-body border-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Edited',
															'fielddate'=> old('updated_at', $note->updated_at )
															])
</div>

</div>	
<hr>

<div class="card card-body border-0">	
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Note',
															'fieldfulltext'=> old('chunk', $note->chunk )
															])				
</div>						
					


@section('formbuttons')
			<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')		

	
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