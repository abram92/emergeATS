@extends('layouts.tab')

@section('title', __( $client->name))

@section('tabheader')
<div class="card card-header client sticky-top mb-1">

	<div class="row"> 
		<div class="col-xs-9 col-sm-9 col-md-9">
			<div><h3><span title="Client" class="fa fa-building">&nbsp;&nbsp;</span>{{ __( $client->name) }}</h3></div>
		</div>
		<div class="col-xs-3 col-sm-3 col-md-3 text-right">
			<h5>
						@include('partials.show_status', ['status'=>$client->status, 'show_shadow'=>true])
			</h5>
			<h5 class="consultant scrollhide">
				<span class="badge" >{{ optional($client->consultant)->fullname_username }}</span>
			</h5>
		</div>
	</div>
</div>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
                    @csrf
					<div class="card-group">				
						@include('partials.addresses.address_view_block_card')
						@include('partials.contacts.contact_view_block_card')
					</div>
					
					@include('partials.collapse_textarea', ['field'=>'techenvironment', 'start_expanded'=>'true', 
															'field_title'=>'Tech Environment', 'field_body'=>($client->techenvironment !== null) ? $client->techenvironment->chunk : ''])
					@include('partials.collapse_textarea_many', ['field'=>'agencynotes', 'start_expanded'=>'true', 
															'field_title'=>'Agency Notes', 'field_list'=> $client->agencynotes])

			
@section('formbuttons')
								<a href="{{ route('clients.edit',$client->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-primary " target="client{{ $client->id }}"><i class="fa fa-edit"></i></a>

							<a class="btn btn-primary" target="_blank" title="Add Agency Note" href="{{ url('clients/'.$client->id.'/notes/create') }}"> 
							@include('partials.icons.add_clientagencynote')
							</a>							
							<a class="btn btn-primary" title="Add Client Contact" href="{{ url('clients/'.$client->id.'/clientcontacts/create') }}"> 
							@include('partials.icons.add_clientcontact')
							</a>
							<a class="btn btn-success" title="Add Job" href="{{ url('clients/'.$client->id.'/jobs/create') }}">
							@include('partials.icons.add_job')
							</a>
							<a class="btn btn-danger" title="Link Candidate" href="{{ route('clients.index') }}">
							@include('partials.icons.link_candidate')
							</a>
@endsection			
@include('partials.footer.formbuttonsSection')
@yield('contentbuttons')		

			@include('partials.documents_card', ['model'=>'clients', 'modelid'=>$client->id])
	

			<div class="card clientcontact-outline">
				<div class="card-header clientcontact">
					<h4>Contacts</h4>
				</div>
				<div class="card-body">
				@include('partials.clients.staff_list')
				</div>
			</div>

			<div class="card job-outline">
				<div class="card-header job">
					<h4>Jobs</h4>
				</div>
				<div class="card-body">
				@include('partials.clients.job_summary_list')
				</div>
			</div>

			<div class="card candidate-outline">
				<div class="card-header coloration client1 candidate2 text-light">
					<h4>Linked Candidates</h4>
				</div>
				<div class="card-body">
				@include('partials.clients.directapplication_summary_list',['tableid'=>'da_summary'])
				</div>
			</div>

			<div class="card candidate-outline">
				<div class="card-header coloration job1 candidate2 text-light">
					<h4>Candidates Linked To Jobs</h4>
				</div>
				<div class="card-body">
				@include('partials.clients.jobapplication_summary_list',['tableid'=>'ja_summary'])
				</div>
			</div>

@include('partials.audit_trail', ['statusArr'=>$statuses])

		</div>		
	</div>			

@endsection 


@section('js')
@parent
 					@include('partials.dropzone_def_js', ['modelurl'=>'clients/'.$client->id])
			
<script>					
		$("document").ready(function() {
						
@include('scripts.ready_datatables')
@include('scripts.ready_datatables_group')
		
		$("#maindetail").css("margin-bottom", $("#buttonfooter").css("height"));

		});					
</script>
					
@stop