	<div class="border col-xs-12 col-sm-12 col-md-12 form-group">
@if ($field_body)	
        <div class="col-xs-12 col-sm-12 col-md-12" id="{{ $field }}heading">
             <label class="text-md-4">
				<button class="btn " type="button" data-toggle="collapse" data-target="#{{ $field }}body" aria-expanded="{{ $start_expanded }}" aria-controls="{{ $field }}body">
                 {{ $field_title }}
				</button>
             </label>
        </div>
        <div id="{{ $field }}body" class="collapse @if($start_expanded == 'true') show @endif" aria-labelledby="{{ $field }}heading" @if(isset($field_parent)) data-parent="#{{ $field_parent }}"@endif >
            <div class="col-xs-12 col-sm-12 col-md-12">
			<pre>{{ $field_body }}</pre>
            </div>
        </div>
@else
        <div class="text-muted" id="{{ $field }}heading">
			{{ $field_title }}
		</div>	
@endif	
    </div>