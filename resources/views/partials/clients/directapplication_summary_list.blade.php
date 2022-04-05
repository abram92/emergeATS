
@if ($directapplications->count() > 0)

	<div class="table-responsive">
	<table id="@php echo $tableid; @endphp" class="table table-bordered table-striped datatable filterable">
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Candidate Job Title</th>
				<th>Date Sent</th>
				<th>Contact</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($directapplications as $key => $jobappl)
			<tr 			@if ($jobappl->comments)
				data-child-value-0="{{$jobappl->comments}}"
			@endif
			>
				<td>{{ $jobappl->candidate->user->listname }}
				</td>
				<td>@include('partials.candidate_email_string_list', ['candidateemailstring'=>$jobappl->candidate->user->email])
				</td>
				<td>{{ $jobappl->candidate->jobtitle_text }}</td>
				
				<td>{{ optional($jobappl->emailevents->first())->time_start }}</td>
<td>{{ optional(optional(optional($jobappl->emailevents->first())->clientcontacts)->first())->listname }}</td>
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
@include('scripts.datatableChildHideShowShortlistSummary', ['tableid'=>$tableid])
