@section('title', $baseclass)

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header @if (isset($styleclass)) {{ $styleclass }} @endif"><h3>{{ __($baseclass) }}</h3></div>

				<div class="card-body">
                        @csrf
						
<div class="card-group">
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Description',
															'fieldvalue'=> old('description', $item->description ) ])
</div>
@if ($item->isFillable('colour_hex'))
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Highlight',
															'fieldhighlight'=> old('colour_hex',$item->colour_hex ) ])
</div>
@endif
</div>							


                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
                </div>
				
            </div>
        </div>
    </div>
</div>

@endsection
