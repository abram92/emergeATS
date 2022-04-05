							<label class="col-form-label text-md-right col-md-2 text-dark">{{ $fieldlabel }}</label>
							
							<div class="form-label-group in-border col-md-5">

								{!! Form::date($fieldname_from, isset($fieldvalue_from) ? $fieldvalue_from : null, array('placeholder' => '','class' => 'form-control ', '', isset($fieldname_from_required) ? 'required' : '', 'autocomplete')) !!}
							{!! Form::label($fieldname_from, 'From', array()) !!}
							</div>
							<div class="form-label-group in-border col-md-5">
							
								{!! Form::date($fieldname_to, isset($fieldvalue_to) ? $fieldvalue_to : null, array('placeholder' => '','class' => 'form-control', '', isset($fieldname_to_required) ? 'required' : '', 'autocomplete')) !!}
															
							{!! Form::label($fieldname_to, 'To', array()) !!}
							</div>
