			<div class="card">
				<div class="card-header email">
					<h4>Emails Sent</h4>
				</div>
				<div class="card-body">
				
@if ($emails->count() > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 3, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [4]}]' data-page-length='15'>
		<thead class="table-dark">
			<tr>
				<th>User</th>
				<th>Recipients</th>
				<th>Subject</th>
				<th>Date</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="emailTable">
		@foreach ($emails as $key => $email_record)
			<tr>
				<td @if($email_record->sender->trashed()) class="text-muted" @endif>{{ $email_record->sender->listname }}</td>
				<td>{{ $email_record->address_to }}</td>
				<td>{{ $email_record->subject }}</td>
				<td>{{ $email_record->date }}</td>
				<td>
					<a href="{{ route('email.show',$email_record->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fas fa-info"  target="email{{ $email_record->id }}"></a>
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@endif
				
				</div>
			</div>
