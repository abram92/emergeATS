@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top ">
		<span><h3>{{ __('Email CVs to Client') }}</h3></span>
@if (isset($jobad) && $jobad)
        <span class="badge1 shadow job ml-auto mr-1">  
			<span><h4>
				{{ __($jobad->jobref) }}
			</h4></span>
			<span><h5 class="scrollhide">
				({{ __($jobad->jobtitle_text) }})
			</h5></span>
		</span>
@else		
	</div>
@endif
@endsection


@section('content')

@if(isset($jobad) && $jobad)

    <div class="card-header job">
		   
		<div class="row">
			<div class="col-3">
				<h4>{{ __($jobad->jobref) }}</h4>
				<h5>{{ __($jobad->jobtitle_text) }}</h5>
			</div>
			<div class="col-3">
			</div> 
			<div class="col-6">
				@include('partials.jobads.jobcontacts_list', ['contacts'=>$jobad->clientcontacts])
			</div>
		</div>
		   
	</div>
							

@endif
			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

        </div>
    </div>
	
@stop
