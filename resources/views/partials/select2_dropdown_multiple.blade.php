@php $required__prop = (isset($is_required) && $is_required) ? true : false @endphp
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-label-group in-border row">
			    <select  @if(isset($fieldid)) id="{{ $fieldid }}" @endif class="@if(isset($select2_class)) {{ __($select2_class) }} @else select-select2 @endif form-inline" data-placeholder="{{ $fieldplaceholder }}" style="width:100%;" tabindex="4" multiple name="{{ $fieldname }}" @if($required__prop) required @endif>
				    @if(!empty($options))
                      @foreach($options as $optionid => $optionvalue)
				    @if (is_array($optionvalue))
				       <option value="{{ $optionvalue['id'] }}" @if(isset($selectedoptions) && isset($selectedoptions[$optionvalue['id']])) selected="selected" @endif @if (isset($optionvalue['colour_hex']) && $optionvalue['colour_hex']) style="background-color:{{ $optionvalue['colour_hex'] }}" data-color="{{ $optionvalue['colour_hex'] }}" @endif>{{ $optionvalue['description'] }}</option>
					@else 
				       <option value="{{ $optionid }}" @if(isset($selectedoptions) && ((is_array($selectedoptions) && in_array($optionid, $selectedoptions)) || isset($selectedoptions[$optionid]))) selected="selected" @endif >{{ $optionvalue }}</option>
				    @endif				  
                      @endforeach
                    @endif
				</select>
							{!! Form::label($fieldname, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}
				
        </div>
    </div>
