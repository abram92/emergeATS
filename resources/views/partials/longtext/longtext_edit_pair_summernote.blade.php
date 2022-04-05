                        <div class="form-label-group in-border row">

								{!! Form::textarea($ltfieldname, old($ltfieldname, ($ltfieldmodel !== null) ? optional($ltfieldmodel)->chunk : ''), array('id'=>$ltfieldname, 'placeholder' => $ltfieldlabel,'class' => 'form-control summernote', $ltrequired, '', 'autocomplete')) !!}
							{!! Form::label($ltfieldname, $ltfieldlabel, array('class' => 'col-form-label text-md-right')) !!}

                                @error($ltfieldname)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
