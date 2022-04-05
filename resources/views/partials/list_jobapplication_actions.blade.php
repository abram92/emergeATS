					<a href="{{ route('jobapplications.show',$jobappl->id) }}" data-toggle="tooltip" title="Show Application" class="btn btn-sm text-info fas fa-info actionbtn" target="jobappl{{ $jobappl->id }}"></a>
					<a href="{{ route('jobapplications.edit',$jobappl->id) }}" data-toggle="tooltip" title="Edit Application" class="btn btn-sm text-primary fa fa-edit actionbtn" target="jobappl{{ $jobappl->id }}"></a>
@if (isset($sent))
					<a href="{{ route('linkcandidatejob',$jobappl->id, $candidateid) }}" target="_blank" data-toggle="tooltip" title="Show Emails" class="btn btn-sm text-primary fa fa-email actionbtn"></a>
@endif
@if (!isset($candidate))
					<a href="{{ route('candidates.show',$jobappl->candidate->id) }}" target="_blank" data-toggle="tooltip" title="Show Candidate" class="text-candidate btn btn-sm fa fa-info actionbtn"></a>
			@if ($jobappl->candidate->agencynotes && $jobappl->candidate->agencynotes->chunk)
					<span data-toggle="tooltip" title="Show Agency Notes" class="shortlistagnotes text-candidate btn btn-sm fa fa-sticky-note actionbtn"></span>
			@endif				
@endif
@if  ($jobappl->applicationable_type == 'App\JobAd')
		@if (!isset($jobad))
					<a href="{{ route('jobs.show',$jobappl->jobad->id) }}" target="_blank" data-toggle="tooltip" title="Show Job" class="text-job btn btn-sm fa fa-info actionbtn"></a>
		@endif
			@if ($jobappl->jobad->cvsendinstructions && $jobappl->jobad->cvsendinstructions->chunk)
					<span data-toggle="tooltip" title="Show Send CV Instructions" class="shortlistsendcv text-job btn btn-sm fa fa-file-signature actionbtn"></span>
			@endif
@endif
			@if ($jobappl->comments)
					<span data-toggle="tooltip" title="Show Comment" class="shortlistcomment btn btn-sm fa fa-comment actionbtn"></span>
			@endif
			
					<a href="{{ url('emailcvstoclient/a_'.$jobappl->id) }}" data-toggle="tooltip" title="Email CV to Client" class="btn btn-sm text-info actionbtn" target="sendcv{{ $jobappl->id }}">
							@include('partials.icons.email_client')
					
					</a>			