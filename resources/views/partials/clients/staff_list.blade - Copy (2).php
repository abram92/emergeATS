@if ($staff->count() > 0)
	<table class="table table-bordered table-striped datatable @if (!isset($sublist) || (!$sublist))filterable @endif">
		<thead class="table-dark">
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
			<tr rowspan="2">
				<td>{{ $person->listname }}
@if ($person->comments)
				
<a href="#" data-id={{ $person->id }} onclick="return toggleComment(this);" title="Toggle Comment" class="fa fa-comment"></a>
@endif	

				</td>
				<td>@include('partials.formatteddate',['dt'=>$person->created_at])</td>
				<td>{{ $person->position }}</td>
				<td>@include('partials.contact_view_grouped2', ['contacts'=> $person->contactfields])</td>
@if (!isset($sublist) || (!$sublist))							
				<td>
					<a href="{{ route('clientcontacts.show',$person->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm btn-info fas fa-info" target="clientcontact{{ $person->id }}"></a>
					<a href="{{ route('clientcontacts.edit',$person->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-primary fa fa-edit" target="clientcontact{{ $person->id }}"></a>
				{!! Form::open(['method' => 'DELETE','route' => ['clientcontacts.destroy', $person->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm btn-danger fas fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}

				</td>
@endif				
@if ($person->comments)
	</tr><tr class="d-none"><td colspan="5">
</td>	
</tr><tr id="contact{{ $person->id }}comment" class="d-none"><td colspan="5">
<pre>{{ $person->comments->chunk }}</pre>

</td>
	
@endif	
				
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
@if (!isset($sublist) || (!$sublist))			
	
	@if ($staff instanceof Illuminate\Pagination\LengthAwarePaginator)
			{{ $staff->links() }}
	Showing	{{ $staff->firstItem() }} - {{ $staff->lastItem() }} of {!! $staff->total() !!} entries
	@else
	Showing 1 - {{ $staff->count() }} of {!! $staff->count() !!}	entries
	@endif
@endif
	
@endif

@section('js')

<script>
	function toggleComment(identifier){     
           id = '#contact'+$(identifier).data('id')+'comment';	
			$(id).toggleClass("d-none d-table-row");
			return false;	   
        }

</script>

@endsection
