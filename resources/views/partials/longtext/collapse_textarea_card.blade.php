<div class="card ">
@if (!strlen($field_body))
	<div class="card-header text-muted mb-0 bg-light pt-0 pb-0">
		<label class="text-md-right" >{{ $field_title }}</label> @if (isset($edit_lt) && ($edit_lt))
			<a href="{{ $edit_lt_route }}" data-toggle="tooltip" title="Edit {{ $field_title }}" class="btn btn-sm text-info fas fa-edit actionbtn" target="{{ $edit_lt_target }}"></a>
		@endif
	</div>
@else
        <div class="card-header card-title text-light bg-dark pt-1 pb-1" id="{{ $field }}heading" data-toggle="collapse" data-target="#{{ $field }}body" aria-expanded="{{ $start_expanded }}" aria-controls="{{ $field }}body">
             <h5 class="mb-0">
                 <label class="text-md-right" >{{ $field_title }}</label> @if (isset($edit_lt) && ($edit_lt))
			<a href="{{ $edit_lt_route }}" data-toggle="tooltip" title="Edit {{ $field_title }}" class="btn btn-sm text-info fas fa-edit actionbtn" target="{{ $edit_lt_target }}"></a>
@endif
             </h5>
        </div>
        <div id="{{ $field }}body" class="collapse @if($start_expanded == 'true') show @endif" aria-labelledby="{{ $field }}heading" @if(isset($field_parent)) data-parent="#{{ $field_parent }}"@endif >
		@if(isset($is_html) && ($is_html))
			<div id="{{ $field }}pre" class="px-2 card-text @if (isset($field_min) && $field_min) p1 @endif">{!!  $field_body  !!}</div>
		@else	
			<pre id="{{ $field }}pre" class="px-2 card-text @if (isset($field_min) && $field_min) p1 @endif">{{  $field_body  }}</pre>
		@endif
        </div>
@endif		
</div>

