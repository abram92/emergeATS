

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Timestamp</th>
				<th>Consultant</th>
				<th>Type</th>
				<th>Level</th>
				<th>Reference</th>
				<th>Status</th>
			</tr>
		</thead>
@php $cnt = 0 @endphp		
		<tbody id="baseTable">
		@foreach ($data as $key => $alert)
		@foreach ($alert->jobs as $key => $jobalert)
@php $cnt++ @endphp		
			<tr>
				<td>{{ \Carbon\Carbon::parse($alert->created_at)->format('Y-m-d H:i') }}</td>
				<td>{{ $alert->consultant->listname }}</td>
				<td class="job">{{ 'Job' }}</td>
				<td class="text-center alertcol{{ $jobalert->pivot->alert_level }}">{{ $jobalert->pivot->alert_level }}</td>
				<td>{{ $jobalert->jobref }}</td>
				<td> @include('partials.show_status', ['status'=>$jobalert->pivot->status]) </td>
			</tr>
@endforeach			
		@foreach ($alert->candidates as $key => $candalert)
@php $cnt++ @endphp		
			<tr>
				<td>{{ \Carbon\Carbon::parse($alert->created_at)->format('Y-m-d H:i') }}</td>
				<td>{{ $alert->consultant->listname }}</td>
				<td class="candidate">{{ 'Candidate' }}</td>
				<td class="text-center alertcol{{ $candalert->pivot->alert_level }}">{{ $candalert->pivot->alert_level }}</td>
				<td>{{ $candalert->user->listname }}</td>
				<td> @include('partials.show_status', ['status'=>$candalert->pivot->status]) </td>
			</tr>
@endforeach			

		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@if ($data instanceof Illuminate\Pagination\LengthAwarePaginator)
			{{ $data->appends($query)->links() }}
	<span class="resultcount">Showing	{{ $data->firstItem() }} - {{ $data->lastItem() }} of {!! $data->total() !!} entries</span>
	@else
	<span class="resultcount">Showing 1 - {{ $cnt }} of {!! $cnt !!} entries</span>
	@endif
</div>	
@else
@if ($q)	
	@include('partials.emptytable')
@endif
@endif


