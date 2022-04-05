						<div class="col-xs-12 col-sm-12 col-md-12">
 							{!! Form::label($ltfieldname, $ltfieldlabel, array('class' => 'col-form-label')) !!}
<br>
 								{!! Form::textarea($ltfieldname, old($ltfieldname, ($ltfieldmodel !== null) ? optional($ltfieldmodel)->chunk : ''), array('placeholder' => $ltfieldlabel,'class' => 'form-control', $ltrequired, '', 'autocomplete')) !!}

                                @error($ltfieldname)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
 						</div>