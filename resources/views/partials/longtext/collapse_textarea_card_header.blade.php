<div class="card">
        <div class="card-heading card-header" id="{{ $field }}heading">
             <h4 class="mb-0">
				<button class="btn " type="button" data-toggle="collapse" data-target="#{{ $field }}body" aria-expanded="{{ $start_expanded }}" aria-controls="{{ $field }}body">
                 <label class="text-md-right" >{{ $field_title }}</label>
				</button>
             </h4>
        </div>
        <div id="{{ $field }}body" class="collapse @if($start_expanded == 'true') show @endif" aria-labelledby="{{ $field }}heading" @if(isset($field_parent)) data-parent="#{{ $field_parent }}"@endif >
            <div class="card-body">
			<pre>{{  $field_body  }}</pre>
            </div>
        </div>
</div>