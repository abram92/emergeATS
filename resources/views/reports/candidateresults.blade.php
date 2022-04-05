

@include('partials.searchfilters.query_criteria_block')
@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Job Title</th>
				<th>Salary</th>
				<th>Preferred Location</th>
				<th>Consultant</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $candidate)
			<tr>
				<td>{{ $candidate->user->listname }}</td>
				<td>{{ $candidate->jobtitle_text }}</td>
				<td>{{ $candidate->salary }}</td>
				<td>{!! nl2br(e(implode(PHP_EOL,optional($candidate->preferredlocations)->pluck('description')->toArray()))) !!}</td>				
				<td>{{ optional($candidate->consultant)->listname }}</td>
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


