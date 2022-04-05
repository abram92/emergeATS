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
