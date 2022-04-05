

@include('partials.searchfilters.query_criteria_block')

@if (count($data) > 0)
<div class="card">	
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Date Activated</th>
				<th>Status</th>
				<th>Action By</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $candidate)
			<tr>
				<td>{{ $candidate->user->fullname }}</td>
				<td>@include('partials.list_date_format',['dt'=>$candidate->activated_at])</td>
				<td>@include('partials.show_status', ['status'=>$candidate->status])</td>
				<td>{{ optional($candidate->audits[0]->user)->fullname_username }}</td>
				<td width="180px">@include('partials.list_candidate_actions', ['dropdownAlign'=>'dropdown-menu-right', 'isReport'=>true])</td>	
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


