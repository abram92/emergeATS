@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top mb-1 clientcontact">
		<h3>{{ __('Client Contact:'.$clientcontact->listname) }}</h3>
		<h4>{{ __('('.$clientcontact->client->name.')') }}</h4>
	</div>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
                    @csrf

<div class="card-group">
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'First Name',
															'fieldvalue'=> old('firstname', $clientcontact->firstname ) ])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Name',
															'fieldvalue'=> old('lastname', $clientcontact->lastname ) ])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Position',
															'fieldvalue'=> old('position', $clientcontact->position )
															])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Edited',
															'fielddate'=> old('created_at', $clientcontact->createdupdated_at )
															])
</div>

</div>	
<hr>


					
@include('partials.users.contacts_view')

@if ($clientcontact->comments)
<div class="card card-body border-0">	
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Comment',
															'fieldfulltext'=> $clientcontact->comments->chunk
															])				
</div>															
@endif	

@section('formbuttons')
								<button type="button" class="btn btn-close">{{ __('Close') }}</button>							
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')			

				</div>		
			</div>
 
@endsection
