@extends('layouts.tab')

@php
$apptype_header = ($jobapp->applicationable_type == 'App\JobAd') ? 'job2' : 'client2';
	
@endphp

@section('tabheader')

<div class="card-header coloration candidate1 {{ __($apptype_header) }} sticky-top mb-1">
	<div class="row p-0">
	<div class="col-6">
		<div class="row">
			<span class="pr-2"><h5 class="scrollhide">{!! nl2br("Edit Job Application") !!}</h5></span>
			<span class="badge1 shadow candidate ml-auto mr-1 "> 
				<h4>
					{{ __($jobapp->candidate->user->listname) }}
				</h4>
			</span>
		</div>		
    </div>
	<div class="col-6">
		<div class="row">
		@if ($jobapp->applicationable_type == 'App\JobAd')
            <span class="badge1 shadow job ">  
				<h4>
					{{ __($jobapp->jobad->jobref) }}
				</h4>
				<h5 class="scrollhide">
					{{ __($jobapp->jobad->jobtitle_text) }}
				</h5>
			</span>
		@else
            <span class="badge1 shadow client"> 
				<h4>
					{{ __($jobapp->client->name) }}
				</h4>
			</span>	
		@endif			
		</div>		
	</div>
	</div>
</div>

@endsection

@section('content')


			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

					{!! Form::model($jobapp, ['method' => 'PATCH','route' => ['jobapplications.update', $jobapp->id]]) !!}


    <div class="card card-body">
	
@include('partials.select2_dropdown_single', ['fieldname'=>'status_id', 
									'fieldlabel'=>'Status', 
									'fieldplaceholder'=>'Choose Status', 
									'options'=>$statuses,
									'selectedid'=>isset($jobapp) ? $jobapp->status_id : null])


    </div>

				
            </div>

    <div class="card card-body">
	@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Comments', 'ltfieldmodel'=> old('comments', $jobapp->comments) , 'ltrequired'=>''])
            </div>

@section('formbuttons')
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
@endsection			
@include('partials.footer.formbuttonsSection', ['tag_textarea'=>true])
@yield('contentbuttons')

{!! Form::close() !!}
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