<div class="card">
@if ($field_list->isEmpty())
	<div class="card-header text-muted mb-0 bg-light pt-0 pb-0 ">
		<label class="text-md-right" >{{ $field_title }}</label>
	</div>
@else
        <div class="card-header card-title text-light bg-dark pt-1 pb-1" id="{{ $field }}heading" data-toggle="collapse" data-target="#{{ $field }}body" aria-expanded="{{ $start_expanded }}" aria-controls="{{ $field }}body">
             <h5 class="mb-0">
                 <label class="text-md-right" >{{ $field_title }}</label>
             </h5>
        </div>
        <div id="{{ $field }}body" class="collapse @if($start_expanded == 'true') show @endif p-1" aria-labelledby="{{ $field }}heading" @if(isset($field_parent)) data-parent="#{{ $field_parent }}"@endif >
		@php $sorted = $field_list->sortBy('created_at'); @endphp
		
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable filterable" data-order='[[ 0, "desc" ]]'  data-column-defs='[{"sortable": false, "targets": [2,3]}]'>
		<thead class="table-dark">
			<tr>
				<th>Date</th>
				<th>Editor</th>
				<th>Note</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach($sorted as $field_item)
			<tr>
				<td>@if($field_item->updated_at){{ $field_item->updated_at }}@else {{ $field_item->created_at }}@endif</td>
				<td  @if($field_item->editor->trashed()) class="text-muted" @endif>{{ $field_item->editor->listname }}</td>
				<td><pre id="{{ $field_item->id.$field }}pre" class="card-text @if (isset($field_min) && $field_min) p1 @endif">{{  $field_item->chunk  }}</pre>
</td>
				<td>
							<a href="{{ url('/clientnotes/'.$field_item->id.'') }}" data-toggle="tooltip" title="Info" class="btn btn-sm text-info fa fa-info" target="_blank"></a> 
							<a href="{{ url('/clientnotes/'.$field_item->id.'/edit') }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit" target="_blank"></a> 
		@if (Auth::id() == $field_item->editor_id)
				{!! Form::open(['method' => 'DELETE','route' => ['clientnotes.destroy', $field_item->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fas fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}
		@endif	

				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
        </div>
@endif		
</div>