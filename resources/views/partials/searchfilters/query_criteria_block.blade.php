@if (isset($queryFilter) && !empty($queryFilter))
					<div class="alert alert-primary" role="alert">
						Query filter: @if(isset($canSave) && $canSave) @include('partials.searchfilters.savelink')	@endif
						@foreach($queryFilter as $k => $v)
						<div>{{ $k }}: {{ is_array($v) ? implode(';', $v) : $v }} </div>
						@endforeach
					</div>
@else					
					<div style="height:5px;">
					</div>
	
@endif