@extends('layouts.tab')

@php
$apptype_header = ($jobapp->applicationable_type == 'App\JobAd') ? 'job2' : 'client2';

@endphp

@section('title', __( 'Application : '.$jobapp->candidate->user->listname.' ('.(($jobapp->applicationable_type == 'App\JobAd') ? $jobapp->jobad->jobref : $jobapp->client->name).')'))

@section('tabheader')

    <div class="card-header coloration candidate1 {{ __($apptype_header) }} sticky-top mb-1">
		<div class="row p-0">
		<div class="col-6">
			<div class="row">
				<span class="pr-2"><h5 class="scrollhide">{{ __('Job Application') }}</h5></span>
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
            <span class="badge1 shadow job">  
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
			<span class="ml-auto mr-1">
				<h5>
					@include('partials.show_status', ['status'=>$jobapp->status, 'show_shadow'=>true])
				</h5>
			</span>
		</div>		
		</div>
		</div>
	</div>
	
@endsection

@section('content')
	
    <div class="card">
        <div class="card-body">
                    @csrf
			
					@include('partials.collapse_textarea', ['field'=>'comments', 'start_expanded'=>'true', 
															'field_title'=>'Comments', 'field_body'=>$jobapp->comments])
				

@section('formbuttons')
			<a class="btn btn-primary" href="{{ route('jobs.index') }}"> Back</a>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')		

	@include('partials.email_trail', ['emails'=> $jobapp->emails])

	@include('partials.audit_trail', ['statusArr'=>$statuses])

		</div>
	</div>


@endsection 

	
					