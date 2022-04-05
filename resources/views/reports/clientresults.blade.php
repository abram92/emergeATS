

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>No</th>
				<th>Company Name</th>

				<th>Address</th>
				<th>Contact Info</th>
				<th>Consultant</th>
				<th>Status</th>


			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $client)
			<tr>
				<td>{{ $client->id }}</td>
				<td>{{ $client->name }}</td>		
				<td class="p-0"><div class="container no-gutters">@include('partials.addresses.address_view_grouped2', ['addresses'=>$client->addresses])</div></td>
				<td class="p-0"><div class="container no-gutters">@include('partials.contact_view_grouped2', ['contacts'=>$client->contactfields])</div></td>
				<td>{{ optional($client->consultant)->fullname_username }}</td>				
				<td>@include('partials.show_status', ['status'=>$client->status])</td>
@if ($client->relationLoaded('staff'))
<tr class="d-none"><td colspan="6"></td></tr>	
<tr><td colspan="6" class="p-0">@include('partials.clients.staff_list', ['staff'=>$client->staff, 'sublist'=>true])</td></tr>	
@endif				
@if (!$client->relationLoaded('staff'))
<tr class="d-none"><td colspan="6"></td></tr>	
				<tr><td colspan="6">		@include('partials.collapse_textarea', ['field'=>'techenvironment'.$client->id, 'field_title'=>'Tech Environment', 'start_expanded'=>'false', 'field_body'=>isset($client->techenvironment) ? $client->techenvironment->chunk : ''])</td></tr>
@endif	
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


