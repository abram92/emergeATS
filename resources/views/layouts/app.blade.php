<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<!--    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">  -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
	@stack('scripts')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
					
					 		<li class="nav-item dropdown">
                                <a id="navbarManage" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('Admin') }} <span class="caret"></span>
                                </a>


                                <div class="dropdown-menu" aria-labelledby="navbarManage">
								    <a class="dropdown-item" href="{{ route('users.index') }}">Users</a>
									<a class="dropdown-item" href="{{ route('admin.locations.index') }}">Locations</a>
									<div class="dropdown-divider"></div>
									<h6 class="dropdown-header">Clients</h6>
                                    <a class="dropdown-item" href="{{ route('admin.clientstatus.index') }}">Status</a>
									<div class="dropdown-divider"></div>
									<h6 class="dropdown-header">Candidates</h6>
                                    <a class="dropdown-item" href="{{ route('admin.candidatestatus.index') }}">Status</a>
									<a class="dropdown-item" href="{{ route('admin.candidatelevels.index') }}">Levels</a>
									<a class="dropdown-item" href="{{ route('admin.candidateratings.index') }}">Ratings</a>
									<a class="dropdown-item" href="{{ route('admin.eestatus.index') }}">EE Status</a>
                                    <div class="dropdown-divider"></div>
									<h6 class="dropdown-header">Jobs</h6>
                                    <a class="dropdown-item" href="{{ route('admin.jobstatus.index') }}">Status</a>
									<a class="dropdown-item" href="{{ route('admin.jobtypes.index') }}">Types</a>
									<a class="dropdown-item" href="{{ route('admin.jobtitles.index') }}">Titles</a>
									<div class="dropdown-divider"></div>
								    <a class="dropdown-item" href="{{ route('admin.salarycategories.index') }}">Salary Categories</a>
									<div class="dropdown-divider"></div>
								    <a class="dropdown-item" href="{{ route('admin.eventtypes.index') }}">Event Types</a>
									<div class="dropdown-divider"></div>
								    <a class="dropdown-item" href="{{ route('admin.aliascategories.index') }}">Alias Categories</a>
									
                                </div>
                            </li>
                        

                    </ul>
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Add') }}</a>
                        </li>

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.edit',[Auth::user()->id]) }}">
                                        {{ __('Profile') }}
                                    </a>

                                </div>
								
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

@yield('js')
</body>
</html>
