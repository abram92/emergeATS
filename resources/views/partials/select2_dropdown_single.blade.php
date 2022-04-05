       <div class="form-label-group in-border row">
			    <select  @if(isset($fieldid)) id="{{ $fieldid }}" @endif class="@if(isset($select2_class)) {{ __($select2_class) }} @else select-select2 @endif form-inline" data-placeholder="{{ $fieldplaceholder }}" style="width:100%;" tabindex="4" name="{{ $fieldname }}" @if(isset($required) && ($required)) required @endif @if(isset($oninvalid)) oninvalid="this.setCustomValidity('{{ $oninvalid }}')"	oninput="this.setCustomValidity('')" @endif>
				    @if(!empty($options))
 						<option value="" class="text-muted"></option>
                     @foreach($options as $optionid => $optionvalue)
				    @if (is_array($optionvalue))
				       <option value="{{ $optionvalue['id'] }}" @if(($selectedid && ($optionvalue['id'] == $selectedid))  || (sizeof($options) == 1)) selected="selected" @endif @if (isset($optionvalue['colour_hex']) && $optionvalue['colour_hex']) style="background-color:{{ $optionvalue['colour_hex'] }}"  data-color="{{ $optionvalue['colour_hex'] }}" @endif>{{ $optionvalue['description'] }}</option>
					@else 
				       <option value="{{ $optionid }}" @if(($selectedid && ($optionid == $selectedid)) || (sizeof($options) == 1)) selected="selected" @endif >{{ $optionvalue }}</option>
				    @endif
                      @endforeach
                    @endif
				</select>
							{!! Form::label( $fieldname, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}
        </div>
