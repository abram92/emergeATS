@if ($jobapplications->count() > 0)
	@php
      $cnt = $jobapplications->where('created_at', '>', date('Y-m-d', strtotime('-30 days')))->count();
	  $canBulkSend =  (Auth::user()->hasRole('Bulk Email Candidates'));
	@endphp  
			@if ($canBulkSend)
				<form method="post" target="_blank" action="{{ route('emailjobspectocandidates.create',$jobad->id) }}" name="emailjobspectocandidates">
	<div class="table-responsive">
	<table id='prospectsummary' class="table table-bordered table-striped datatable filterable" data-order='[[ 4, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [0,7]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
@else
	<div class="table-responsive">
	<table id='prospectsummary' class="table table-bordered table-striped datatable filterable" data-order='[[ 3, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [6]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
	@endif

		<thead class="table-dark">
			<tr>
			@if ($canBulkSend)
				<th></th>
			@endif
				<th>Name</th>
				<th>Email Address</th>
				<th>Candidate Job Title</th>
				<th>Date Linked</th>
				<th>Consultant</th>
				<th>Status</th>
				<th>Job Spec Sent</th>
				<th>Upload Date</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobappl)
			<tr @if ($jobappl->jobad->cvsendinstructions)
				data-child-value-1="{{$jobappl->jobad->cvsendinstructions->chunk}}"
			@endif @if ($jobappl->comments)
				data-child-value-0="{{$jobappl->comments}}"
			@endif  @if ($jobappl->candidate->agencynotes)
				data-child-value-2="{{$jobappl->candidate->agencynotes->chunk}}"
			@endif>
			@if ($canBulkSend)			
				<td><input type="checkbox" name="applIds[]" class="chk"  value="{{ $jobappl->id }}_{{ $jobappl->candidate->id }}">
				</td>
			@endif	
				<td>{{ $jobappl->candidate->user->listname }}
				</td>
				<td>@include('partials.candidate_email_string_list', ['candidateemailstring'=>$jobappl->candidate->user->email])
				</td>				
				<td>{{ $jobappl->candidate->jobtitle_text }}
				</td>
				<td>{{ $jobappl->created_at }}</td>
				<td>{{ optional($jobappl->candidate->consultant)->fullname_username }}</td>
<td>@include('partials.show_status', ['status'=>$jobappl->status])</td>
				<td>{{ optional($jobappl->emailevents->first())->time_start }}</td>
				<td>{{ $jobappl->candidate->activated_at }}</td>
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
			@if ($canBulkSend)	
	<button class="btn btn-primary" type="submit" id="emailjobspec">Email Job Spec to Candidates</button>
						@csrf
	</form>
			@endif
@endif

@includeWhen(!isset($toggledJS), 'scripts.toggledCommentJS')
@include('scripts.datatableChildHideShowShortlistSummary', ['tableid'=>'prospectsummary'])