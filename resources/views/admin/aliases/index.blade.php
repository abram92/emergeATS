@extends('layouts.admin')

@section('contentsearch')
					<form method="post" class="form-horizontal searchForm setting" role="form" action="{{ url('aliases/search') }}">
			@csrf

@include('partials.searchfilters.navbar_search_filter', ['filterName'=> 'q[keyword]',
														'filterVar'=> ((isset($q['keyword'])) ? $q['keyword'] : null),
														'filterPlaceholder'=> 'Filter Keywords',
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
@include('partials.select2_filter_dropdown_multiple', ['fieldname'=>'q[categories][]', 'fieldlabel'=>'Areas of Specialisation', 
									'fieldplaceholder'=>'Choose Specialisation', 
									'options'=>$categories,
									'selectedoptions'=>old('q[categories]', isset($q['categories']) ? $q['categories'] : null)])
					</div>
					<div class="col-md-6">				
@include('partials.filter_text_input', ['fieldname'=>'q[alias]', 'fieldlabel'=>'Skill', 
									'fieldplaceholder'=>'Filter Skills', 
									'fieldvalue'=>(isset($q['alias'])) ? $q['alias']:''])
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
		<div class="card-header">
			<div class="row">
				<div class="col-md-8">
					<h3>{{ __('Skills & Keywords') }}</h3>
				</div>
				<div class="col-md-4 float-right">
					<a href="{{ route('admin.aliases.create') }}" data-toggle="tooltip" title="Add New" class="btn btn-xs text-info fa fa-plus-circle float-right"></a>
				</div>
			</div>
			<div class="row container-fluid">
				<div class="col-md-8">

@include('partials.searchfilters.query_criteria_block')

				 </div>
				 
				 
				 			<div class="row container-fluid border">

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
				<th>Areas of Specialisation</th>
				<th>Skill</th>
				<th>Keywords</th>
				<th>Minimum Matches</th>
				<th width="180px">Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($data as $key => $alias)
			<tr>
				<td>{{ optional($alias->category)->description }}</td>
				<td>{{ $alias->description }}</td>
				<td>{{ optional($alias->keywords)->implode('keyword', ', ') }}
</td>
				<td class="text-center">{{ $alias->minimum_parser_matches }}</td>
				<td>
					<a href="{{ route('admin.aliases.show',$alias->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info" target="alias{{ $alias->id }}"></a>
					<a href="{{ route('admin.aliases.edit',$alias->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit" target="alias{{ $alias->id }}"></a>
		@if (!isset($item->deletable) || $item->deletable)	
				{!! Form::open(['method' => 'DELETE','route' => ['admin.aliases.destroy', $alias->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fa fa-trash', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
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
	@include('partials.show_pagination')	

@else
	@include('partials.emptytable')
@endif


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


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


@include('scripts.src_select2')
    <script>
	
		$("document").ready(function() {
@include('scripts.ready_select2')		
		$("#filterHeading").click(function() {


			$('.select2-container').prop("disabled",false);						
					
		});			
								
		});
		
		
    </script>
@endsection	
