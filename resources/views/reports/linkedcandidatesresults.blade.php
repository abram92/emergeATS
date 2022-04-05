

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">		
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Job Ref</th>
				<th>Firstname</th>
				<th>Surname</th>
				<th>Email Address</th>
				<th>Job Title</th>
				<th>Date Linked</th>
				<th>Consultant</th>
			</tr>
   $headers = array("Job Ref", "Firstname", "Surname", "Email Address", "Job Title", "Date Linked", "Consultant");
			
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $linkedcandidate)
			<tr>
				<td>{{ $linkedcandidate->jobad->refno }}</td>
				<td>{{ $linkedcandidate->user->firstname }}</td>
				<td>{{ $linkedcandidate->user->lastname }}</td>
				<td>@include('partials.candidate_email_string_list', ['candidateemailstring'=>$linkedcandidate->user->email])</td>
				<td>{{ $linkedcandidate->jobtitle }}</td>
				<td>{{ $linkedcandidate->timestamp }}</td>
				<td>{{ $linkedcandidate->candidate->consultant->fullname }}</td>
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


