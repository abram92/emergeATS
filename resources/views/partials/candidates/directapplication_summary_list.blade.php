@if ($jobapplications->count() > 0)
	@php
      $cnt = $jobapplications->where('emailevents_count', '>', 0)->count();
	  $canBulkSend =  true;
	@endphp  
			@if ($canBulkSend)
				<form method="post" target="_blank" action="{{ route('emailcvtoclients.create',$candidate->id) }}" name="emailcvtoclientjobs">
	<div class="table-responsive">
			<table class="table table-bordered table-striped datatable  filterable" data-order='[[ 4, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [0,5]}]'  @if($cnt) data-page-length='{{ $cnt }}'@endif>
			@else
	<div class="table-responsive">
			<table class="table table-bordered table-striped datatable  filterable" data-order='[[ 3, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [4]}]'  @if($cnt) data-page-length='{{ $cnt }}'@endif>				
			@endif
		<thead class="table-dark">
			<tr>
			@if ($canBulkSend)
				<th></th>
			@endif
				<th>Company</th>
				<th>Contact</th>
				<th>CV Sent</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobappl)
			<tr >
			@php $sentemail = $jobappl->emailevents->first(); @endphp
			@if ($canBulkSend)			
				<td><input type="checkbox" name="applIds[]" class="chk"  value="{{ $jobappl->id }}_c_{{ $jobappl->client->id }}">
				</td>
			@endif	
				<td>{{ $jobappl->client->name }}</td>
				<td>@if($sentemail){!! nl2br(e(implode(PHP_EOL,$sentemail->clientcontacts->pluck('listname')->toArray()))) !!} @endif</td>			
				<td>{{ optional($sentemail)->time_start }}</td>
<td>@include('partials.show_status', ['status'=>$jobappl->status])</td>
				<td>
				@include('partials.list_jobapplication_actions')
				</td>
				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
			@if ($canBulkSend)	
	<button class="btn btn-primary" type="submit" id="emailjobspec">Email CV to Clients</button>
						@csrf
	</form>
			@endif	
@endif

@includeWhen(!isset($toggledJS), 'scripts.toggledCommentJS')
