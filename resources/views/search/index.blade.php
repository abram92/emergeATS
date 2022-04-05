@extends('layouts.admin')


@section('content')

<div class="container col-md-12">
	<div class="card">
		<div class="card-header savedsearch">
			<div class="row">
				<div class="col-md-12">
					<h3>{{ __('Saved Searches') }}</h3>
				</div>
			</div>
		</div>
		<div class="card-body">

@include('partials.flashmessages')

@if (count($data) > 0)


	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Description</th>
				<th>Date Saved</th>
				<th>Search Parameters</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
	@php  $current = ""; @endphp
		@foreach ($data as $key => $search)
		@if ($current != $search->search_type)
			<tr><td colspan=4 class="{{ $search->search_type }}"><h4>{{ ucfirst($search->search_type) }}
			</td></tr>
		@endif
			<tr>
				<td>{{ $search->description }}</td>
				<td>{{ $search->saved_at }}</td>
				<td class="p-0">@include('partials.json_table', ['jsondata'=>json_decode($search->filtercriteria)])</td>
				<td>
					<a href="{{ url($search->search_type.'s/search?search_id='.$search->id) }}" title="Search" target="search{{ $search->id }}"><i class="fa fa-search"></i></a>
		@if (!isset($item->deletable) || $item->deletable)
				{!! Form::open(['method' => 'DELETE','route' => ['savedsearch.destroy', $search->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fa fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete', 'onclick' => 'return confirm("Are you sure?")']) !!}
				{!! Form::close() !!}
		@endif
				</td>
			</tr>
		@php  $current = $search->search_type; @endphp
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@else
	@include('partials.emptytable')
@endif


		</div>
	</div>
</div>

@endsection

