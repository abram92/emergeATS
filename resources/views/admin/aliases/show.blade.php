@extends('layouts.tab')

@section('tabheader')
	<div class="card card-header sticky-top mb-1">
		<div class="row"> 
			<div class="col-xs-9 col-sm-9 col-md-9">
				<div>
					<h3><span title="Alias">&nbsp;&nbsp;</span>{{ __('Skill: '. $alias->description) }}</h3>
				</div>
			</div>
			<div class="col-xs-3 col-sm-3 col-md-3 text-right">
			</div>
		</div>
	</div>

@endsection

@section('content')
	
    <div class="card">
        <div class="card-body">
                    @csrf

<div class="row">

<div class="col-3">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Area of Specialisation',
															'fieldvalue'=> optional($alias->category)->description ])
</div>
<div class="col-3">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Skill',
															'fieldvalue'=> $alias->description ])
</div>
<div class="col-3">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Keywords',
															'fieldvalue'=> optional($alias->keywords)->implode('keyword', ',') ])
</div>
<div class="col-3">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Minimum matches',
															'fieldvalue'=> $alias->minimum_parser_matches ])
</div>

</div>



@section('formbuttons')
	<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')								
					
		</div>
	</div>			

@endsection 

@section('js')


    <script>
	
		$("document").ready(function() {
			
    $(document).on("click", ".btn-close" , function(){

        window.close();

    });		
		});
		
		
    </script>
@endsection
