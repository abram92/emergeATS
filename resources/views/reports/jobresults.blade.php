

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">		
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Job Ref</th>
				<th>Client</th>
				<th>Job Title</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $job)
			<tr>
				<td>{{ $job->jobref }}</td>
				<td>{{ $job->client->name }}</td>
				<td>{{ $job->jobtitle_text }}</td>
				<td width="180px">@include('partials.list_job_actions', ['dropdownAlign'=>'dropdown-menu-right'])</td>			
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


