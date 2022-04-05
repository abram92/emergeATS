

@include('partials.searchfilters.query_criteria_block', ['canSave'=>true])

@if (count($data) > 0)
	<div class="mb-0">
		@foreach ($data as $key => $job)
			<tr>
				<td>
				@include('partials.job_view_list', ['job'=>$job])
				</td>
			</tr>
		@endforeach
	</div>	
		
	@include('partials.show_pagination')	

	@if (isset($candidate) && $candidate)
@php
$is_disabled = (!session()->get('search_job_'.$candidate->id)) ? 'disabled' : '';
$count = (session()->get('search_job_'.$candidate->id)) ? count(session()->get('search_job_'.$candidate->id)) : '';
@endphp		
		<div class="fixed-bottom  form-group row mb-0">
		<div style="width:50px;"></div>
		<a class="btn btncheck btnchecklnk text-light @if($is_disabled)disableClick  @endif" title="Link Jobs to Candidate" target="_blank" href="{{ url('linkjobs/'.$candidate->id) }}" { $is_disabled }}>@include('partials.icons.link_job_badge', ['count'=>$count])</a>
			<a class="btn btncheck btnchecklnk text-light @if($is_disabled)disableClick  @endif" title="Email Jobs to Candidate" target="_blank" href="{{ route('emailjobspecstocandidate.create',$candidate->id) }}" {{ $is_disabled }}>@include('partials.icons.email_job_badge', ['count'=>$count])</a> 
	@if (config('bulk.multiclient', false) === true)		
		<a class="btn btncheck btnchecklnk text-light @if($is_disabled)disableClick  @endif"  title="Email Candidate to Clients" target="_blank" href="{{ url('emailcvtoclients/'.$candidate->id) }}" >@include('partials.icons.email_client_badge', ['count'=>$count])</a>
	@endif			
		<button type="button" class="btn btncheck btncheckbtb"  id="deselectall" {{ $is_disabled }} title="Deselect All Jobs">@include('partials.icons.uncheck')</button>
		</div>
@section('contentfooter')			
	@include('partials.footer.padding')
@endsection				
	<script src="{{ asset('js/ajaxcalls.js') }}" rel='javascript'></script>	

		@endif
@else
@if ($q)	
	@include('partials.emptytable')
@endif
@endif


