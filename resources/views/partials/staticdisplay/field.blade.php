		<div class="row small @if (isset($promptclass)) {{ $promptclass }} @else static-prompt @endif">{{ __($fieldprompt) }}</div>
		@if (array_key_exists('fieldvalue', get_defined_vars()))
		<div class="row static-value @if(isset($is_trashed) && $is_trashed) text-muted @endif" @if (isset($fieldid)) id="{{ $fieldid }}" @endif>{{ $fieldvalue ? __($fieldvalue) : '-' }}</div>		
		@endif
		@if (isset($fieldstatus))		
		<div class="row static-value" @if (isset($fieldid)) id="{{ $fieldid }}" @endif>@include('partials.show_status', ['status'=>$fieldstatus])</div>
		@endif
		@if (isset($fielddate))		
		<div class="row static-value" @if (isset($fieldid)) id="${{ $fieldid }}" @endif>@include('partials.list_date_format',['dt'=>$fielddate])</div>
		@endif
		@if (isset($fieldconsultant))		
		<div class="row static-value" @if (isset($fieldid)) id="{{ $fieldid }}" @endif>@include('partials.list_consultant',['cons'=>$fieldconsultant])</div>
		@endif
		@if (isset($fieldfulltext))		
		<div class="row border static-value p-1" @if (isset($fieldid)) id="{{ $fieldid }}" @endif><pre class="wraptext">{{ $fieldfulltext }}</pre></div>
		@endif
		@if (isset($fieldhtml))
		<div class="col-12 mx-n3 border static-value" @if (isset($fieldid)) id="{{ $fieldid }}" @endif>{!! $fieldhtml !!}</div>		
		@endif	
		@if (isset($fieldspan))
		<span class="static-value" @if (isset($fieldid)) id="{{ $fieldid }}" @endif @if (isset($fieldname)) id="{{ $fieldname }}" @endif>{!! $fieldspan !!}</span>		
		@endif	
		@if (isset($fieldhighlight))
        <div class="row static-value" @if (isset($fieldid)) id="{{ $fieldid }}" @endif>		
			@if ($fieldhighlight)
				<label class="badge" style="background-color:{{ $fieldhighlight }}">&nbsp;&nbsp;</label>&nbsp;{{ $fieldhighlight }}
			@else
				{{ __('No Highlight') }}
			@endif
        </div>		
		@endif