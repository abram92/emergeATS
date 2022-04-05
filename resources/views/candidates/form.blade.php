@extends('layouts.tab')

@section('tabheader')
	<div class="candidate card card-header sticky-top mb-1">
		<h3>@if (isset($candidate->id)) {{ __('Edit '.$candidate->user->listname) }} @else {{ __('Add Candidate') }} @endif</h3>
	</div>

@endsection

@section('content')

	<div class="mt-5 candidate-container mb-5 container">
		<div class="card-body">

@include('partials.flashmessages')

@if (isset($candidate->id))
	@include('staticwork.emailssentnotice', ['viewobj'=>'candidate'])
					{!! Form::model($candidate, ['method' => 'PATCH','route' => ['candidates.update', $candidate->id]]) !!}
@else
					{!! Form::open(array('route' => 'candidates.store','method'=>'POST')) !!}
@endif

<div  class="card-deck">
	<div class="card card-body  border-0">


@include('partials.users.details_edit', ['userdetail'=>isset($candidate) ? $candidate->user : null])


@include('partials.form_text', ['fieldname'=>'idnumber', 'fieldlabel'=>'ID Number'])

@include('partials.form_date', ['fieldname'=>'birthdate', 'fieldlabel'=>'Birth Date'])

@include('partials.select2_dropdown_single', ['fieldname'=>'gender_id',
									'fieldlabel'=>'Gender',
									'fieldplaceholder'=>'Choose Gender',
									'options'=>$genders,
									'select2_class' => 'select-select2-optional',
									'required'=>false,
									'selectedid'=>old('gender_id', isset($candidate) ? $candidate->gender_id : null)])
@include('partials.select2_dropdown_single', ['fieldname'=>'consultant_id',
									'fieldlabel'=>'Consultant',
									'fieldplaceholder'=>'Choose Consultant',
									'options'=>$consultants,
									'required'=>true,
									'oninvalid'=>'Consultant must be assigned',
									'selectedid'=>old('consultant_id', isset($candidate) ? $candidate->consultant_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'status_id',
									'fieldlabel'=>'Status',
									'fieldplaceholder'=>'Choose Status',
									'options'=>$statuses,
									'required'=>true,
									'oninvalid'=>'Status cannot be blank',
									'selectedid'=> old('status_id', isset($candidate) ? $candidate->status_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'availability_id',
									'fieldlabel'=>'Availability',
									'fieldplaceholder'=>'Choose Availability',
									'select2_class' => 'select-select2-optional',
									'options'=>$availabilities,
									'selectedid'=>old('availability_id', isset($candidate) ? $candidate->availability_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'candidate_level_id',
									'fieldlabel'=>'Candidate Level',
									'fieldplaceholder'=>'Choose Level',
									'options'=>$candidatelevels,
									'selectedid'=>old('candidate_level_id', isset($candidate) ? $candidate->candidate_level_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'candidate_rating_id',
									'fieldlabel'=>'Candidate Rating',
									'fieldplaceholder'=>'Choose Rating',
									'options'=>$candidateratings,
									'selectedid'=>old('candidate_rating_id', isset($candidate) ? $candidate->candidate_rating_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'ee_status_id',
									'fieldlabel'=>'EE Status',
									'fieldplaceholder'=>'Choose EE Status',
									'options'=>$eestatuses,
									'selectedid'=>old('ee_status_id', isset($candidate) ? $candidate->ee_status_id : null)])

	</div>
	<div class="card card-body">
    <div class="card-deck">
        	<div class="card">
@include('partials.contacts.contact_edit_block')
	</div>
    </div>

    <div class="card-deck">
	<div class="card">
@include('partials.addresses.address_edit_block')
	</div>


{{-- @include('partials.select2_dropdown_single', ['fieldname'=>'consultant_id',
									'fieldlabel'=>'Consultant',
									'fieldplaceholder'=>'Choose Consultant',
									'options'=>$consultants,
									'required'=>true,
									'oninvalid'=>'Consultant must be assigned',
									'selectedid'=>old('consultant_id', isset($candidate) ? $candidate->consultant_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'status_id',
									'fieldlabel'=>'Status',
									'fieldplaceholder'=>'Choose Status',
									'options'=>$statuses,
									'required'=>true,
									'oninvalid'=>'Status cannot be blank',
									'selectedid'=> old('status_id', isset($candidate) ? $candidate->status_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'availability_id',
									'fieldlabel'=>'Availability',
									'fieldplaceholder'=>'Choose Availability',
									'select2_class' => 'select-select2-optional',
									'options'=>$availabilities,
									'selectedid'=>old('availability_id', isset($candidate) ? $candidate->availability_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'candidate_level_id',
									'fieldlabel'=>'Candidate Level',
									'fieldplaceholder'=>'Choose Level',
									'options'=>$candidatelevels,
									'selectedid'=>old('candidate_level_id', isset($candidate) ? $candidate->candidate_level_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'candidate_rating_id',
									'fieldlabel'=>'Candidate Rating',
									'fieldplaceholder'=>'Choose Rating',
									'options'=>$candidateratings,
									'selectedid'=>old('candidate_rating_id', isset($candidate) ? $candidate->candidate_rating_id : null)])

@include('partials.select2_dropdown_single', ['fieldname'=>'ee_status_id',
									'fieldlabel'=>'EE Status',
									'fieldplaceholder'=>'Choose EE Status',
									'options'=>$eestatuses,
									'selectedid'=>old('ee_status_id', isset($candidate) ? $candidate->ee_status_id : null)]) --}}
	</div>

</div>

	{{-- <div class="card">
@include('partials.contacts.contact_edit_block')
	</div> --}}
</div>

	{{-- <div class="card border-0 bg-light mb-3">
@include('partials.candidates.location_edit_block')
	</div> --}}

	<div class="card border-0 bg-light mb-3">
@include('partials.candidates.jobtitle_edit_block')
	</div>

	<div class="card border-0 mb-3">
@include('partials.candidates.salary_edit_block')
	</div>


@include('partials.longtext.longtext_edit', ['ltfieldname'=>'sellme', 'ltfieldlabel'=>'Core Skills', 'ltfieldmodel'=> (isset($candidate->sellme) ) ? $candidate->sellme : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'textcv', 'ltfieldlabel'=>'Text CV', 'ltfieldmodel'=> (isset($candidate->textcv) ) ? $candidate->textcv : null , 'ltrequired'=>''])


@include('partials.form_checkbox', ['fieldname'=>'interviewed',
									'fieldlabel'=>'Interviewed',
									'fielddefault'=>old('interviewed', isset($candidate->interviewed) ? $candidate->interviewed : '')])


@include('partials.longtext.longtext_edit', ['ltfieldname'=>'interviewnotes', 'ltfieldlabel'=>'Interview Notes', 'ltfieldmodel'=> (isset($candidate->interviewnotes)) ? $candidate->interviewnotes : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'agencynotes', 'ltfieldlabel'=>'Agency Notes', 'ltfieldmodel'=> (isset($candidate->agencynotes)) ? $candidate->agencynotes : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'idealjob', 'ltfieldlabel'=>'Ideal Job', 'ltfieldmodel'=> (isset($candidate->idealjob)) ? $candidate->idealjob : null , 'ltrequired'=>''])
@include('partials.longtext.longtext_edit', ['ltfieldname'=>'summary', 'ltfieldlabel'=>'Summary', 'ltfieldmodel'=> (isset($candidate->summary)) ? $candidate->summary : null , 'ltrequired'=>''])


@if (isset($candidate->id))
    <div class="card card-body">
	@include('partials.longtext.longtext_edit', ['ltfieldname'=>'comments', 'ltfieldlabel'=>'Edit Comment', 'ltfieldmodel'=> old('comments', null) , 'ltrequired'=>''])
</div>
@endif




@section('formbuttons')
	<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
	<button type="button" class="btn btn-close">{{ __('Close') }}</button>
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

    $(document).on("click", ".btn-close" , function(){
        window.close();
    });


	var contacttypes = @json($contact_types);

	$('#idnumber').bind("change keyup input",function(){

		var now = new Date();

		let ele = this.value;
		var res = ele.substring(0, 6);

		if (/^([0-9]{6})$/.test(res)) {

			let finalVal = res.match(/.{1,2}/g).join('-');
			century = ((now.getYear() - ele.substring(0, 2)) > 100) ? '20' : '19';
			datefromid = century +  finalVal;//now.getFullYear()  + '-' + ('0' + (now.getMonth()+1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2);
			if (!isNaN(new Date(datefromid).getDate()))
				$('#birthdate').val(datefromid);
		}
  });


			@include('scripts.ready_select2')

@include('scripts.contacts.ready_contact_edit_js');
@include('scripts.addresses.ready_address_edit_js')

		});


    </script>
@endsection
