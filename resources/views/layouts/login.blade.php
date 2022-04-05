<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Scripts -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
	    <link rel="stylesheet" href="{{ asset('css/style1.css') }}">
	    <link rel="stylesheet" href="{{ asset('css/floating-labels.css') }}">
	
  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

  <script src="{{ asset('js/submit.js') }}"></script>	
	
	@stack('scripts')
	
</head>
<body>
    <div id="app">
		@auth
        <nav class="navbar navbar-expand-md sticky-top bg-light shadow-sm mb-0 p-1">
            <div class="container">

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
        <div class="sidebar-brand-icon">
                <img class="img-profile" src="{{ asset('favicon.ico') }}">
        </div>

                    <!-- Right Side Of Navbar -->
                          @guest
						  @else
                  <ul class="navbar-nav ml-auto">
			  		  
                        <!-- Authentication Links -->
                            <li>
                                    <span class="text-gray-600">Logged in as {{ Auth::user()->username }}</span> 
                            </li>
                    </ul>
					@endguest
                </div>
            </div>
        </nav>
		@endauth
        <main class="">
            @yield('content')
        </main>
    </div>


@yield('js')
</body>
</html>
