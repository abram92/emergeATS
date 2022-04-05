@extends('layouts.tab')

@section('title', __('Public Holiday: '. $publicholiday->description))

@section('tabheader')
	<div class="card card-header sticky-top mb-1">
		<div class="row"> 
			<div class="col-xs-9 col-sm-9 col-md-9">
				<div>
					<h3><span title="Public Holiday">&nbsp;&nbsp;</span>{{ __('Public Holiday: '. $publicholiday->description) }}</h3>
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
<div class="col-6">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Date',
															'fielddate'=> ($publicholiday->recurring) ?
															\Carbon\Carbon::parse($publicholiday->holiday_date)->format('j F ') . " ANNUALLY " :
															\Carbon\Carbon::parse($publicholiday->holiday_date)->format('j F Y')
															])
</div>

<div class="col-6">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Description',
															'fieldvalue'=> $publicholiday->description ])
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

@endsection
