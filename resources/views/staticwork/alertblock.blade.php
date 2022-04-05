<div class="container col-md-12">
	<div class="card">
		<div class="card-title card-header {{ $alerttype }}" id="{{ $sw_cat }}heading" data-toggle="collapse" data-target="#{{ $sw_cat }}body" aria-expanded="true" aria-controls="{{ $sw_cat }}body">
             <h4 class="mb-0">
                 <label class="text-md-right" >{{ $title }}</label>
             </h4>
		
		</div>
		<div class="card-body" id="{{ $sw_cat }}body" class="collapse show" aria-labelledby="{{ $sw_cat }}heading">
		<small>
	<table class="table">
	@foreach ($alertLevels as $level => $levelColour) 
		<tr><th scope="row" class="{{ __($levelColour) }}">&nbsp;</th><td>{{ __($legend[$level]) }}</td></tr>
	@endforeach
	</table>
<div class="card-group">
	@foreach ($alertLevels as $k => $levelColour) 
<div class="card">


		@if (isset($arr[$k])) 
			@foreach ($arr[$k] as $current_row => $v1) 
				@php $bg_color = ( $current_row % 2 ) ? "diveven" :  "divodd"; @endphp
				@php $md = $v1['model']; @endphp
				<div class="alertdiv {{ __($bg_color) }} alertdiv{{ $k }}">
					@if ($alerttype == 'candidate')
				<a href="{{ route('candidates.show',$md->id) }}" data-toggle="tooltip" title="Show" target="candidate{{ $md->id }}" class="btn btn-sm text-info fas fa-info"></a>
@endif
					@if ($alerttype == 'job')
		<a href="{{ route('jobs.show',$md->id) }}" data-toggle="tooltip" title="Show" target="jobad{{ $md->id }}"class="btn btn-sm text-info fas fa-info"></a>
@endif
				@if (isset($md->created_at))
					<span class="uploaddate @if ($v1['is_final']) {{ __('finalalert') }} @endif">{{\Carbon\Carbon::parse( $md->created_at )->format('j F Y') }}</span>
				@endif
					@if ($alerttype == 'candidate')
		{{ $md->user->listname }}<br>
@endif
					@if ($alerttype == 'job')
		{{ $md->jobref }}<br>
@endif
				@if (isset($md->client))
					{{ __(  " ".$md->client->name ) }}
				@endif
				@if ((isset($md->jobtitle_text)) && ($md->jobtitle_text))
					{{ __(  " (".$md->jobtitle_text.")" ) }}
				@endif
				@if (isset($md->jobdescription))
					<br>{{ __(  $md->jobdescription ) }}
				@endif

				</div>
			@endforeach
		@endif


</div>
@endforeach
</div>
</small>
		</div>
	</div>
</div>

