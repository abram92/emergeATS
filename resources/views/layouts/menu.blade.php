<!DOCTYPE html>
<html lang="en">
@php
$isAdmin = Auth::user()->hasRole('Admin');
$isExporter = Auth::user()->hasRole('Data Exporter');
@endphp
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

  <!-- Custom fonts for this template-->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
<!--  <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet"> -->

	    <link rel="stylesheet" href="{{ asset('css/styles-sb.css') }}">
	    <link rel="stylesheet" href="{{ asset('css/style1.css') }}">
	    <link rel="stylesheet" href="{{ asset('css/floating-labels.css') }}">

  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

  <!-- Custom scripts for all pages-->
<!--    <script src="{{ asset('/js/sb-admin-2.min.js') }}"></script> -->
  <script src="{{ asset('js/submit.js') }}"></script>

	@stack('scripts')
</head>

<body id="page-top" class="side-nav">
        <!-- Topbar -->
<!--		           <nav class="navbar navbar-expand-lg navbar-dark bg-dark topbar sticky-top static-top shadow">  -->

        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow">

                      <a class="navbar-brand d-flex align-items-center justify-content-center" href="{{ url('/home') }}">
        <div class="navbar-brand-icon">
                <img class="img-profile" src="{{ asset('favicon.ico') }}">
        </div>
        <div class="navbar-brand-text mx-3"></sup></div>
      </a>


         <!-- Sidebar Toggle (Topbar) -->
            <button class="btn btn-link btn-sm d-lg-none order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>


         <!-- Add Shortcuts -->
				{{-- <div class="topadd">
					<a href="{{ route('candidates.create') }}" data-toggle="tooltip" title="Add Candidate" target="_blank" class="btn btn-xs btn-navbar">@include('partials.icons.add_candidate')</a>
				</div> --}}
				<div class="topadd">
					<a href="{{ route('clients.create') }}" data-toggle="tooltip" title="Add Client" target="_blank" class="btn btn-xs btn-navbar">@include('partials.icons.add_client')</a>
				</div>
				{{-- <div class="topadd">
					<a href="{{ route('clients.index') }}" data-toggle="tooltip" title="Add Job" class="btn btn-xs btn-navbar">@include('partials.icons.add_job')</a>
				</div> --}}

          <!-- Topbar Search -->
			@yield('contentsearch')


          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">


            <!-- Nav Item - Static Alerts -->
@if (isset($staticalerts_count) && $staticalerts_count)
	<button class="btn " type="button" title="Static Work" data-toggle="modal" data-target="#staticWork">
		<i class="fas text-danger fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">{{ $staticalerts_count }}</span>
    </button>
@endif

            <!-- Nav Item - Alerts -->
@if (isset($notifications))
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas evnt fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">{{ $notifications->count }}</span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts
                </h6>
				@foreach ($notifications as $notification)
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle {{$notification->class }}">
                      <i class="{{$notification->icon }}"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">{{$notification->date }}</div>
                    <span class="font-weight-bold">{{$notification->message }}</span>
                  </div>
                </a>
				@endforeach
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
              </div>
            </li>
@endif
            <!-- Nav Item - Messages -->
@if (isset($messages))
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">{{ $messages->count }}</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Messages
                </h6>
				@foreach ($messages as $message)
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" {{$message->class }}>
                    <div class="status-indicator {{$message->status }}"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">{{$message->note }}</div>
                    <div class="small text-gray-500">{{$message->sender }} · {{$message->date }}</div>
                  </div>
                </a>
				@endforeach
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
              </div>
            </li>
@endif
            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->username }}</span>
                <i class="fa fa-caret-down fa-sm fa-fw mr-2" style="color:black;"></i>

              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('users.edit',[Auth::user()->id]) }}">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  {{ __('Profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  {{ __('Logout') }}
                </a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
              </div>
            </li>

          </ul>



        </nav>
		<!-- End of Topbar -->
<div id="layoutSidenav">
    {{-- <div class="col-sm-4 col-md-3 col-lg-2 col-2">
      <div class="row side-nav"><div class="col-12">
        <div class="side-nav-logo text-center pt-4 mb-4">
            <img src="/img/emerge-logo.f87b7bc8.png">
        </div>
        <a class="row align-items nav-rows" href="{{ url('/home') }}" title="Home">

            <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4 col-12">
                <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="book" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-book b-icon bi">
                    <g><path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z">
                        </path>
                    </g>
                </svg>
            </div>

            <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Home</div>
         </a>
         <div class="row align-items nav-rows-active">
            <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="circle" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-circle b-icon bi">
                    <g>
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z">
                            </path>
                        </g>
                    </svg>
                </div>
                <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">My Condidates</div>
            </div>
            <div class="row align-items nav-rows">
                <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                    <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="people" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-people b-icon bi">
                        <g>
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z">
                                </path>
                            </g>
                        </svg>
                    </div>
                    <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Clients</div>
                </div>
                <div class="row align-items nav-rows"><div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                    <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="building" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-building b-icon bi">
                        <g>
                            <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694L1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z">
                                </path>
                                <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-4 col-lg-8">Jobs</div>
                    </div>
                    <div class="row align-items nav-rows">
                        <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                            <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="calendar" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-calendar b-icon bi">
                                <g>
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z">
                                        </path>
                                    </g>
                                </svg>
                            </div>
                            <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Calendar</div>
                        </div>
                        <div class="row align-items nav-rows">
                            <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                                <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="printer" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-printer b-icon bi">
                                    <g>
                                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z">
                                            </path>
                                            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z">
                                                </path>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Report</div>
                                </div>
                                <div class="row align-items nav-rows">
                                    <div class="color-grey text-center p-0 col-sm-3 col-md-4 col-lg-4">
                                        <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="tools" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-tools b-icon bi">
                                            <g>
                                                <path d="M1 0L0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.356 3.356a1 1 0 0 0 1.414 0l1.586-1.586a1 1 0 0 0 0-1.414l-3.356-3.356a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0zm9.646 10.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708zM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </div>
                                        <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Settings</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div> --}}



            <div id="layoutSidenav_nav">
<nav class="sb-sidenav floating-menu">
            <ul id="sidebaritems" class="list-unstyled">

      <!-- Sidebar - Brand -->


      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/home') }}" title="Home">
          <i class="fas fa-fw fa-home"></i>
          <div class="d-none d-lg-block d-md-block d-sm-block col-sm-9 col-md-8 col-lg-8">Home</div>
          <span>Home</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">


     <!-- Nav Item - Candidates -->
      <li class="candidate nav-item">
        <a class="nav-link" href="{{ url('candidates') }}" title="Candidate Search">
          <i class="fas fa-fw fa-users"></i>
          <span>Candidates</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

     <!-- Nav Item - Jobs -->
      <li class="job nav-item">
        <a class="nav-link" href="{{ url('jobs') }}" title="Job Search">
          <i class="fas fa-fw fa-briefcase"></i>
          <span>Jobs</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

     <!-- Nav Item - Clients -->
      <li class="client nav-item">
        <a class="nav-link" href="{{ url('clients') }}"  title="Client Search">
          <i class="fas fa-fw fa-building"></i>
          <span>Clients</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <li class="savedsearch nav-item">
        <a class="nav-link" href="{{ url('savedsearches') }}"  title="Saved Searches">
          <i class="fas fa-fw fa-search"></i>
          <span>Saved Searches</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

     <!-- Nav Item - Calendar -->
      <li class="calendar nav-item">
        <a class="nav-link" href="{{ url('calendarevents') }}" title="Calendar">
          <i class="fas fa-fw fa-calendar-alt"></i>
          <span>Calendar</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

@if ($isExporter || $isAdmin)
      <!-- Nav Item - Report Collapse Menu -->
      <li class=" report nav-item">
        <a class="nav-link " href="#collapseReport" data-toggle="modal" data-target="#menuReports" title="Reports">
          <i class="fa fa-fw fa-file-alt"></i>
          <span>Reports</span>
        </a>

      </li>
      <!-- Divider -->
      <hr class="sidebar-divider my-0">

@endif
@if ($isAdmin)

      <li class="settings nav-item">
       <a class="nav-link " href="#collapseSettings" data-toggle="modal" data-target="#menuSettings" title="Settings">
          <i class="fa fa-fw fa-cogs"></i>
          <span>Settings</span>
        </a>

	  </li>
      <!-- Divider -->
      <hr class="sidebar-divider my-0">

@endif

    </ul>
	</nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
            <div class="content">
                <div class="{{config('adminlte.classes_content', 'container-fluid')}}">
                    @yield('content')
        @hasSection('contentfooter')
		@yield('contentfooter')
        @endif
				</div>

            </div>
                    </div>
                </main>
        @hasSection('footer')
        <footer class="main-footer">

            @yield('footer')

        </footer>
        @endif
            </div>
        </div>


@if ($isExporter || $isAdmin)

<div class="modal fade" id="menuReports" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="report modal-header">
                <h5 class="modal-title">Reports</h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">

				<div class="card-columns">

      <!-- Nav Item - Users -->
					<div class="card user">
							<h5 class="card-header user">
								<i class="fas fa-fw fa-users-cog"></i>
								<span>Users</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">

@if ($isExporter)
           <li class="list-group-item">
			<a class="collapse-item" href="{{ url('reports/useractivity') }}">User Activity</a>
          </li>
           <li class="list-group-item">
            <a class="collapse-item" href="{{ url('reports/staticalerts') }}">Static Alerts</a>
          </li>
@endif
@if ($isAdmin)
           <li class="list-group-item">
            <a class="collapse-item" href="{{ url('reports/cvsent') }}">Cvs sent by consultant</a>
          </li>
@endif
						</ul>
					</div>

      <!-- Nav Item - Candidates -->
					<div class="card candidate">
							<h5 class="card-header candidate">
              <i class="fa fa-fw fa-users"></i>
               <span>Candidates</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class=" collapse-item" href="{{ url('reports/candidates') }}">List</a>
							</li>
@if ($isAdmin)
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('reports/candidatehistory') }}">Candidate History</a>
							</li>
@endif
@if ($isAdmin)
           <li class="list-group-item">
            <a class="collapse-item" href="{{ url('reports/cvsent') }}">Cvs sent by consultant</a>
          </li>
@endif
@if ($isExporter)
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('reports/linkedcandidates') }}">Linked Candidates</a>
							</li>
@endif
						</ul>
					</div>

      <!-- Nav Item - Client Collapse Menu -->
					<div class="card client">
							<h5 class="card-header client">
								<i class="fas fa-fw fa-building"></i>
								<span>Clients</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('reports/clients') }}">List</a>
							</li>
						</ul>
					</div>

      <!-- Nav Item - Jobs Collapse Menu -->
					<div class="card job">
							<h5 class="card-header job">
								<i class="fas fa-fw fa-briefcase"></i>
								<span>Jobs</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('reports/jobs') }}">List</a>
							</li>
@if ($isAdmin)
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('reports/jobhistory') }}">Job History</a>
							</li>
@endif
						</ul>
					</div>


				</div>
			</div>
		</div>
	</div>
</div>

@endif

@if ($isAdmin)

<div class="modal fade" id="menuSettings" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="settings modal-header">
                <h5 class="modal-title" >Settings</h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">

				<div class="card-columns">

      <!-- Nav Item - Users -->
					<div class="card user">
							<h5 class="card-header user">
								<i class="fas fa-fw fa-users-cog"></i>
								<span>Users</span>
							</h5>
						<ul class="list-group list-group-flush">
						    <li class="list-group-item">
								<a class="nav-link" href="{{ url('users') }}"><i class="fas fa-fw fa-users"></i>
									<span>Admin</span>
								</a>
							</li>
						    <li class="list-group-item">
								<a class="nav-link" href="{{ url('teams') }}"><i class="fas fa-fw fa-users"></i>
									<span>Teams</span>
								</a>
							</li>
						</ul>
					</div>



      <!-- Nav Item - Candidates Collapse Menu -->
					<div class="card candidate">
							<h5 class="card-header candidate">
								<i class="fas fa-fw fa-users"></i>
								<span>Candidates</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('candidatestatus') }}">Statuses</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('candidatelevels') }}">Levels</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('candidateratings') }}">Ratings</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('candidateavailabilities') }}">Availability</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('candidatesarchive') }}">Bulk Archive</a>
							</li>
						</ul>
					</div>



      <!-- Nav Item - Client Collapse Menu -->
					<div class="card client">
							<h5 class="card-header client">
								<i class="fas fa-fw fa-building"></i>
								<span>Clients</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('clientstatus') }}">Statuses</a>
							</li>
						</ul>
					</div>

      <!-- Nav Item - Jobs Collapse Menu -->
					<div class="card job">
							<h5 class="card-header job">
								<i class="fas fa-fw fa-briefcase"></i>
								<span>Jobs</span>
							</h5>
						<ul class="list-group list-group-flush text-dark">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('jobstatus') }}">Statuses</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('jobtitles') }}">Titles</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('jobtypes') }}">Types</a>
							</li>
						</ul>
					</div>

      <!-- Nav Item - Alias Collapse Menu -->
					<div class="card alias">
							<h5 class="card-header alias">
								<i class="fas fa-fw "></i>
								<span>Skills</span>
							</h5>
						<ul class="list-group list-group-flush">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('aliascategories') }}">Areas Of Specialisation</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('aliases') }}">Skills & Keywords</a>
							</li>
						</ul>
					</div>

      <!-- Nav Item - Other Collapse Menu -->
					<div class="card settings">
							<h5 class="card-header settings">
								<i class="fas fa-fw "></i>
								<span>Misc. Lists</span>
							</h5>
						<ul class="list-group list-group-flush">
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('eestatus') }}">EE Status</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('eventtypes') }}">Event Types</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('genders') }}">Genders</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('jobapplicationstatus') }}">Job Application Statuses</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('locations') }}">Locations</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('publicholidays') }}">Public Holidays</a>
							</li>
							<li class="list-group-item">
								<a class="collapse-item" href="{{ url('salarycategories') }}">Salary Categories</a>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
@endif




@yield('js')

	<script src="{{ asset('/js/sidebar.js') }}"></script>
</body>

</html>
