@extends('layouts.admin')

@section('title', 'Team:'.$team->description)

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header @if (isset($styleclass)) {{ $styleclass }} @endif"><h3>{{ __($team->description) }}</h3></div>

				<div class="card-body">
                        @csrf
						
<div class="card-group">
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Description',
															'fieldvalue'=> old('description', $team->description ) ])
</div>
<div class="card card-body border-0 margin-0">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Highlight',
															'fieldhighlight'=> old('colour_hex',$team->colour_hex ) ])
</div>
</div>							
<div class="card team">
<div class="card-header pt-1 pb-1">Users</div>		
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Leaders',
															'fieldvalue'=> optional($team->leaders)->implode('listname', ', ') ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Members',
															'fieldvalue'=> optional($team->members)->implode('listname', ', ') ])
						
</div>
		</div>		

                        <div class="form-group row mb-0">
                            <div class="col-md-12 offset-md-6">
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
                </div>
				
            </div>
        </div>
    </div>
</div>

@endsection
