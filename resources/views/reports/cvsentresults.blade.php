

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Create Date</th>
				<th>Consultant</th>
				<th>Candidate</th>
				<th>Client</th>
				<th>Contact</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $email)
			<tr>
				<td>{{ \Carbon\Carbon::parse($email->created_at)->format('Y-m-d H:i') }}</td>
				<td  @if($email->owner->trashed()) class="text-muted" @endif> {{ optional($email->owner)->listname }} </td>
				<td>@foreach ($email->candidates as $key1 => $candidate) @if($key1) {!! nl2br(",\r\n") !!} @endif   {{ optional($candidate->user)->listname }} @endforeach</td>
				<td>@if(!$email->clients->isEmpty()) @foreach ($email->clients as $key1 => $client) @if($key1) {!! nl2br(",\r\n") !!} @endif   {{ $client->name }} @endforeach
				    @else
					@foreach ($email->jobs as $key1 => $job) @if($key1) {!! nl2br(",\r\n") !!} @endif  {{ $job->client->name }} @endforeach	
					@endif</td>
				<td>@foreach ($email->clientcontacts as $key1 => $clientcontact) @if($key1) {!! nl2br(",\r\n") !!} @endif  {{ $clientcontact->listname }} @endforeach</td>
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


