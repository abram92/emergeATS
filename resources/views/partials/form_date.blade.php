@php $required__prop = (isset($is_required) && $is_required) ? 'required' : '' @endphp
                        <div class="form-label-group in-border row">

								{!! Form::date($fieldname, null, array('id'=>$fieldname, 'placeholder' => '','class' => 'form-control', $required__prop, '', 'autocomplete')) !!}
							{!! Form::label($fieldname, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}

                                @error($fieldname)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
