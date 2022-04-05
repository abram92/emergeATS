@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top mb-1 clientcontact">
		<h3>@if (isset($clientcontact->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Contact') }} @if (isset($clientcontact)) {{ __('('.$clientcontact->listname.')') }} @endif
		</h3>
		<h4>{{ __('('.$client->name.')') }}</h4>
	</div>
@endsection

@section('content')
				
	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

@if (isset($clientcontact->id))				
					{!! Form::model($clientcontact, ['method' => 'PATCH','route' => ['clientcontacts.update', $clientcontact->id]]) !!}
								
@else
					{!! Form::open(array('route' => ['clientcontacts.store', $client->id],'method'=>'POST')) !!}
@endif

							<input type="hidden" name="client_id" value="{{ $client->id }}">
 
@include('partials.users.details_edit', ['userdetail'=>isset($clientcontact) ? $clientcontact : null])
		

@include('partials.form_text', ['fieldname'=>'position', 
								'fieldlabel'=>'Position', 
								'fielddefault'=> old('position', isset($clientcontact) ? $clientcontact->position : null ),
								'is_required'=>true])		
								

@include('partials.contacts.contact_edit_block')

@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Comments', 'ltfieldmodel'=> isset($comments) ? $comments : null , 'ltrequired'=>''])
	
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
@include('scripts.src_select2')
@endpush

	
@section('js')

    <script>
	
		$("document").ready(function() {
	var contacttypes = @json($contact_types);
			
			
			@include('scripts.ready_select2')
			
@include('scripts.contacts.ready_contact_edit_js');
			
		
		});
		
		
    </script>
@endsection