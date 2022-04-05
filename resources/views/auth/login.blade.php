 @extends('layouts.login')

@section('content')

  <div class="mb-3 mt-0 loginmerge">
    <div class="row justify-content-center align-items-center">
        <div class="col-sm-8 col-md-6 col-lg-4 col-12">
      <div class="login-logo text-center">
         <img src="emerge2.png">
      </div>
      </div>
    </div>
<form method="POST" action="{{ route('login') }}">
  @csrf
    <div class="row justify-content-center align-items-center">
      <div class="col-sm-8 col-md-6 col-lg-4 col-12">
                <input id="username" type="text" class="home home-text-input text-input @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="" autofocus placeholder="Username">
					{{-- <label for="username" class="home home-text-input text-input">{{ __('Username') }}</label> --}}

                       @error('username')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
        {{-- <input
          id="username"
          type="text"
          placeholder="Username"
          class="home home-text-input text-input"
        /> --}}
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-sm-8 col-md-6 col-lg-4 col-12">
          	<input id="password" type="password" class="home home-text-input text-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"  placeholder="Password">
				{{-- <label for="password" class="col-form-label text-md-right">{{ __('Password') }}</label> --}}

                     @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror


        {{-- <input
          type="password"
          placeholder="Password"
          class="home home-text-input text-input"
        /> --}}
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
        <div class="home checkbox-align col-sm-4 col-md-3 col-lg-2 col-12">
    <div class="checkox-remember-me custom-control custom-checkbox">
        <input type="checkbox" name="remember" class="custom-control-input" value="accepted" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="custom-control-label" for="remember"> {{ __('Remember Me') }}</label>
    </div>
</div>
<div class="home forgot-password-text-align col-sm-4 col-md-3 col-lg-2 col-12">
@if (Route::has('password.request'))

       <a class="forgot-password-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif

</div>


    </div>
    <div class="row justify-content-center align-items-center">
    <div class="col-sm-8 col-md-6 col-lg-4 col-12">
       <button type="submit" class="login-button button-padding">
                                    {{ __('Login') }}
                                </button>



    </div>
    </div>
</form>
    <div class="footer-img">
      <img src="../assets/bottom-login.png" />
    </div>
  </div>@endsection




