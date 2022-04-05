@extends('layouts.tab')

@section('tabheader')
	<div class="card-header evnt sticky-top mb-1"><h3> {{ __('Add Event') }} </h3></div>
@endsection

@section('content')

   <script src="/js/moment.min.js"></script>
	
	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')
				
					{!! Form::open(array('route' => ['calendarevents.store'],'method'=>'POST')) !!}
			<input class="form-control" type="hidden" id="event_id">

<div class="col-xs-12 col-sm-12 col-md-12">
@include('partials.form_text', ['fieldname'=>'title', 
								'fieldlabel'=>'Title', 
								'fielddefault'=> old('title', isset($event) ? $event->title : null ),
								'is_required'=>true])		
	@include('partials.select2_dropdown_single', ['fieldname'=>'type_id', 
									'fieldid'=>'type_id',
									'fieldlabel'=>'Type', 
									'required'=>true,
									'fieldplaceholder'=>'Choose Event Type', 
									'options'=>$alleventtypes,
									'selectedid'=>old('type_id', isset($event) ? $event->type_id : null )])	
</div>
				<div class="row">
						
							<div class="form-label-group in-border col-6">
				<div class="input-group-append">

								<input placeholder="" class="form-control "   autocomplete name="start_date" type="date"><input class="form-control" name="start_time" type="time" id="start_time">
							<label for="start_date" class="col-form-label text-md-right">Start</label>
				</div>				
							</div>
							<div class="form-label-group in-border col-6">
				<div class="input-group-append">
								<input placeholder="" class="form-control "   autocomplete name="end_date" type="date">
								<label for="end_date" class="col-form-label text-md-right">End</label>
								<input class="form-control" name="end_time" type="time" id="end_time">
							
				</div>				
							</div>
				
				</div>	
									
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Comments', 'ltfieldmodel'=> isset($comments) ? $comments : null , 'ltrequired'=>''])

@include('partials.select2_dropdown_multiple', ['fieldname'=>'candidatelist', 
									'fieldid'=>'candidatelist', 
									'fieldlabel'=>'Candidates', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Candidates', 
									'options'=>$candidatelist,
									'selectedoptions'=>old('candidatelist', isset($candidatelist) ? $candidatelist : null)])

@include('partials.select2_dropdown_multiple', ['fieldname'=>'joblist', 
									'fieldid'=>'joblist', 
									'fieldlabel'=>'Job References', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Jobs', 
									'options'=>$joblist,
									'selectedoptions'=>old('joblist', isset($joblist) ? $joblist : null)])

@include('partials.select2_dropdown_multiple', ['fieldname'=>'clientlist', 
									'fieldid'=>'clientlist', 
									'fieldlabel'=>'Clients', 
									'select2_class'=>'select-select2-optional select2-fetch',
									'fieldplaceholder'=>'Select Clients', 
									'options'=>$clientlist,
									'selectedoptions'=>old('clientlist', isset($clientlist) ? $clientlist : null)])
									

			
@section('formbuttons')
	<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
	<button type="button" class="btn btn-close">{{ __('Close') }}</button>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')	
		

{!! Form::close() !!}
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

@include('scripts.ready_select2_fetch_autocomplete')

		  
	});	
	

</script>	
@endsection	