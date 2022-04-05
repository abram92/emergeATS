

@include('partials.searchfilters.query_criteria_block', ['canSave'=>true])

@if (count($data) > 0)
<div class="card">
	<div class="table-responsive">
	<table class="table card-body table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Consultant</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $client)
		@if ($client->relationLoaded('staff')) <tr class="d-none"><td colspan="4"></td></tr> @endif
			<tr @if ($client->relationLoaded('staff')) class="client" @endif>
				<td>{{ $client->name }}</td>
				<td>{{ optional($client->consultant)->fullname_username }}</td>
				<td>@include('partials.show_status', ['status'=>$client->status])</td>
				<td @if ($client->relationLoaded('staff')) class="alert-primary" @endif>
@include('partials.list_client_actions')	
				</td>
			</tr>
			
@if ($client->relationLoaded('staff'))
<tr class="d-none"><td colspan="4"></td></tr>	
<tr><td colspan="4" class="p-0">@include('partials.clients.staff_list', ['staff'=>$client->staff, 'sublist'=>true])</td></tr>	
@endif
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

	<script src="{{ asset('js/ajaxcalls.js') }}" rel='javascript'></script>	

