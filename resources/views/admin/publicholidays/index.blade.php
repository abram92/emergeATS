@extends('layouts.admin')

@section('title', 'Public Holidays')

@section('content')

<div class="container col-md-12">
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-8">
					<h3>{{ __('Public Holidays') }}</h3>
				</div>
				<div class="col-md-4 float-right">
					<a href="{{ route('admin.publicholidays.create') }}" data-toggle="tooltip" title="Add New" class="btn btn-xs text-info fa fa-plus-circle float-right"></a>
				</div>
			</div>

		</div>
		<div class="card-body">
			
@include('partials.flashmessages')
<div class="card-deck">
@if (count($recurring) > 0)
<div class="card">
<h5 class="card-header">Recurring</h5>
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Date</th>
				<th>Description</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($recurring as $key => $holiday)
			<tr>
				<td>{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('j F') }}</td>
				<td>{{ $holiday->description }}</td>
				<td>
					<a href="{{ route('admin.publicholidays.edit',$holiday->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit" target="holiday{{ $holiday->id }}"></a>
		@if (!isset($item->deletable) || $item->deletable)	
				{!! Form::open(['method' => 'DELETE','route' => ['admin.publicholidays.destroy', $holiday->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fa fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
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
</div>
@else
	@include('partials.emptytable')
@endif

@if (count($yearSpecific) > 0)
<div class="card">
<h5 class="card-header">Year Specific</h5>
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Date</th>
				<th>Description</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($yearSpecific as $yearkey => $yearholiday)
		<tr><th class="table-primary text-center" colspan=3>{{ $yearkey }}</th></tr>
		@foreach ($yearholiday as $key => $holiday)
			<tr>
				<td>{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('j F') }}</td>
				<td>{{ $holiday->description }}</td>
				<td>
					<a href="{{ route('admin.publicholidays.edit',$holiday->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit" target="holiday{{ $holiday->id }}"></a>
		@if (!isset($item->deletable) || $item->deletable)	
				{!! Form::open(['method' => 'DELETE','route' => ['admin.publicholidays.destroy', $holiday->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fa fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}
		@endif	
				</td>
			</tr>
		@endforeach
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	</div>
@else
	@include('partials.emptytable')
@endif
</div>
		</div>
	</div>
</div>

<div id="popupModal2" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body">
      <iframe src="" style="zoom:0.60" frameborder="0" height="250" width="99.6%"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">OK</button>
	</div>
</div>
@endsection 

@section('js')


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection	
