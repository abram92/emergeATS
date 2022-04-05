@extends('layouts.tab')

@section('tabheader')
	<div class="card-header sticky-top sticky-top coloration candidate1 client2"><h3>{{ __('Email Candidate ('.$candidate->user->listname.') to Multiple Clients') }}</h3></div>
@endsection

@section('content')



			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

					{!! Form::open(array('route' => ['emailcvtoclients.store', $candidate->id],'method'=>'POST')) !!}


@if($candidate)
				<div class="card candidate-outline">
				<div class="card-body candidate">
						<div class="row ">
						<h4>{{ __($candidate->user->listname) }}</h4>
@if($candidate->agencynotes)	
<div class="float-left">	
<button type="button" class="btn btn-danger pt-0 pb-0 ml-3" data-toggle="modal" data-target="#textDetail" title="" data-candidate="{{ __($candidate->user->listname)  }}" data-content="{{ $candidate->agencynotes->chunk }}">Agency Notes</button>
</div>
@endif
@if($candidate->textcv)
		
<div class="float-left">	
<button type="button" class="btn btn-primary pt-0 pb-0 ml-3 " data-toggle="modal" data-target="#textDetail" title="" data-candidate="{{ __($candidate->user->listname)  }}" data-content="{{ $candidate->textcv->chunk }}">Text CV</button>
</div>
<div class="float-left pt-0 pb-0 ml-3">
	<input type="checkbox" name="attachCVIds[]" class="chk"  value="{{ $candidate->id }}">
	{!! Form::label('cndcv'.$candidate->id, 'Attach Text CV', array('class' => 'col-form-label')) !!}
</div>
@endif
						</div>
			@include('partials.jobapplications.send_documents_card', ['model'=>'candidate', 'modelid'=>$candidate->id, 'documents'=>$candidate->documents])
 
						
<hr class="candidate">
				</div>
				</div>
@endif

		
@foreach ($jobads as $jobad)

				<div class="card job-outline">
				
            <div class="card-header job">
		   
				<div class="row">
					<div class="col-6">
					<div class="row">
						<h4>{{ __($jobad->jobref) }}</h4>
						@if ($jobads->count() > 1)
					<div class="ml-auto mr-3"><input type='checkbox' name="discardIds[]" class="chk discardchk" @if(in_array($jobad->id, old('discardIds', []))) checked @endif value="{{ $jobad->id }}"> Discard</div>
						@endif
					</div>
						<h5>{{ __($jobad->jobtitle_text) }}</h5>
					</div>
					<div class="col-6 detail{{ $jobad->id }}">
				@include('partials.jobads.jobcontacts_list', ['contacts'=>$jobad->clientcontacts])
					</div>
				</div>
		   
			</div>
				<div class="card-body detail{{ $jobad->id }}" id="body{{ $jobad->id }}">
		   
			
@include('partials.select2_dropdown_multiple', ['fieldname'=>'contacts['.$jobad->id.'][]', 
									'fieldlabel'=>'Client Contacts', 
									'fieldplaceholder'=>'Select Contacts',
									'options'=> isset ($allcontacts[$jobad->client_id]) ? $allcontacts[$jobad->client_id] : [],
									'selectedoptions'=>old('contacts.'.$jobad->id, isset($contacts[$jobad->id]) ? $contacts[$jobad->id] : null)])

@include('partials.form_email', ['fieldname'=>'cc['.$jobad->id.']', 'fieldlabel'=>'CC'])		

@include('partials.form_text', ['fieldname'=>'subject['.$jobad->id.']', 'fieldlabel'=>'Subject'])		

 

@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'coverpage_'.$jobad->id, 'ltfieldlabel'=>'Cover Page', 'ltfieldmodel'=>  null , 'ltrequired'=>''])

</div>
</div>
<hr class="job">

@endforeach		

@foreach ($clients as $client)
				<div class="card client-outline">
				
				<div class="card-header client">
						<div class="row">
						<h4>{{ __($client->name) }}</h4>
						@if ($clients->count() > 1)
						<div class="ml-auto mr-3"><input name="discardIds[]" type='checkbox' class="chk discardchk" @if(in_array($client->id, old('discardIds', []))) checked @endif value="{{ $client->id }}"> Discard</div>
						@endif
						</div>

				</div>
				<div class="card-body detail{{ $client->id }}" id="body{{ $client->id }}">
		   
@include('partials.select2_dropdown_multiple', ['fieldname'=>'contacts['.$client->id.'][]', 
									'fieldlabel'=>'Client Contacts', 
									'fieldplaceholder'=>'Select Contacts', 
									'options'=>isset ($allcontacts[$client->id]) ? $allcontacts[$client->id] : [],
									'selectedoptions'=>old('contacts.'.$client->id, isset($contacts[$client->id]) ? $contacts[$client->id] : null)])

@include('partials.form_email', ['fieldname'=>'cc['.$client->id.']', 'fieldlabel'=>'CC'])		

@include('partials.form_text', ['fieldname'=>'subject['.$client->id.']', 'fieldlabel'=>'Subject'])		

 

@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'coverpage_'.$client->id, 'ltfieldlabel'=>'Cover Page', 'ltfieldmodel'=>  null , 'ltrequired'=>''])

</div>
</div>
<hr class="client">

@endforeach	

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


<div class="modal fade" id="textDetail" tabindex="-1" role="dialog" aria-labelledby="textDetailLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title" id="textDetailLabel"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body text-wrap">
          <pre id="modal-text" class="wraptext">
          </pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
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


    <script>
	
		$("document").ready(function() {
			
@include('scripts.ready_select2')	
@include('scripts.ready_datatables')					

@include('scripts.ready_summernote');

@include('scripts.ready_multiplediscard');

$('#textDetail').on('show.bs.modal', function (event) {

  var button = $(event.relatedTarget) // Button that triggered the modal
  var content = button.data('content') 
  var candidate = button.data('candidate');
  
	bgcolor = button.css( "background-color" ) 
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-header').css("background-color", bgcolor)
  modal.find('.modal-title').text(button.text() + ' for ' + candidate)
  modal.find('.modal-body pre').text(content.replace(/\r\n/g,'\n'))
})

  

					
		});
@include('scripts.init_popover')		
    </script>
	
@stop

						
