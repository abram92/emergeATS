@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
		<div class="col-md-12">	
            <div class="card">
                <div class="card-header user"><h3>{{ __('User:'.$user->listname) }}</h3></div>

                <div class="card-body">
                    @csrf

<div class="card-group">
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'First Name',
															'fieldvalue'=> old('firstname', $user->firstname ) ])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Last Name',
															'fieldvalue'=> old('lastname', $user->lastname ) ])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Username',
															'fieldvalue'=> old('username', $user->username )
															])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Job Code',
															'fieldvalue'=> old('jobcode', $user->jobcode )
															])
</div>

</div>	
<hr>
@include('partials.users.contacts_view')
					
<hr>					
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="roles" class="col-md-4 text-md-right">{{ __('Roles') }}</label>
							<div class="col-md-6" id="roles">
						@if(!empty($user->getRoleNames()))
							@foreach($user->getRoleNames() as $v)
								<span class="badge" @if ($roles[$v]) style="background-color:{{ $roles[$v] }}" @endif>{{ $v }}</span>
							@endforeach
						@endif
							</div>
						
						</div>
					</div>
@if($user->emailsignature)					
<hr>					
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Email Signature',
															'fieldhtml'=> old('emailsignature', $user->emailsignature )
															])
</div>
@endif
                    <div class="form-group row mb-0">

						<div class="col-md-8 offset-md-4">
							<a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
						</div>
					</div>
				</div>		
			</div>
		</div>
    </div>
</div>
 
@endsection