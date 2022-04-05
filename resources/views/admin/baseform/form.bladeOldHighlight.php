@section('title', $baseclass)

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header @if (isset($styleclass)) {{ $styleclass }} @endif"><h3>@if ($item->id){{__('Edit')}} @else {{__('Add')}} @endif{{ __($baseclass) }}</h3></div>

				<div class="card-body">

@include('partials.flashmessages')

@if ($item->id)
					<form method="POST" action="{{route('admin.'.$basepath.'.update', $item->id)}}">
					<input name="_method" type="hidden" value="PATCH">
@else
					<form method="POST" action="{{route('admin.'.$basepath.'.store')}}">
@endif
                        @csrf
						<div class="col-xs-12 col-sm-12 col-md-12">

@include('partials.form_text', ['fieldname'=>'description', 
								'fieldlabel'=>'Description', 
								'fielddefault'=> old('description', isset($item) ? $item->description : ""),
								'is_required'=>true, 
								'is_autofocus'=>true])
								
						</div>
@if ($item->isFillable('colour_hex'))
						<div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
                            <label for="colour_hex" class="col-md-4 col-form-label text-md-right">{{ __('Highlight') }}</label>

                            <div class="col-md-6">		
								<input class="form-control-color col-sm-2" title="Choose colour" placeholder="" name="colour_hex" default="#ffffff" type="{{ old('colour_hex', $item->colour_hex ) ? 'color' : 'hidden' }}" id="colour_hex" value="{{ old('colour_hex', $item->colour_hex ) }}">
								<input class="form-control-check col-sm-2" placeholder="" name="colour_no" type="checkbox" id="colour_no" {{ old('colour_hex', $item->colour_hex ) ? '' : 'checked' }}>
								<label for="colour_no" class="form-check-label">{{ __('No Highlight') }}</label>
                            </div>
                        </div>
						</div>
@endif
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
				
            </div>
        </div>
    </div>
</div>

@endsection

@if ($item->isFillable('colour_hex'))
	
@section('js')
    <script>
		 
	$("document").ready(function() {
		$("#colour_no").change(function() {
			var colourInput = $("#colour_hex");
			 
            if(this.checked) {
                colourInput.attr('type', 'hidden');
		        colourInput.attr('val', '');
			} else {
				colourInput.attr('val', '#ffffff');
				colourInput.attr('type', 'color');
			}
		});
	});
    </script>
@endsection

@endif