<div class="row">
					<a href="{{ route('clients.show',$client->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fa fa-info actionbtn" target="client{{ $client->id }}"></a>
					<a href="{{ route('clients.edit',$client->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit actionbtn" target="client{{ $client->id }}"></a>
					<a href="{{ url('clients/'.$client->id.'/jobs/create') }}" data-toggle="tooltip" title="Add Job" class="btn btn-sm actionbtn" target="jobadd{{ $client->id }}">@include('partials.icons.add_job')</a>
					<a href="{{ url('clients/'.$client->id.'/clientcontacts/create') }}" data-toggle="tooltip" title="Add Contact" class="btn btn-sm actionbtn" target="contactadd{{ $client->id }}">@include('partials.icons.add_clientcontact')</a>
					<a href="{{ url('calendarevents/add') }}?client_id={{ $client->id }}" data-toggle="tooltip" title="Add Event" class="btn btn-sm actionbtn" target="eventaddclnt{{ $client->id }}">@include('partials.icons.add_event')</a>
		@if (!isset($item->deletable) || $item->deletable)	
				{!! Form::open(['method' => 'DELETE','route' => ['clients.destroy', $client->id],'style'=>'display:inline']) !!}
				{!! Form::button('', ['class' => 'btn btn-sm text-danger fa fa-trash actionbtn', 'type' => 'submit', 'data-toggle' => 'tooltip', 'title' => 'Delete']) !!}
				{!! Form::close() !!}
		@endif	
		
@if (isset($candidate) && $candidate)
    @if (!$candidate->jobapplications->contains('job_ad_id', $client->id))

						
					<a href="javascript:registerInterest('{{ route('linkcandidateclient', [$candidate->id, $client->id]) }}', 'lnkclnt{{ $client->id }}')" data-toggle="tooltip" title="Link" class="btn btn-sm"  id="lnkclnt{{ $client->id }}">	
					@include('partials.icons.link_candidate')
						</a>		
	@endif
@else

	
@endif	
</div>