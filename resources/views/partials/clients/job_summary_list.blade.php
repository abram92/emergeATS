@if ($jobs->count() > 0)
	@php
      $cnt = $jobs->where('activated_at', '>', date('Y-m-d', strtotime('-30 days')))->count();
	@endphp  
		<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 6, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [8]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
		<thead class="table-dark">
			<tr>
				<th>Ref Code</th>
				<th>Contacts</th>
				<th>Job Title</th>
				<th>Salary</th>
				<th>Location</th>
				<th>Consultant</th>
				<th>Upload Date</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobs as $key => $job)
			<tr >
				<td>{{ $job->jobref }}
				</td>
				<td>@include('partials.clients.jobcontacts', ['contacts'=>$job->clientcontacts])
				</td>
				<td>{{ $job->jobtitle_text }}
				</td>
				<td>{{ $job->salary_from }}
				</td>
				<td>{{ optional($job->location)->description }}</td>
				
				<td>{{ optional($job->consultant)->listname }}</td>
<td>@include('partials.formatteddate', ['dt'=>$job->activated_at])</td>
<td>@include('partials.show_status', ['status'=>$job->status])</td>
				<td>
				@include('partials.list_job_actions', ['dropdownAlign'=>'dropdown-menu-right'])
				</td>
				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@endif

@includeWhen(!isset($toggledJS), 'scripts.toggledCommentJS')
