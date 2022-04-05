@if ($duplicates->count() > 0)
	<div class="card">
		<div class="card-header">
			<h4>Duplicate Records</h4>
		</div>
		<div class="card-body pt-0 pl-0 pr-0">
	<div class="table-responsive">
	<table id="duplicates" class="table table-bordered table-striped datatable filterable" data-order='[[ 1, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [7]}]' >
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Date Activated</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($duplicates as $key => $duplicate)
			<tr>
				<td>{{ $duplicate->user->listname }}
				</td>			
				<td>{{ $duplicate->activated_at }}</td>
				<td>@include('partials.show_status', ['status'=>$duplicate->status])</td>
				<td>
<div class="row">
					<a href="{{ route('candidates.show',$duplicate->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fas fa-info" target="candidate{{ $duplicate->id }}"></a>
					<a href="{{ route('candidatemerge', [$candidate->id, $duplicate->id]) }}" data-toggle="tooltip" title="Merge Target" class="btn btn-sm text-primary fa fa-clone"></a>

</div>
				</td>
				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
		</div>
	</div>
@endif
