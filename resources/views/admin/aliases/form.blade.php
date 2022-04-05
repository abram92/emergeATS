@section('content')

				<div class="container card-header"><h3>@if (isset($alias->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Skill') }}</h3></div>

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

@if (isset($alias->id))				
					{!! Form::model($alias, ['method' => 'PATCH','route' => ['admin.aliases.update', $alias->id]]) !!}
@else
					{!! Form::open(array('route' => 'admin.aliases.store','method'=>'POST')) !!}
@endif

						<div class="card card-body border-0">

	@include('partials.select2_dropdown_single', ['fieldname'=>'alias_category_id', 
									'fieldlabel'=>'Area of Specialisation', 
									'fieldplaceholder'=>'Choose Specialisation', 
									'required'=>true,
									'options'=>$categories,
									'selectedid'=>isset($alias) ? $alias->alias_category_id : null])						
	
		
@include('partials.form_text', ['fieldname'=>'description', 
								'fieldlabel'=>'Skill', 
								'fielddefault'=> old('description', isset($alias) ? $alias->description : ""),
								'is_required'=>true, 
								'is_autofocus'=>true])			
						
						
@include('partials.form_textarea', ['fieldname'=>'keywords', 
									'fieldlabel'=>'Keyword phrases (1 per line)', 
									'fielddefault'=> old('keywords', isset($alias) ? optional($alias->keywords)->implode('keyword', "\r\n" ) : "") , 
									'is_required'=>true])						
					
@include('partials.form_number', ['fieldname'=>'minimum_parser_matches1', 
								'fieldlabel'=>'Minimum Matches', 
								'fielddefault'=> old('minimum_parser_matches', isset($alias) ? $alias->minimum_parser_matches : ""),
								'fieldmin'=>0])								


</div>	

				
	
    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
</div>
{!! Form::close() !!}
				</div>
			</div>
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
		});
		
		
    </script>
@endsection