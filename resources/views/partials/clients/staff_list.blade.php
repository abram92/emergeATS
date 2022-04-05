@if ($staff->count() > 0)
	@php
      $cnt = $staff->where('created_at', '>', date('Y-m-d', strtotime('-180 days')))->count();
	@endphp  
	<div class="table-responsive">
	<table class="table table-bordered datatable @if (!isset($sublist) || (!$sublist)) table-striped filterable @else compact @endif"
@if (!isset($sublist) || (!$sublist))
     data-order='[[ 1, "desc" ]]' data-column-defs='[{"sortable": false, "targets": [4]}]' @if($cnt) data-page-length='{{ $cnt }}'@endif
@endif 
	 >
		<thead class="{{ (!isset($sublist) || (!$sublist)) ? "table-dark" : "clientcontact" }}">
			<tr>
				<th>Contact Name</th>
				<th>Date Added</th>
				<th>Position</th>
				<th>Contact Info</th>
@if (!isset($sublist) || (!$sublist))			
				<th width="180px">Actions</th>
@endif
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($staff as $key => $person)
			<tr
			>
				<td>{{ $person->listname }}
@if ($person->comments)
				
<a href="#" data-id={{ $person->id }} onclick="return toggleComment(this, 'contact');" title="Toggle Comment" class="fa fa-comment"></a>
<div id="contact{{ $person->id }}comment" class="d-none">
<pre>{{ $person->comments->chunk }}</pre>

</div>
@endif	
					
				</td>
				<td>@include('partials.formatteddate',['dt'=>$person->created_at])</td>
				<td>{{ $person->position }}</td>
				<td>@include('partials.contact_view_grouped2', ['contacts'=> $person->contactfields])</td>
@if (!isset($sublist) || (!$sublist))							
				<td>
					<a href="{{ route('clientcontacts.show',$person->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fas fa-info" target="clientcontact{{ $person->id }}"></a>
					<a href="{{ route('clientcontacts.edit',$person->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit" target="clientcontact{{ $person->id }}"></a>
				{!! Form::open(['method' => 'DELETE','route' => ['clientcontacts.destroy', $person->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fas fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}

				</td>
@endif				
				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
@if (!isset($sublist) || (!$sublist))			
	
@endif
	
@endif

@includeWhen(!isset($toggledJS), 'scripts.toggledCommentJS')
