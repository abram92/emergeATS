<div class="card audit">
@if ($audit->count() == 0)
	<div class="card-header text-muted mb-0 bg-light ">
					<h5>Audit Trail</h5>
	</div>
@else
        <div class="card-header card-title" id="auditheading" data-toggle="collapse" data-target="#auditbody" aria-expanded="false" aria-controls="auditbody">
					<h4>Audit Trail</h4>
        </div>
        <div id="auditbody" class="collapse card-body" aria-labelledby="auditheading" >
@if ($audit->count() > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>User</th>
				<th>Action</th>
				<th>Status</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody id="baseTable">

		@foreach ($audit as $key => $audit_record)
			<tr>
				<td @if($audit_record->user) @if($audit_record->user->deleted_at) class="text-muted" @endif>{{ $audit_record->user->listname }} @endif</td>
				<td>{{ $audit_record->event }}</td>
				<td>	@if ((isset($audit_record->new_values['status_id'])) && isset($statusArr))

@php $status_idx = 	$audit_record->new_values['status_id']; @endphp		

@php $audit_status = $statusArr->filter(function ($item) use ($status_idx) {
    return $item['id'] == $status_idx;
}); @endphp

@include('partials.show_status', ['status'=>collect($audit_status->first())])
@endif</td>
				<td>{{ $audit_record->created_at }}</td>
			</tr>
	@if (isset($audit_record->new_values['comments']))
	 <tr class="hidden"></tr>
	 <tr><td colspan="4"><pre>{{ $audit_record->new_values['comments'] }}</pre></td></tr>
	@endif		
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@endif
        </div>
@endif		
</div>
