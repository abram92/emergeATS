

@include('partials.searchfilters.query_criteria_block', ['canSave'=>true])

@if (count($data) > 0)
	
<div class="mb-0">
		@foreach ($data as $key => $candidate)
		@include('partials.candidate_view_list', ['candidate'=>$candidate])
		@endforeach
</div>
	@include('partials.show_pagination')	

	
	@if (isset($jobad) && $jobad)
@php
$is_disabled = (!session()->get('search_cand_'.$jobad->id)) ? 'disabled' : '';
$count = (session()->get('search_cand_'.$jobad->id)) ? count(session()->get('search_cand_'.$jobad->id)) : '';
@endphp		
		<div class="fixed-bottom form-group row mb-0">
		<div style="width:50px;"></div>
		<a class="btn btncheck btnchecklnk text-light  @if($is_disabled)disableClick @endif"  title="Link Candidates to Job" target="_blank" href="{{ url('linkcandidates/'.$jobad->id) }}" >@include('partials.icons.link_job_badge', ['count'=>$count, 'iconclass'=>'text-footer'])</a>
		<a class="btn btncheck btnchecklnk text-light  @if($is_disabled)disableClick  @endif"  title="Email Candidates to Client" target="_blank" href="{{ url('emailcvstoclient/j_'.$jobad->id) }}" >@include('partials.icons.email_job_badge', ['count'=>$count, 'iconclass'=>'text-footer'])</a>
	@if (Auth::user()->hasRole('Bulk Email Candidates'))
		
		<a class="btn btncheck btnchecklnk text-light @if($is_disabled)disableClick  @endif"  title="Email Job to Candidates" target="_blank" href="{{ url('emailjobspectocandidates/'.$jobad->id) }}" >@include('partials.icons.email_candidate_badge', ['count'=>$count, 'iconclass'=>'text-footer'])</a>
	@endif
		<button type="button" class="btn btncheck btncheckbtn"  id="deselectall" {{ $is_disabled }} title="Deselect All Candidates">@include('partials.icons.uncheck')</button>
		</div>
@section('contentfooter')			
	@include('partials.footer.padding')
@endsection		
		@endif

	<script src="{{ asset('js/ajaxcalls.js') }}" rel='javascript'></script>	
@else
@if ($q)	
	@include('partials.emptytable')
@endif
@endif


