
@section('title', $baseclass)


@section('contentsearch')
		<form action="#" method="get" class="form-inline ml-2 mr-2 searchForm">
			<div class="input-group">
				<input class="form-control form-control-navbar" type="search" name="q" placeholder="Filter Description" aria-label="filter" @if (isset($query) && !empty($query)) value="{{ $query['q'] }}" @endif>
				<div class="input-group-append">
					<button class="btn btn-outline-secondary bg-light" title="Search" type="submit">
						<i class="fas fa-search"></i>
					</button>
				</div>
			</div>
		</form>
<div class="topbar-divider d-none d-sm-block"></div>
		
@stop
@section('content')
<div class="container col-md-12">
	<div class="card">
		<div class="card-header @if (isset($styleclass)) {{ $styleclass }} @endif ">
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __($baseclass) }}</h3>
				</div>
				<div class="col-md-4">
</div>
				<div class="col-md-4 float-right">
					<a href="{{ route('admin.'.$basepath.'.create') }}" data-toggle="tooltip" title="Add New" class="btn btn-xs text-info fa fa-plus-circle float-right"></a>
				</div>
			</div>
			<div class="row container-fluid">
				<div class="col-md-8">
				
@include('partials.searchfilters.query_criteria_block')

				 </div>
			</div>
		</div>
		<div class="card-body">
			
@include('partials.flashmessages')

@if (count($items) > 0)
@if ($sortable)
					<div class="alert alert-secondary" role="alert">
						Drag and drop row to sort display order in lists
					</div>
@endif
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Description	
</th>
			@if (isset($items[0]) && $items[0]->isFillable('colour_hex')) 
				<th>Highlight</th>
			@endif 
				<th width="180px">Actions</th>
			</tr>
		</thead>
	
		<tbody id="baseTable">
        @foreach ($items as $item)
			<tr id="{{ $item->id }}">
				<td field-key='description'>{{ $item->description }}</td>
			@if ($items[0]->isFillable('colour_hex'))   
				<td field-key='colour_hex'>@if ($item->colour_hex)<label class="badge" style="background-color:{{ $item->colour_hex }}">&nbsp;&nbsp;</label>@endif &nbsp;{{ $item->colour_hex }} </td>
			@endif  
				<td>
					<a href="{{ route('admin.'.$basepath.'.show',$item->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info"></a>
					<a href="{{ route('admin.'.$basepath.'.edit',[$item->id]) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit"></a>

				
				@if (!isset($item->deletable) || $item->deletable)	
						
                        {!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('Are you sure?');",
                            'route' => ['admin.'.$basepath.'.destroy', $item->id])) !!}
                        {!! Form::button('', ['class' => 'btn text-danger btn-sm fa fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
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
	@include('partials.show_pagination', ['data'=>$items])	
	
@else
	@include('partials.emptytable')
@endif


		</div>
	</div>
</div>
@endsection 

@push('scripts')
@include('scripts.src_select2')
@endpush

@section('js')
 		

    <script>

		$("document").ready(function() {

@if($sortable)
			$('tbody').sortable({
				        stop: function() {
							
                $.map($(this).find('tr'), function(el) {
                var id = el.id;
                var sort_seq = $(el).index();

                $.ajax({
                    url: '{{ $basepath }}/updateOrder',
                    type: 'GET',
                    data: {
                        id: id,
                        sort_seq: sort_seq
                    },
                });
            });
        }
			});
@endif			
			
			$("#baseSearch").on("keyup", function () {
				var value = $(this).val().toLowerCase();
				$("#baseTable tr").filter(function() {
					$(this).toggle($(this).children().first().text().toLowerCase().indexOf(value) > -1)
				});
			});
		});
		
		
    </script>
	

@stop