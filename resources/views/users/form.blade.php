@extends('layouts.admin')


@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header user"><h3>@if (isset($user->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif @if (isset($user) && ($user->id == Auth::user()->id)) {{ __('Profile') }} @else {{ __('User') }} @endif @if (isset($user->id)) <div class="float-right"><span class="text-md-right fa fa-user"></span>                             {{ old('username', $user->username ) }} </div> @endif</h3></div>

				<div class="card-body">

@include('partials.flashmessages')

@if (isset($user->id))				
					{!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
								
@else
					{!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
@endif

@include('partials.users.details_edit', ['userdetail'=>isset($user) ? $user : null])
@include('partials.contacts.contact_edit_block', ['contacts'=>old('contacts', (isset($contacts) ? $contacts : null))])



@if (Auth::user()->hasRole('Admin'))


@include('partials.select2_dropdown_multiple_role', ['fieldname'=>'roles[]', 
									'fieldlabel'=>'Roles', 
									'fieldid'=>'rolesoptions',
									'fieldplaceholder'=>'Choose Roles', 
									'options'=>$roles,
									'selectedoptions'=>isset($userRole) ? $userRole : null])

@endif	

@if (Auth::user()->hasRole('Admin') || ($user->hasRole('Consultant')))
	
@include('partials.form_text', ['fieldname'=>'jobcode', 
								'fieldlabel'=>'Job Code', 
								'fielddefault'=> old('jobcode', isset($user) ? $user->jobcode : "")])	
								

@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'emailsignature', 'ltfieldlabel'=>'Email Signature', 'ltfieldmodel'=> (isset($user) ) ? $user->emailsignature : null , 'ltrequired'=>''])

@endif
	
@include('partials.form_password', ['fieldname'=>'password', 
								'fieldlabel'=>'Password'])
								
@include('partials.form_password', ['fieldname'=>'confirm-password', 
								'fieldlabel'=>'Confirm Password'])
								
	
    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
</div>
{!! Form::close() !!}
                </div>
				
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('scripts.src_select2', ['summernote'=>true])
@include('scripts.src_summernote')
@endpush

	
@section('js')

    <script>
	
		$("document").ready(function() {
	var contacttypes = @json($contact_types);
		
			@include('scripts.ready_select2')

@include('scripts.contacts.ready_contact_edit_js');
			
@include('scripts.ready_summernote');
		

		});
		
		
    </script>
@endsection