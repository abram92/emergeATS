

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Consultant</th>
				<th>Candidates Edited</th>
				<th>Candidates Loaded</th>
				<th>Candidates Active</th>
				<th>CVs Sent</th>
				<th>Jobs Active</th>
				<th>Total Jobs</th>
				<th>Clients Loaded</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $user)
			<tr>
				<td>{{ $user->fullname_username }}</td>
				<td>{{ $user->candidates_edited_count }}</td>
				<td>{{ $user->candidates_loaded_count }}</td>
				<td>{{ $user->candidates_active_count }}</td>
				<td>{{ $user->cv_sent_count }}</td>
				<td>{{ $user->jobs_active_count }}</td>
				<td>{{ $user->jobs_total_count }}</td>
				<td>{{ $user->clients_loaded_count }}</td>
			</tr>

		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@include('partials.show_pagination')	
</div>
@else
@if ($q)	
	@include('partials.emptytable')
@endif
@endif


