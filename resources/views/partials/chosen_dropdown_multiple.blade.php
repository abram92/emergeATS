    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group row">
							{!! Form::label($fieldname, $fieldlabel, array('class' => 'col-md-3 col-form-label text-md-right')) !!}
                           <div class="col-md-8">
			    <select class="chosen-select form-inline" data-placeholder="{{ $fieldplaceholder }}" style="width:100%;" tabindex="4" multiple name="{{ $fieldname }}">
				    @if(!empty($options))
                      @foreach($options as $optionid => $optionvalue)
				    @if (is_array($optionvalue))
				       <option value="{{ $optionvalue['id'] }}" @if(isset($selectedoptions) && isset($selectedoptions[$optionvalue['id']])) selected="selected" @endif @if (isset($optionvalue['colour_hex']) && $optionvalue['colour_hex'])) style="background-color:{{ $optionvalue['colour_hex'] }}" @endif>{{ $optionvalue['description'] }}</option>
					@else 
				       <option value="{{ $optionid }}" @if(isset($selectedoptions) && isset($selectedoptions[$optionid])) selected="selected" @endif >{{ $optionvalue }}</option>
				    @endif				  
                      @endforeach
                    @endif
				</select>
				</div>
        </div>
    </div>
