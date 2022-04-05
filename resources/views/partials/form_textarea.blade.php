@php $required__prop = (isset($is_required) && $is_required) ? 'required' : '' @endphp
@php $autofocus__prop = (isset($is_autofocus) && $is_autofocus) ? 'autofocus' : '' @endphp
@php $default__prop = (isset($fielddefault)) ? $fielddefault : null @endphp

                        <div class="form-label-group in-border row">

								{!! Form::textarea($fieldname, $default__prop, array('id'=>$fieldname, 'placeholder' => $fieldlabel,'class' => 'form-control', $required__prop, $autofocus__prop, 'autocomplete')) !!}
							{!! Form::label($fieldname, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}

                                @error($fieldname)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
