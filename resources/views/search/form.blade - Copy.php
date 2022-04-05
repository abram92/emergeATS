@section('tabheader')
	<div class="card-header savedsearch sticky-top mb-1">
		<h3>
			{{ __('Save Search') }}
		</h3>
	</div>
@endsection

@section('content')


	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

				{!! Form::model($search, ['method' => 'PATCH','route' => ['savedsearch.update', $search->id]]) !!}

			<div class="form-group row ">
							{!! Form::label('searchtype', 'Search Type', array('class' => 'col-md-3 col-form-label text-md-right')) !!}
                <div class="col-md-8 col-form-label {{ $search->search_type }}">
						   {{ ucfirst($search->search_type) }}
				</div>
			</div>
						
            <div class="form-group row">
							{!! Form::label('description', 'Description', array('class' => 'col-md-3 col-form-label text-md-right')) !!}

                <div class="col-md-8">
							{!! Form::text('description', old('description', isset($search) ? $search->description : ""), array('placeholder' => 'Description','class' => 'form-control', 'required', 'autofocus', 'autocomplete')) !!}

                            @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                            @enderror
                </div>
            </div>
						
            <div class="form-group row">
							{!! Form::label('filter', 'Search Criteria', array('class' => 'col-md-3 col-form-label text-md-right')) !!}
                <div class="col-md-8">
							@include('partials.json_table', ['jsondata'=>json_decode($search->filtercriteria)])
							
                </div>
            </div>

				
			@include('partials.footer.button_start')
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
			@include('partials.footer.button_end')	
{!! Form::close() !!}
    </div>
        </div>

@include('partials.footer.padding')

@endsection

