<tr id={{ $key }}>
	<td>
		<div class="row">
			<div class="col-md-4 {{ $contact->contactFieldType->fontawesome_icon }}" data-toggle="tooltip" title="{{ $contact->contactFieldType->name }}">
			
			</div>
			<div class="input-group-append col-md-8">
				{{ $contact->data }}
				</div>	
		</div>
	</td>
</tr>