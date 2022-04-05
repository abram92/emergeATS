@if ($contacts->count() > 0)
	<table class="table table-bordered table-striped datatable">
		<thead @if ((isset($setcontactcolour) && $setcontactcolour)) class="clientcontact" @endif>
			<tr>
				<th>Contact Name</th>
				<th>Contact Info</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($contacts as $key => $person)
			<tr rowspan="2">
				<td>{{ $person->listname }}
				</td>
				<td>@include('partials.contact_view_grouped2', ['contacts'=> $person->contactfields])</td>				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
@endif
