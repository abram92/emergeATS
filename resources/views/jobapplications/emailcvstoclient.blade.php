@extends('layouts.tab')

@section('tabheader')

<div class="card-header mb-1 sticky-top coloration candidate1 client2 }}">
	<div class="row">
		<span><h3>{{ __('Email CVs to Client') }}</h3></span>
@if ($target instanceOf App\JobAd)
        <span class="badge1 shadow job ml-auto mr-1">  
			<span><h4>
				{{ __($target->jobref) }}
			</h4></span>
			<span><h5 class="scrollhide">
				({{ __($target->jobtitle_text) }})
			</h5></span>
		</span>
@else
        <span class="badge1 shadow client ml-auto mr-1"> 
			<h4>
				{{ __($target->client->name) }}
			</h4>
		</span>	
@endif			
	</div>		
</div>

@endsection


@section('content')


		@if($target instanceOf App\JobAd)
            <div class="card-header job">
		   
				<div class="row">
					<div class="col-3">
						<h4>{{ __($target->jobref) }}</h4>
						<h5>{{ __($target->jobtitle_text) }}</h5>
					</div>
					<div class="col-3">
					</div> 
					<div class="col-6">
				@include('partials.jobads.jobcontacts_list', ['contacts'=>$target->clientcontacts])
					</div>
				</div>
		   
			</div>
		@else
            <div class="card-header client">
		   
				<div class="row">
					<h4>{{ __($target->name) }}</h4>
				</div>
		   	</div>
		@endif
			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

					{!! Form::open(array('route' => ['emailcvstoclient.store', $matchid],'method'=>'POST')) !!}

@include('partials.select2_dropdown_multiple', ['fieldname'=>'contacts[]', 
									'fieldlabel'=>'Client Contacts', 
									'fieldplaceholder'=>'Select Contacts', 
									'options'=>$allcontacts,
									'selectedoptions'=>old('contacts', isset($contacts) ? $contacts : null)])

@include('partials.form_email', ['fieldname'=>'cc', 'fieldlabel'=>'CC'])		

@include('partials.form_text', ['fieldname'=>'subject', 'fieldlabel'=>'Subject'])		

 

@include('partials.longtext.longtext_edit_pair_summernote', ['ltfieldname'=>'coverpage', 'ltfieldlabel'=>'Cover Page', 'ltfieldmodel'=>  null , 'ltrequired'=>''])

@if($candidates)
				<div class="card  candidate-outline">
				<div class="card-header candidate">
					<h4 style="display:inline">Candidates </h4>
				</div>
				<div class="card-body">
@foreach($candidates as $candidate)
						<div class="row">
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
@endforeach
				</div>
				</div>
@endif

				

@section('formbuttons')
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

						
