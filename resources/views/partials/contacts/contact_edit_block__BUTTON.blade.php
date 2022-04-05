							<div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
							{!! Form::label('', 'Contact Info', array('class' => 'col-md-3 col-form-label text-md-right')) !!}

                            <div class="col-md-8">

	<table class="table table-bordered table-striped datatable">
		<thead>
			<tr>
				<th><input type="button" id="contactbtn" class="btn btn-xs btn-info fa fa-plus float-right" value="Add New"></th>
			</tr>
		</thead>
		<tbody id="contactTable">
        @if (isset($contacts))
		@foreach ($contacts as $key => $contact)
		    <tr id={{ $key }}>
				<td>
					@include('partials.contacts.contact_editrow')
				</td>
			</tr>		
		@endforeach
		@endif
		</tbody>
		<tfoot>
		</tfoot>
	</table>
								<div id="contactClone" style="display:none;">
							    @include('partials.contacts.contact_editrow',['contact'=>null, 'key'=>'key'])
							</div>
                            </div>
                        </div>
						</div>