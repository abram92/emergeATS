@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top sticky-top coloration job1 candidate2 }}">
	<h3>{{ $view == 'job' ? __('Email Job Spec ('.$jobads->jobref.') to Multiple Candidates') : __('Email Job Specs to Candidate ('.$candidates->user->listname.')') }}</h3>
	</div>
@endsection

@section('content')

	<div class="card">
		<div class="card-body">

	@include('partials.flashmessages')

@if ($candidates instanceof Illuminate\Database\Eloquent\Collection) 
					{!! Form::open(array('route' => ['emailjobspectocandidates.store', $jobads->id],'method'=>'POST')) !!}
@else
					{!! Form::open(array('route' => ['emailjobspecstocandidate.store', $candidates->id],'method'=>'POST')) !!}
@endif	

	@include('partials.form_text', ['fieldname'=>'subject', 'fieldlabel'=>'Subject', 'is_autocomplete' =>false])		

	@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'coverpage', 'ltfieldlabel'=>'Cover Page', 'ltfieldmodel'=>  null , 'ltrequired'=>''])

@if ($candidates instanceof Illuminate\Database\Eloquent\Collection) 
					<div class="card">
	@if ($candidates->count() > 1)				
						<div class="card-header">
							<h4 style="display:inline">Custom Messages </h4>
							<h6 style="display:inline"> (can be blank)</h6>
						</div>
	@endif						
						<div class="card-body">
	@foreach($candidates as $candidate)
							<div class="form-group">
							<div class="row">
							<h4>{{ __($candidate->user->listname) }}</h4>						
		@if($candidate->agencynotes)	
<div class="float-left">	
			<button type="button" class="btn btn-danger pt-0 pb-0 ml-3" data-container="body" data-placement="bottom" data-toggle="popover" title="" data-content="{{ $candidate->agencynotes->chunk }}">Agency Notes</button>
</div>
		@endif
		@if ($candidates->count() > 1)
<div class="pt-0 pb-0 ml-auto mr-3">
	<input type="checkbox" name="discardIds[]" class="chk discardchk" @if(in_array($candidate->id, old('discardIds', []))) checked @endif value="{{ $candidate->id }}">
		{!! Form::label('discard'.$candidate->id, 'Discard', array('class' => 'col-form-label')) !!}
</div>	
		@endif
								</div>
		@if ($candidates->count() > 1)								
								<div class=" detail{{ $candidate->id }}" id="collapsediv{{ $candidate->id }}">
	@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'cnd'.$candidate->id, 'ltfieldlabel'=>'Custom message for '.$candidate->user->listname, 'ltfieldmodel'=>  null , 'ltrequired'=>''])
								</div>
		@endif								
							</div>
						
						<hr class="candidate">
	@endforeach
						</div>
					</div>
@else			
@if($candidates->agencynotes)	
                    <div class="form-group row">
						<div class="col-md-3 text-md-right">
							{!! Form::label('cnd'.$candidates->id, $candidates->user->listname, array('class' => 'col-form-label')) !!}
						</div>
						<div class="col-md-8">
							<button type="button" class="btn btn-danger" data-container="body" data-placement="bottom" data-toggle="popover" title="" data-content="{{ $candidates->agencynotes->chunk }}">Agency Notes</button>
						</div>
					</div>
@endif
@endif

@if ($jobads instanceof Illuminate\Database\Eloquent\Collection) 
	@foreach ($jobads as $jobad)
					<div class="card-header job">
						<div class="row"> 
							<div class="col-xs-9 col-sm-9 col-md-9"><h3>{{ __('Job:') }}</h3></div>
							<div class="col-xs-3 col-sm-3 col-md-3 text-right">
						<h4>
						{{ __($jobad->jobref) }}
						</h4>
						<h4>
						{{ __($jobad->jobtitle_text) }}
						</h4>
							</div>
						</div>
					</div>
			@include('partials.jobapplications.send_documents_card', ['model'=>'jobad', 'modelid'=>$jobad->id, 'documents'=>$jobad->documents])
	@endforeach	
@else
					<div class="card-header job">
						<div class="row"> 
							<div class="col-xs-9 col-sm-9 col-md-9"><h3>{{ __('Job:') }}</h3></div>
							<div class="col-xs-3 col-sm-3 col-md-3 text-right">
						<h4>
						{{ __($jobads->jobref) }}
						</h4>
						<h4>
						{{ __($jobads->jobtitle_text) }}
						</h4>
							</div>
						</div>
					</div>
			@include('partials.jobapplications.send_documents_card', ['model'=>'jobad', 'modelid'=>$jobads->id, 'documents'=>$jobads->documents])
@endif	

@if(isset($applids))
	@foreach ($applids as $applid)
<input type='hidden' name='applIds[]' value='{{ $applid }}'>
	@endforeach
@endif
	
@section('formbuttons')
@if(!isset($applids))
<div class="btn form-check">
  <label class="form-check-label text-white" for="checklink">
  <input class="form-check-input" type="checkbox" value="" id="checklink">
    Create Link
  </label>
</div>
@endif		
							<button type="submit" class="btn btn-success">{{ __('Send') }}</button>
							<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>						
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')

{!! Form::close() !!}
				</div>
			</div>

	
@endsection

@push('scripts')
@include('scripts.src_select2', ['summernote'=>true])
@include('scripts.src_summernote')
@include('scripts.src_datatables')
@endpush 
	
@section('js')
@parent


   <script src="{{ asset('js/autosize.min.js') }}" rel="javascript"></script>
 
    <script>

	
		$("document").ready(function() {
			autosize($('textarea'));
@include('scripts.ready_select2')	
@include('scripts.ready_datatables')					
	
@include('scripts.ready_summernote');

@include('scripts.ready_multiplediscard');
	
		});
@include('scripts.init_popover')		

		
    </script>
	
@stop