
@if ($documents->count() > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 2, "desc" ]]'  data-column-defs='[{"sortable": false, "targets": [0,3,4]}]'>
		<thead class="table-dark">
			<tr>
				<th>Attach</th>
				<th>Filename</th>
				<th>Upload Date</th>
				<th>File Size</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($documents as $key => $document)
			<tr>
				<td>
					<input type="checkbox" name="attachIds[]" class="chk"  value="{{ $document['id'] }}">
				</td>
				<td>{{ $document['filename'] }}</td>
				<td>@if($document['updated_at']){{ $document['updated_at'] }}@else {{ $document['created_at'] }}@endif</td>
				<td>{{ $document['size'] }}</td>
				<td>
							<a href="{{ url('/file/'.$document['id'].'/download') }}" data-toggle="tooltip" title="Download" class="btn btn-sm btn-info bootpopup fa fa-file-download" target="popupModal2"></a> 
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@else
	@include('partials.emptytable')
@endif

