@extends('layouts.admin')

@section('title', 'Teams')


@section('contentsearch')
       <form class="form-horizontal searchForm team" method="post" role="form" action="{{ url('teams/search') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[description]',
														'filterVar'=> ((isset($q['description'])) ? $q['description'] : null),
														'filterPlaceholder'=> 'Filter Description',
														'advancedFilter'=>true, 
														'filterPrefix'=>'filter', 
														'isExpanded'=>false,
														'canSearch'=> Auth::user()->hasPermissionTo('data-search'), 
														'canExport'=> false])								


		<div id="filterBody" class="searchFilters searchForm collapse @if($data->isEmpty()) show @endif" aria-labelledby="filterHeading">
							<div class="container-fluid p-0" style="max-height:80vh; overflow-y:auto;">

			<div class="card card-body w-100">
				<div class="row">
					<div class="col-md-6">
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[leaders][]', 'fieldlabel'=>'Team Leaders', 
									'fieldplaceholder'=>'Select Team Leaders', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[leaders]', isset($q['leaders']) ? $q['leaders'] : null)])
					</div>
					<div class="col-md-6">				
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[members][]', 'fieldlabel'=>'Team Members', 
									'fieldplaceholder'=>'Select Team Members', 
									'options'=>$allconsultants,
									'selectedoptions'=>old('q[members]', isset($q['members']) ? $q['members'] : null)])
					</div>
				</div>				
				</div>
						</div>

@include('partials.form_reset')	

		</div>
		</form>
<div class="topbar-divider d-none d-sm-block"></div>
		
@stop
@section('content')
<div class="container col-md-12">
	<div class="card">
		<div class="card-header team ">
			<div class="row">
				<div class="col-md-4">
					<h3>{{ __('Teams') }}</h3>
				</div>
				<div class="col-md-4">
</div>
				<div class="col-md-4 float-right">
					<a href="{{ route('admin.teams.create') }}" data-toggle="tooltip" title="Add New" class="btn btn-xs text-info fa fa-plus-circle float-right"></a>
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

@if (count($data) > 0)

	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Description	
</th>
				<th>Leaders</th>
				<th>Members</th>
			@if (isset($data[0]) && $data[0]->isFillable('colour_hex')) 
				<th>Highlight</th>
			@endif 
				<th width="180px">Actions</th>
			</tr>
		</thead>
	
		<tbody id="baseTable">
        @foreach ($data as $item)
			<tr id="{{ $item->id }}">
				<td field-key='description'>{{ $item->description }}</td>
				<td field-key='leaders'>{!! nl2br(e(implode(PHP_EOL,optional($item->leaders)->pluck('listname')->toArray()))) !!}</td>
				<td field-key='members'>{!! nl2br(e(implode(PHP_EOL,optional($item->members)->pluck('listname')->toArray()))) !!}</td>
			@if ($data[0]->isFillable('colour_hex'))   
				<td field-key='colour_hex'>@if ($item->colour_hex)<label class="badge" style="background-color:{{ $item->colour_hex }}">&nbsp;&nbsp;</label>@endif &nbsp;{{ $item->colour_hex }} </td>
			@endif  
				<td>
					<a href="{{ route('admin.teams.show',$item->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info"></a>
					<a href="{{ route('admin.teams.edit',[$item->id]) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit"></a>

				
				@if (!isset($item->deletable) || $item->deletable)	
						
                        {!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('Are you sure?');",
                            'route' => ['admin.teams.destroy', $item->id])) !!}
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
	@include('partials.show_pagination', ['data'=>$data])	
	
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
			
@include('scripts.ready_select2')
			
	@include('scripts.ready_select2_form_reset');
			
		});
		
		
    </script>
	

@stop