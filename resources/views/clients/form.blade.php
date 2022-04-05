@extends('layouts.tab')

@section('tabheader')
	<div class="card-header client sticky-top mb-1"><h3>@if (isset($client->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Client') }} @if (isset($client->id)) {{ $client->name }} @endif</h3></div>
@endsection

@section('content')
<div class="mt-5 candidate-container mb-5 container">
	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

@if (isset($client->id))
					{!! Form::model($client, ['method' => 'PATCH','route' => ['clients.update', $client->id]]) !!}
@else
					{!! Form::open(array('route' => 'clients.store','method'=>'POST')) !!}
@endif

<div  class="card-deck">
						<div class="card card-body border-0">

{{-- @include('partials.form_text', ['fieldname'=>'name', 'fieldlabel'=>'Name', 'is_required'=>true, 'is_autofocus'=>true])
	@include('partials.select2_dropdown_single', ['fieldname'=>'consultant_id',
									'fieldlabel'=>'Consultant',
									'fieldplaceholder'=>'Choose Consultant',
									'required'=>true,
									'options'=>$consultants,
									'selectedid'=>isset($client) ? $client->consultant_id : null]) --}}

@include('partials.select2_dropdown_single', ['fieldname'=>'status_id',
									'fieldlabel'=>'Status',
									'fieldplaceholder'=>'Choose Status',
									'required'=>true,
									'options'=>$statuses,
									'selectedid'=>isset($client) ? $client->status_id : null])


						</div>


    <div class="card card-body">
		<div class="card">
 @include('partials.contacts.contact_edit_block')
	</div>


	<div class="card">
 @include('partials.addresses.address_edit_block')
	</div>



    </div>
</div>

{{-- <div class="card-deck">
	<div class="card">
 @include('partials.addresses.address_edit_block')
	</div>
	<div class="card">
 @include('partials.contacts.contact_edit_block')
	</div>
</div>						 --}}
<div></div>
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'techenvironment', 'ltfieldlabel'=>'Tech Environment', 'ltfieldmodel'=> isset($client->techenvironment) ? $client->techenvironment : null , 'ltrequired'=>'required'])

@include('partials.longtext.longtext_edit', ['ltfieldname'=>'agencynotes', 'ltfieldlabel'=>'New Agency Notes', 'ltfieldmodel'=> old('agencynotes', null) , 'ltrequired'=>''])


@if (isset($client->id))
	@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Edit Comment', 'ltfieldmodel'=> old('comments', null) , 'ltrequired'=>''])
@endif


</div>

@section('formbuttons')
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
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
@include('scripts.addresses.ready_address_edit_js')



		});


    </script>
@endsection
