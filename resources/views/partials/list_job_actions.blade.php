
					<a href="{{ route('jobs.show',$job->id) }}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fas fa-info actionbtn" target="job{{ $job->id }}"></a>
					<a href="{{ route('jobs.edit',$job->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit actionbtn" target="job{{ $job->id }}"></a>

@if (isset($job->documents) && (count($job->documents)))
	@include('partials.documents_paperclip_list', ['documentsid'=>$job->id, 'documents'=>$job->documents, 'doctype'=>'jobs'])
@endif

@if (isset($candidate) && $candidate)
	    @if (!$candidate->jobapplications->contains('job_ad_id', $job->id))

					<a href="javascript:registerInterest('{{ route('linkcandidatejob', [$candidate->id, $job->id]) }}', 'linkjb{{ $job->id }}')" data-toggle="tooltip" title="Link" class="btn btn-sm text-primary fa fa-link actionbtn" id="linkjb{{ $job->id }}"></a>
		@endif		
@else
	@if (!isset($isReport) || !$isReport)
					<a class="btn btn-sm actionbtn" title="Match Candidate" target="matchcand{{ $job->id }}" href="{{ route('candidates.index').'?jobid='.$job->id }}">
							@include('partials.icons.search_candidate')
					</a>
	@endif
@endif
