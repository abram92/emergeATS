<div class="card-heading">
<label class="col-form-label">{{ __('Salary') }}</label>
</div>
<div class="card-body">
                        <div class="form-group row">
							{!! Form::label('salary', 'Gross Salary', array('class' => 'col-md-3 col-form-label text-md-right')) !!}

                            <div class="col-md-8">
								{!! Form::number('salary', old('salary', optional($candidate)->salary ), array('min'=>0, 'placeholder' => 'Gross Salary','class' => 'form-control', '', '', 'autocomplete')) !!}

                            </div>
                        </div>
</div>