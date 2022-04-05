							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="form-group">
									<div class="row">
										<div class="col-md-3 text-md-right contactinfoheader">
							{!! Form::label('contactTable', 'Contact Info', array('class' => 'ol-form-label')) !!}
										</div>
										<div class="col-md-9">							
							<div class="pl-2 text-sm bg-info ">Add New
				    @if(!empty($contact_types))
			          @foreach($contact_types as $rolename => $contact_type)

<button type="button" id="contactbtn_{{ $rolename }}" class="btn btn-sm btn-info contact-type-btn" data-toggle="tooltip" title="{{ $contact_type['name'] }}" ><i class="{{ $contact_type['fontawesome_icon'] }}"></i></button>

                      @endforeach
                    @endif					
</div>					
										</div>
									</div>
									<div class="row">
	<table class="table table-bordered table-striped datatable">
		<tbody id="contactTable">
@php $currcontacts = 	old('contacts', isset($contacts) ? $contacts : null)	 @endphp
        @if ($currcontacts)
		@foreach ($currcontacts as $key => $contact)
		    <tr id={{ $key }}>
				<td>
					@include('partials.contacts.contact_editrow', ['selectclass'=>'savedcontact'])
				</td>
			</tr>		
		@endforeach
		@endif
		</tbody>
		<tfoot>
		</tfoot>
	</table>
									</div>
									<div id="contactClone" style="display:none;">
							    @include('partials.contacts.contact_editrow',['contact'=>null, 'key'=>'key', 'selectclass'=>''])
									</div>
								</div>
							</div>