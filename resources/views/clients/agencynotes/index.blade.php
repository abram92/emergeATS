@extends('layouts.admin')


@section('content')

<div class="container col-md-12">
	<div class="card">
		<div class="card-header">
			<h3>{{ __('Client Notes') }}</h3>
			<div class="row container-fluid">
				<div class="col-md-8">
					<input class="form-control" id="baseSearch" type="text" placeholder="Search...">
				</div>
				<div class="col-md-4 float-right">
				{{--				<a href="{{ route('clientnotes.create') }}" class="btn btn-xs btn-info fa fa-plus float-right">Add New</a> --}}
				</div>
			</div>
		</div>
		<div class="card-body">
			
@include('partials.flashmessages')

@if (count($data) > 0)
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Date</th>
				<th>Editor</th>
				<th>Note</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $note)
			<tr>
				<td>@if($note->updated_at){{ $note->updated_at }}@else {{ $note->created_at }}@endif</td>
				<td @if($note->editor->trashed()) class="text-muted" @endif>{{ $note->editor->listname }}</td>
				<td><pre id="{{ $note->id.$field }}pre" class="card-text @if (isset($field_min) && $field_min) p1 @endif">{{  $note->chunk  }}</pre>
</td>
				<td>
					<a href="{{ route('clientnotes.show',$note->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm btn-info fas fa-info" target="clientnote{{ $note->id }}"></a>
					<a href="{{ route('clientnotes.edit',$note->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm btn-primary fa fa-edit" target="clientnote{{ $note->id }}"></a>
		@if (Auth::id() != $note->editor_id)
				{!! Form::open(['method' => 'DELETE','route' => ['clientnotes.destroy', $note->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm btn-danger fas fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}
		@endif	

				</td>
			</tr>		
		
		
			<tr>
				<td>{{ $clientcontact->listname }}</td>
				<td>{{ $clientcontact->email }}</td>
				<td>
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
	@include('partials.show_pagination')	
	
@else
	@include('partials.emptytable')
@endif


{!! $data->render() !!}
		</div>
	</div>
</div>

<div id="popupModal2" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body">
      <iframe src="" style="zoom:0.60" frameborder="0" height="250" width="99.6%"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal">OK</button>
	</div>
</div>
@endsection 

@section('js')

@push('scripts')
@include('scripts.src_select2')
@endpush

    <script>
		$("document").ready(function() {
			
			$("#baseSearch").on("keyup", function () {
				var value = $(this).val().toLowerCase();
				$("#baseTable tr").filter(function() {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
			
			
			$('.bootpopup').click(function(){
				alert('1234');
    var frametarget = $(this).attr('href');
  var targetmodal = $(this).attr('target');
  if (targetmodal == undefined) {
    targetmodal = '#popupModal';
  } else { 
    targetmodal = '#'+targetmodal;
  }
  if ($(this).attr('title') != undefined) {
    $(targetmodal+ ' .modal-header h3').html($(this).attr('title'));
    $(targetmodal+' .modal-header').show();
    $(targetmodal+' .modal-footer').show();
  } else {
     $(targetmodal+' .modal-header h3').html('');
    $(targetmodal+' .modal-header').hide();
  }  
  alert(frametarget);
    $(targetmodal).on('show', function () {
		alert("setting target");
        $('iframe').attr("src", frametarget );
       alert("set");		
	});
	alert("show modal");
        $('iframe').attr("src", frametarget );
    $(targetmodal).modal({show:true});
  return false;
    
});
		});
		
		
    </script>
	
@endsection