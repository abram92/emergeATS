@if ($jobapplications->count() > 0)
	@php
	  $tableid = "shortlist";
      $cnt = $jobapplications->where('created_at', '>', date('Y-m-d', strtotime('-30 days')))->count();
	  $canBulkSend =  true;
	@endphp  
			@if ($canBulkSend)
				<form method="post" target="_blank" action="{{ route('emailcvtoclients.create',$candidate->id) }}" name="emailcvtoclients">
	<div class="table-responsive">
			<table id="@php echo $tableid; @endphp" class="table table-bordered table-striped datatable filterable" data-order='[[ 7, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [0,9]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
@else
	<div class="table-responsive">
			<table id="@php echo $tableid; @endphp" class="table table-bordered table-striped datatable filterable" data-order='[[ 6, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [8]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif>
			@endif
		<thead class="table-dark">
			<tr>
			@if ($canBulkSend)
				<th></th>
			@endif
				<th>Reference Code</th>
				<th>Upload Date</th>
				<th>Job Title</th>
				<th>Job Status</th>
				<th>Contacts</th>
				<th>Company</th>
				<th>Date Linked</th>
				<th>Status</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobapplications as $key => $jobappl)
			<tr 
			@if ($jobappl->comments)
				data-child-value-0="{{$jobappl->comments}}"
			@endif
			@if ($jobappl->jobad->cvsendinstructions)
				data-child-value-1="{{$jobappl->jobad->cvsendinstructions->chunk}}"
			@endif
			>
			@if ($canBulkSend)			
				<td><input type="checkbox" name="applIds[]" class="chk"  value="{{ $jobappl->id }}_j_{{ $jobappl->jobad->id }}">
				</td>
			@endif	
				<td>{{ $jobappl->jobad->jobref }}
				</td>
				<td>{{ $jobappl->jobad->activated_at }}
				</td>
				<td>{{ $jobappl->jobad->jobtitle_text }}
				</td>
<td>@include('partials.show_status', ['status'=>$jobappl->jobad->status])</td>
<td>{{ optional($jobappl->jobad->clientcontacts)->implode('listname', ',') }}</td>
				<td>{{ $jobappl->jobad->client->name }}</td>
				
				<td>{{ $jobappl->created_at }}</td>
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
	<button class="btn btn-primary" type="submit" id="emailjobspec">Email CV to Client (Linked Jobs)</button>
						@csrf
	</form>
			@endif	
@endif

@includeWhen(!isset($toggledJS), 'scripts.toggledCommentJS')
@if ($jobapplications->count() > 0)
   @include('scripts.datatableChildHideShowShortlistSummary', ['tableid'=>$tableid])
@endif