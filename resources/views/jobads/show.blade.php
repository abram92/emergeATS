@extends('layouts.tab')

@section('title', __($jobad->jobref))

@section('tabheader')
                <div class="card-header job sticky-top mb-1">
				<div class="row"> 
					<div class="col-xs-9 col-sm-9 col-md-9"><h3>{{ __($jobad->jobref) }}</h3>
					<h4>{{ old('jobtitle', $jobad->jobtitle_text ) }}</h4>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 text-right">
						<h5>
						@include('partials.show_status', ['status'=>$jobad->status, 'show_shadow'=>true])
						</h5>
						<h5 class="scrollhide">
						<span class="badge" >{{ optional($jobad->consultant)->fullname_username }}</span>
						</h5>
						<h5 class="scrollhide">
							<span class="badge" >{{ $jobad->activated_at }}</span>
						</h5>						
					</div>
				</div>
	@include('staticwork.emailssentnotice', ['viewobj'=>'jobad'])			
				</div>
@endsection
@section('content')
			
    <div class="card">

        <div class="card-body">
                    @csrf

	<div class="card-group">				
	<div class="card client-outline">
		<div class="card-header client">Client</div>
			<div class="card-body col-xs-12 col-sm-12 col-md-12">
				<div class="row">
                                {{ old('name', $jobad->client->name ) }}
				</div>
			</div>
					
					
			<div class="col-xs-12 col-sm-12 col-md-12 p-0">
				@include('partials.jobads.jobcontacts_list', ['contacts'=>$jobad->clientcontacts, 'setcontactcolour'=>true])
			</div>	
	</div>
	
	<div class="card p-0"> 
	
<div class="card-body pt-1 pb-1">
				@include('partials.staticdisplay.field', ['fieldprompt'=>'EE Status',
															'fieldstatus'=> $jobad->eestatus ])
				@include('partials.staticdisplay.field', ['fieldprompt'=>'Locations',
														  'promptclass'=>'locationheader', 
															'fieldvalue'=> optional($jobad->locations)->implode('description', ', ') ])

				@include('partials.staticdisplay.field', ['fieldprompt'=>'Gender',
															'fieldstatus'=> optional($jobad->gender) ])

						
</div>	
       
		@include('partials.salary_view_block_card', ['obj'=>$jobad])

	</div>				
	
</div>


					@include('partials.collapse_textarea', ['field'=>'cvsendinstructions', 'start_expanded'=>'true', 
															'field_title'=>'CV Send Instructions', 'field_body'=>optional($jobad->cvsendinstructions)->chunk])
					@include('partials.collapse_textarea', ['field'=>'summary', 'start_expanded'=>'false', 
															'field_title'=>'Summary', 'field_body'=>optional($jobad->summary)->chunk])
					@include('partials.collapse_textarea', ['field'=>'agencynotes', 'start_expanded'=>'true', 'field_min'=>true, 
															'field_title'=>'Agency Notes', 'field_body'=>optional($jobad->agencynotes)->chunk,
															'edit_lt'=>true, 
															'edit_lt_route'=>route('notes.edit',['model'=>'jobs','modelid'=>$jobad->id]), 
															'edit_lt_target'=>'jobad'.$jobad->id])
					@include('partials.collapse_textarea', ['field'=>'projectplan', 'start_expanded'=>'false', 'field_min'=>true, 
															'field_title'=>'Project Plan', 'field_body'=>optional($jobad->projectplan)->chunk])
					@include('partials.collapse_textarea', ['field'=>'skills', 'start_expanded'=>'true', 
															'field_title'=>'Skills', 'field_body'=>optional($jobad->skills)->chunk])
					@include('partials.collapse_textarea', ['field'=>'fulldescription', 'start_expanded'=>'true', 
															'field_title'=>'Description', 'field_body'=>optional($jobad->fulldescription)->chunk])

@section('formbuttons')
					<a href="{{ route('jobs.edit',$jobad->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-primary" target="job{{ $jobad->id }}"><i class="fa fa-edit"></i></a>
					
					<a class="btn btn-primary" href="{{ route('jobs.index') }}"> Back</a>

					<a class="btn btn-danger" title="Match Candidate" target="matchcand{{ $jobad->id }}" href="{{ route('candidates.index').'?jobid='.$jobad->id }}">
							@include('partials.icons.search_candidate')
					</a>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')							
			
			@include('partials.documents_card', ['model'=>'jobs', 'modelid'=>$jobad->id])
			

			<div class="card">
				<div class="card-header coloration candidate1 client2  text-light">
					<h4>Candidates CV sent to client</h4>
				</div>
				<div class="card-body">
				@include('partials.jobads.jobapplication_summary_list', ['jobapplications'=>$jobad->jobapplicationsLinked])
				</div>
			</div>

			<div class="card">
				<div class="card-header candidate  text-light">
					<h4>Prospect Candidates</h4>
				</div>
				<div class="card-body">
				@include('partials.jobads.prospect_summary_list', ['jobapplications'=>$jobad->jobapplicationsProspect])
				</div>
			</div>

@include('partials.audit_trail', ['statusArr'=>$statuses])


		</div>		
	</div>
			
@endsection 

@push('scripts')
@include('scripts.src_datatables')
@endpush

@section('js')
@parent
 					@include('partials.dropzone_def_js', ['modelurl'=>'jobs/'.$jobad->id])

  
<script>					
		$("document").ready(function() {

@include('scripts.ready_datatables')



		});

</script>			
@stop