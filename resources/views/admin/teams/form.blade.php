@section('title', 'Teams')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header @if (isset($styleclass)) {{ $styleclass }} @endif"><h3>@if (isset($team->id)){{__('Edit')}} @else {{__('Add')}} @endif{{ __('Team') }}
</h3></div>

				<div class="card-body">

@include('partials.flashmessages')

@if (isset($team->id))
					<form method="POST" action="{{route('admin.teams.update', $team->id)}}">
					<input name="_method" type="hidden" value="PATCH">
@else
					<form method="POST" action="{{route('admin.teams.store')}}">
@endif
                        @csrf
						<div class="col-xs-12 col-sm-12 col-md-12">

@include('partials.form_text', ['fieldname'=>'description', 
								'fieldlabel'=>'Description', 
								'fielddefault'=> old('description', isset($team) ? $team->description : ""),
								'is_required'=>true, 
								'is_autofocus'=>true])
								
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12">	
                        <div class="form-group row">
							<fieldset class="fs_highlight col-xs-12 col-sm-12 col-md-12">
							<legend>Highlight</legend>
								<input class="form-control-check" placeholder="" name="colour_no" type="checkbox" id="colour_no" {{ old('colour_hex', isset($team) ? $team->colour_hex : "" ) ? '' : 'checked' }}>
								<label for="colour_no" class="form-check-label mr-3">{{ __('No Highlight') }}</label>
								<input class="form-control-color btn-sm" title="Choose colour" placeholder="" name="colour_hex" default="#ffffff" type="{{ old('colour_hex', isset($team) ? $team->colour_hex : "" ) ? 'color' : 'hidden' }}" id="colour_hex" value="{{ old('colour_hex', isset($team) ? $team->colour_hex : "" ) }}">
							</fieldset>	
                        </div>								
   
						</div>
						
@include('partials.select2_dropdown_multiple', ['fieldname'=>'leaders[]', 
									'fieldlabel'=>'Team Leaders', 
									'fieldplaceholder'=>'Choose Team Leaders', 
									'options'=>$teamleaders,
									'selectedoptions'=>old('leaders', isset($leaders) ? $leaders : null)])
@include('partials.select2_dropdown_multiple', ['fieldname'=>'members[]', 
									'fieldlabel'=>'Team Members', 
									'fieldplaceholder'=>'Choose Team Members', 
									'options'=>$consultants,
									'selectedoptions'=>old('members', isset($members) ? $members : null)])
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

@push('scripts')
@include('scripts.src_select2')
@endpush
	
@section('js')
    <script>
		 
	$("document").ready(function() {
		
			@include('scripts.ready_select2')
		
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

