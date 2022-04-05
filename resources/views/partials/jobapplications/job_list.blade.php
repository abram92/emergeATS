
@if ($jobapplications->count() > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Name</th>
				<th>Email Address</th>
				<th>Candidate Job Title</th>
				<th>Date Linked</th>
				<th>Status</th>
				<th>Job Spec Sent</th>
				<th>Upload Date</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobapplication)
			<tr>
				<td>{{ $jobapplication['listfilename'] }}</td>
				<td>{{ $jobapplication['updated_at'] }}</td>
				<td>{{ $jobapplication['size'] }}</td>
				<td>
							<a href="{{ url('/file/'.$jobapplication['id'].'/download') }}" data-toggle="tooltip" title="Download" class="btn btn-sm btn-info bootpopup fa fa-file-download" target="popupModal2"></a> 
						<a href="{{ url('/file/'.$jobapplication['id'].'/delete') }}" data-toggle="tooltip" title="Delete" class="btn btn-sm btn-danger fa fa-trash"></a> 

				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@include('partials.show_pagination', ['data'=>$jobapplications])	

@else
	@include('partials.emptytable')
@endif

