
@if (count($data) > 0)
<div class="card card-header job">My Jobs</div>
<div class="card card-body pt-0 pl-0 pr-0 job-outline">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
	<th>Job Ref</th>
	<th>Client</th>
	<th>Consultant</th>
	<th>Status</th>
	<th>Date Activated</th>
	<th>Actions</th>
	</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $job)
		<tr>
	<td>{{ $job->jobref }}</td>
	<td>{{ $job->client->name }}</td>
	<td>@include('partials.list_consultant',['cons'=>$job->consultant])</td>
	<td>@include('partials.show_status', ['status'=>$job->status])</td>
	<td>@include('partials.list_date_format',['dt'=>$job->activated_at])</td>
	<td>			<div class="row">
				@include('partials.list_job_actions', ['dropdownAlign'=>'dropdown-menu-right'])
				</div>
</td>
</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@include('partials.show_pagination')	
	
</div>		


@endif


