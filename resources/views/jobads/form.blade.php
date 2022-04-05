@extends('layouts.tab')

@section('tabheader')
			<div class="job card-header sticky-top mb-1"><h3>@if (isset($jobad->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Job') }}
			@if (isset($jobad->id)) {{ __('('.$jobad->jobref.') - '.$jobad->jobtitle_text) }} @else  {{ __('('.current(reset($clients)).')') }}@endif 
			</h3></div>
@endsection

@section('content')

	<div class="card">
		<div class="card-body">

@include('partials.flashmessages')

@if (isset($jobad->id))				
	@include('staticwork.emailssentnotice', ['viewobj'=>'jobad'])			

					{!! Form::model($jobad, ['method' => 'PATCH','route' => ['jobs.update', $jobad->id]]) !!}
@else
					{!! Form::open(array('url' => Request::url(),'method'=>'POST')) !!}
@endif

<div class="row">
<div class="col-md-2">

@if (isset($nextjobnumber))
	
<div class="form-label-group in-border row">
		<div class="input-group">
	<input size="3" maxlength="3" placeholder="" class="form-control col-md-4"  autofocus autocomplete name="jobref" type="text" id="jobref">
	<label for="jobref" class="col-form-label text-md-right">Reference Code</label>
					<div class="input-group-append">
					  <span class="input-group-text">{{  $nextjobnumber }}</span>
				</div>
						</div>
						
<input type="hidden" name="nextjobnumber" value="{{ $nextjobnumber }}">
                                @error('jobref')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>	
					

@else
@include('partials.form_text', ['fieldname'=>'jobref', 
								'fieldlabel'=>'Reference Code', 
								'fielddefault'=> null,
								'is_required'=>true])	
	@endif


</div>
<div class="col-md-10">
@include('partials.form_text', ['fieldname'=>'jobtitle_text', 
								'fieldlabel'=>'Job Title', 
								'fielddefault'=> null,
								'is_required'=>true])	
</div>
</div>

@include('partials.select2_dropdown_multiple', ['fieldname'=>'locations[]', 
									'fieldlabel'=>'Locations', 
									'fieldplaceholder'=>'Select Locations', 
									'options'=>$alllocations,
									'selectedoptions'=>old('locations', isset($locations) ? $locations : null)]) 	

<div  class="card-deck">

 @include('partials.client_and_contact_edit_block', ['client_id'=>isset($jobad) ? $jobad->client_id : null])			


		<div class="card card-body">
@include('partials.select2_dropdown_single', ['fieldname'=>'consultant_id', 
									'fieldlabel'=>'Consultant', 
									'fieldplaceholder'=>'Choose Consultant', 
									'options'=>$consultants,
									'selectedid'=>old('consultant_id', isset($jobad) ? $jobad->consultant_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'status_id', 
									'fieldlabel'=>'Status', 
									'fieldplaceholder'=>'Choose Status', 
									'options'=>$statuses,
									'selectedid'=>old('status_id', isset($jobad) ? $jobad->status_id : null)])


@include('partials.select2_dropdown_single', ['fieldname'=>'ee_status_id', 
									'fieldlabel'=>'EE Status', 
									'fieldplaceholder'=>'Choose EE Status', 
									'options'=>$eestatuses,
									'selectedid'=>old('ee_status_id', isset($jobad) ? $jobad->ee_status_id : null)])
	
@include('partials.select2_dropdown_single', ['fieldname'=>'gender_id', 
									'fieldlabel'=>'Gender', 
									'fieldplaceholder'=>'Choose Gender', 
									'options'=>$genders,
									'select2_class' => 'select-select2-optional',
									'required'=>false,
									'selectedid'=>old('gender_id', isset($jobad) ? $jobad->gender_id : null)])
</div>
</div>

	<div class="card mb-3">
@include('partials.jobads.salary_edit_block')
	</div>


@include('partials.longtext.longtext_edit', ['ltfieldname'=>'cvsendinstructions', 'ltfieldlabel'=>'CV Send Instructions', 'ltfieldmodel'=> isset($jobad->cvsendinstructions) ? $jobad->cvsendinstructions : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'summary', 'ltfieldlabel'=>'Summary', 'ltfieldmodel'=> (isset($jobad->summary)) ? $jobad->summary : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'agencynotes', 'ltfieldlabel'=>'Agency Notes', 'ltfieldmodel'=> isset($jobad->agencynotes) ? $jobad->agencynotes : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'projectplan', 'ltfieldlabel'=>'Project Plan', 'ltfieldmodel'=> isset($jobad->projectplan) ? $jobad->projectplan : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'skills', 'ltfieldlabel'=>'Skills', 'ltfieldmodel'=> isset($jobad->skills) ? $jobad->skills : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'fulldescription', 'ltfieldlabel'=>'Description', 'ltfieldmodel'=> isset($jobad->fulldescription) ? $jobad->fulldescription : null , 'ltrequired'=>''])
						


@if (isset($jobad->id))	
    <div class="card card-body">	
	@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Edit Comment', 'ltfieldmodel'=> old('comments', null) , 'ltrequired'=>''])
	</div>
@endif


@section('formbuttons')
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
@endsection			
@include('partials.footer.formbuttonsSection', ['tag_textarea'=>true])
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
		
					
		});
		
		
    </script>
@endsection