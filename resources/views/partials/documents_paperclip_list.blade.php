				<div class="dropdown btn btn-sm">
					<a href="#" class="dropdown-toggle @if(isset($paperclipcolor)) {{ $paperclipcolor}} @else text-secondary @endif fa  fa-paperclip" data-toggle="dropdown" title="Show Documents" id="attachments{{ $documentsid }}"></a>
					<div  class="dropdown-content doclist {{ isset($dropdownAlign) ? $dropdownAlign : '' }}" aria-labelledby="attachments{{ $documentsid }}">
					@foreach ($documents as $attachment)
						<a style="max-width: 100%;" class="{{ $loop->even ? 'even' : 'odd' }}" href="{{ url('/file/'.$attachment['id'].'_'.$doctype.'_'.$documentsid.'/download') }}">{{ $attachment->filename }} <div class="text-right"><small>{{ \Carbon\Carbon::parse($attachment->created_at)->format('j F, Y') }}</small></div></a>
					@endforeach
					</div>
				</div>					