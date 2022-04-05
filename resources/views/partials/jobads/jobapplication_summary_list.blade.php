@if ($jobapplications->count() > 0)
	@php
      $cnt = 0;
	@endphp  
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 3, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [6]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Email Address</th>
				<th>Candidate Job Title</th>
				<th>Date Sent</th>
				<th>Contacts</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobappl)
			<tr >
				<td>{{ $jobappl->candidate->user->listname }}
				</td>
				<td>@include('partials.candidate_email_string_list', ['candidateemailstring'=>$jobappl->candidate->user->email])
				</td>
				<td>{{ $jobappl->candidate->jobtitle_text }}
				</td>				
				<td>{{ optional($jobappl->emailevents->first())->time_start }}</td>
<td>{{ optional($jobappl->emailevents->first())->clientcontacts->implode('listname', ', ') }}</td>
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
