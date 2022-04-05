@extends('layouts.tab')

@section('title', __( 'Email : '.$email->date))

@section('tabheader')
<div class="card card-header sticky-top email">
<div class="row"> 
					<div class="col-xs-9 col-sm-9 col-md-9"><div><h3><span title="Email" class="fa fa-email">&nbsp;&nbsp;</span>{{ __( $email->subject) }}</h3></div>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 text-right">
						<h4>
						<span class="badge" >{{ $email->date }}</span>
						</h4>
					</div>
				</div>
</div>
@endsection
@section('content')

            <div class="card">

                <div class="card-body">
                    @csrf
	<div class="card-deck">				
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="from" class="col-md-4 text-md-right">{{ __('From') }}</label>
							<div class="col-md-6" id="from">
                                <strong>{{ old('address_from', $email->sender->listname ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="to" class="col-md-4 text-md-right">{{ __('To') }}</label>
							<div class="col-md-6" id="to">
                                <strong>{{ old('address_to', $email->address_to ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="cc" class="col-md-4 text-md-right">{{ __('Cc') }}</label>
							<div class="col-md-6" id="cc">
                                <strong>{{ old('address_cc', $email->address_cc ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="bcc" class="col-md-4 text-md-right">{{ __('Bcc') }}</label>
							<div class="col-md-6" id="bcc">
                                <strong>{{ old('address_bcc', $email->address_bcc ) }}</strong>
                            </div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group row">
							<label for="messageId" class="col-md-4 text-md-right fa fa-envelope" data-toggle="tooltip" title="Message Id"></label>
							<div class="col-md-6" id="messageId">
                                <strong>{{ old('messageId', $email->messageId ) }}</strong>
                            </div>
						</div>
					</div>
	</div>
					@include('partials.collapse_textarea', ['field'=>'messagebody', 'start_expanded'=>'true', 'is_html'=>true, 
															'field_title'=>'Message', 'field_body'=>$email->body])
					@include('partials.collapse_textarea', ['field'=>'headers', 'start_expanded'=>'false', 
															'field_title'=>'Headers', 'field_body'=>$email->headers])
				</div>		
			</div>
			
@section('formbuttons')
		<a class="btn btn-primary" href="{{ url('calendarevents.index') }}"> Back</a>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')	
				

			@include('partials.attachments_card', ['model'=>'emails', 'modelid'=>$email->id])
	
<div  class="card-deck no-gutters">
@if ($candidates->count() > 0)
			<div class="card">
				<div class="card-header candidate">
					<h4>Linked Candidates</h4>
				</div>
				<div class="card-body p-0">
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Candidate</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($candidates as $key => $candidate)
			<tr>
				<td>{{ $candidate->user->listname }}</td>
				<td>
					<a href="{{ route('candidates.show',$candidate->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info"  target="candidate{{ $candidate->id }}"></a>
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
				</div>
			</div>
@endif

@if ($clients && ($clients->count() > 0))
			<div class="card">
				<div class="card-header client">
					<h4>Linked Clients</h4>
				</div>
				<div class="card-body p-0">
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Client</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($clients as $key => $client)
			<tr>
				<td>{{ $client->name }}</td>
				<td>
					<a href="{{ route('clients.show',$client->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info"  target="client{{ $client->id }}"></a>
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
				</div>
			</div>
@endif

@if ($jobads->count() > 0)
			<div class="card">
				<div class="card-header job">
					<h4>Linked Jobs</h4>
				</div>
				<div class="card-body p-0">
	<div class="table-responsive">
	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th>Job Ref</th>
				<th>Client</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="baseTable">
		@foreach ($jobads as $key => $jobad)
			<tr>
				<td>{{ $jobad->jobref }}</td>
				<td>{{ $jobad->client->name }}</td>
				<td>
					<a href="{{ route('jobs.show',$jobad->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info"  target="jobad{{ $jobad->id }}"></a>
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	</div>
				</div>
			</div>
@endif
</div>		

@endsection 
