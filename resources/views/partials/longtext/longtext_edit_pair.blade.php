						<div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-label-group form-control-sm in-border row">

								{!! Form::textarea($ltfieldname, old($ltfieldname, ($ltfieldmodel !== null) ? optional($ltfieldmodel)->chunk : ''), array('id'=>$ltfieldname, 'placeholder' => $ltfieldlabel,'class' => 'form-control', $ltrequired, '', 'autocomplete')) !!}
							{!! Form::label($ltfieldname, $ltfieldlabel, array('class' => 'col-form-label text-md-right')) !!}

                                @error($ltfieldname)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
						</div>