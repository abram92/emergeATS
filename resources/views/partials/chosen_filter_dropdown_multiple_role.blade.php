	<div class="form-group">
		{!! Form::label($fieldname, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}
		<select @if(isset($fieldid)) id="{{ $fieldid }}" @endif class="chosen-select" data-placeholder="{{ $fieldplaceholder }}" style="width:100%;" tabindex="4" multiple name="{{ $fieldname }}">
			@if(!empty($options))
				@foreach($options as $optionid => $optionvalue)
				    @if (is_array($optionvalue))
				       <option value="{{ $optionvalue['id'] }}" @if(isset($selectedoptions) && in_array($optionvalue['id'], $selectedoptions)) selected="selected" @endif @if (isset($optionvalue['colour_hex']) && $optionvalue['colour_hex']) style="background-color:{{ $optionvalue['colour_hex'] }}" @endif>{{ $optionvalue['description'] }}</option>
					@else 
				       <option value="{{ $optionid }}" @if(isset($selectedoptions) && in_array($optionid, $selectedoptions)) selected="selected" @endif @if ($optionvalue) style="background-color:{{ $optionvalue }}" @endif>{{ $optionid }}</option>
				    @endif				  
				@endforeach
            @endif
		</select>
    </div>