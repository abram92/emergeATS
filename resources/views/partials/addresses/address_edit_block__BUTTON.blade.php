							<div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
							{!! Form::label('', 'Address Info', array('class' => 'col-md-3 col-form-label text-md-right')) !!}

                            <div class="col-md-8">

	<table class="table table-bordered table-striped datatable">
		<thead class="table-dark">
			<tr>
				<th><input type="button" id="addressbtn" class="btn btn-xs btn-info fa fa-plus float-right" value="Add New"></th>
			</tr>
		</thead>
		<tbody id="addressTable">
        @if (isset($addresses))
		@foreach ($addresses as $key => $address)
		    <tr id={{ $key }}>
				<td>
					@include('partials.addresses.address_editrow')
				</td>
			</tr>		
		@endforeach
		@endif
		</tbody>
		<tfoot>
		</tfoot>
	</table>
								<div id="addressClone" style="display:none;">
							    @include('partials.addresses.address_editrow',['address'=>null, 'key'=>'key'])
							</div>
                            </div>
                        </div>
						</div>
