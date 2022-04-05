@extends('layouts.admin')

@section('contentsearch')
		<form method="post" class="form-horizontal user searchForm" action="{{ url('users/search') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[name]',
														'filterVar'=> ((isset($q['name'])) ? $q['name'] : null),
														'filterPlaceholder'=> 'Filter Name',
														'advancedFilter'=>true, 
														'filterPrefix'=>'filter', 
														'isExpanded'=>false,
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> false])							

		<div id="filterBody" class="searchFilters searchForm user card-body collapse " aria-labelledby="filterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">

			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple_role', ['fieldname'=>'q[roles][]', 'fieldlabel'=>'Roles', 
									'fieldplaceholder'=>'Choose Roles', 
									'options'=>$roles,
									'fieldid'=>'rolesoptions',
									'selectedoptions'=>old('q[roles]', isset($q['roles']) ? $q['roles'] : null)])
					</div>
					<div class="col-md-6">				
									
@include('partials.filter_text_input', ['fieldname'=>'q[username]', 'fieldlabel'=>'Username', 
									'fieldplaceholder'=>'Filter Username', 
									'fieldvalue'=>(isset($q['username'])) ? $q['username']:''])
					</div>
				</div>
				</div>
						</div>
@include('partials.form_reset')											
						</div>
		</form>		
		<div class="topbar-divider d-none d-sm-block"></div>

@stop

@section('content')

<div class="container col-md-12">
	<div class="card">
		<div class="card-header user">
			<div class="row">
				<div class="col-md-8">
					<h3>{{ __('Users') }}</h3>
				</div>
				<div class="col-md-4 float-right">
					<a href="{{ route('users.create') }}" data-toggle="tooltip" title="Add New" class="btn btn-xs text-info  float-right"><i class="fa fa-plus-circle"></i></a>
				</div>
			</div>		
		</div>
		<div class="card-body">
			
@include('partials.flashmessages')

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Username</th>
				<th>Name</th>
				<th>Roles</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $user)
			<tr>
				<td>{{ $user->username }}</td>
				<td>{{ $user->listname }}</td>
				<td>
		@if(!empty($user->getRoleNames()))
			@foreach($user->getRoleNames() as $v)
					<label class="badge" @if ($roles[$v]) style="background-color:{{ $roles[$v] }}" @endif>{{ $v }}</label>
			@endforeach
		@endif
				</td>
				<td>
					<a href="{{ route('users.show',$user->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info bootpopup fas fa-info" target="popupModal2"></a>
					<a href="{{ route('users.edit',$user->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit"></a>
		@if (Auth::id() != $user->id)
				{!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fas fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}
		@endif	
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@include('partials.show_pagination')	
	
@else
	@include('partials.emptytable')
@endif


		</div>
	</div>
</div>

@endsection

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')


    <script>
	
		$("document").ready(function() {
		
			@include('scripts.ready_select2')

			@include('scripts.ready_select2_form_reset')


		});		
    </script>
@endsection	
