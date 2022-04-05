@if (is_object($jsondata))
			<div class="table-responsive">
			<table class="table table-bordered table-striped datatable">
			<tbody id="criteriaTable">
			@foreach ($jsondata as $key => $criteria)
				<tr>
					<th class="jkey">{{ $key }}</th>
					<td class="jvalue">{{ $criteria }}</td>
				</tr>
			@endforeach
			</tbody>
			</table>
			</div>
@endif