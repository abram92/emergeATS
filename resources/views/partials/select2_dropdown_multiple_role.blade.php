    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-label-group in-border row">
			    <select  @if(isset($fieldid)) id="{{ $fieldid }}" @endif class="@if(isset($select2_class)) {{ __($select2_class) }} @else select-select2 @endif form-inline" data-placeholder="{{ $fieldplaceholder }}" style="width:100%;" tabindex="4" multiple name="{{ $fieldname }}">
				    @if(!empty($options))
                      @foreach($options as $rolename => $rolecolour)
						<option value="{{ $rolename }}" @if(isset($userRole[$rolename])) selected="selected" @endif  @if ($rolecolour) style="background-color:{{ $rolecolour }}"   data-color="{{ $rolecolour }}"@endif>{{ $rolename }}</option>			  
                      @endforeach
                    @endif
				</select>
							{!! Form::label($fieldid, $fieldlabel, array('class' => 'col-form-label text-md-right')) !!}
				
        </div>
    </div>
