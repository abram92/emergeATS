@if ($jobapplications->count() > 0)
	@php
      $cnt = $jobapplications->where('emailevents_count', '>', 0)->count();
	@endphp  
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable  filterable" data-order='[[ 5, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [7]}]'  @if($cnt) data-page-length='{{ $cnt }}'@endif>
		<thead class="table-dark">
			<tr>
				<th>Reference Code</th>
				<th>Job Title</th>
				<th>Job Status</th>
				<th>Contacts</th>
				<th>Company</th>
				<th>Date Sent</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobappl)
			<tr >
				<td>{{ $jobappl->jobad->jobref }}
				</td>
				<td>{{ $jobappl->jobad->jobtitle_text }}
				</td>
<td>@include('partials.show_status', ['status'=>$jobappl->jobad->status])</td>
<td>{{ optional($jobappl->jobad->clientcontacts)->implode('listname', ', ') }}</td>
				<td>{{ $jobappl->jobad->client->name }}</td>
				
				<td>{{ optional($jobappl->emailevents->first())->time_start }}</td>
<td>@include('partials.show_status', ['status'=>$jobappl->status])</td>
				<td>
				@include('partials.list_jobapplication_actions')
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
