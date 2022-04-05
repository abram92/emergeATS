<div class="row">
@php $rankvars= ""; @endphp
		@if (isset($ranks) && !empty($ranks))
@php  $rankvars .= "?"; @endphp
@endif				
@if (isset($ranks['sellme']) && isset($ranks['sellme'][$candidate->id]))
@php				$rankvars .= "&skillsrank=".number_format($ranks['sellme'][$candidate->id], 2); @endphp
@endif				
@if (isset($ranks['textcv']) && isset($ranks['textcv'][$candidate->id]))
@php				$rankvars .= "&cvrank=".number_format($ranks['textcv'][$candidate->id], 2); @endphp
@endif		
					<a href="{{ route('candidates.show',$candidate->id) }}{!! $rankvars !!}" data-toggle="tooltip" title="Show" class="btn btn-sm text-info fas fa-info actionbtn" target="candidate{{ $candidate->id }}"></a>
					<a href="{{ route('candidates.edit',$candidate->id) }}" data-toggle="tooltip" title="Edit" class="btn btn-sm text-primary fa fa-edit actionbtn" target="candidate{{ $candidate->id }}"></a>

@if (isset($candidate->documents) && (count($candidate->documents)))
	@include('partials.documents_paperclip_list', ['documentsid'=>$candidate->id, 'documents'=>$candidate->documents, 'doctype'=>'candidates'])
@endif

@if (isset($jobad) && $jobad)
    @if (!$jobad->jobapplications->contains('candidate_id', $candidate->id))

					<a href="javascript:registerInterest('{{ route('linkcandidatejob', [$candidate->id, $jobad->id]) }}', 'linkcnd{{ $candidate->id }}')" data-toggle="tooltip" title="Link" class="btn btn-sm text-secondary fa  fa-link actionbtn"  id="linkcnd{{ $candidate->id }}"></a>
	@endif
@else
	@if (!isset($isReport) || !$isReport)
					<a class="btn btn-sm actionbtn" title="Match Job" target="matchjob{{ $candidate->id }}"  href="{{ route('jobs.index').'?candid='.$candidate->id }}" target="_blank">
							@include('partials.icons.search_job')
							</a>
	@endif
@endif
	@if (!isset($isReport) || !$isReport)
					<a class="btn btn-sm actionbtn" title="Link To Client" target="linkclient{{ $candidate->id }}"  href="{{ route('clients.index').'?candid='.$candidate->id }}" target="_blank">
							@include('partials.icons.link_client')
							</a>
	@endif
							
</div>