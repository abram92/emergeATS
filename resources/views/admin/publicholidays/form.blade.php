@section('content')

				<div class="container card-header"><h3>@if (isset($publicholiday->id)) {{ __('Edit') }} @else {{ __('Add') }} @endif {{ __('Public Holiday') }}</h3></div>

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">

				<div class="card-body">

@include('partials.flashmessages')

@if (isset($publicholiday->id))				
					{!! Form::model($publicholiday, ['method' => 'PATCH','route' => ['admin.publicholidays.update', $publicholiday->id]]) !!}
@else
					{!! Form::open(array('route' => 'admin.publicholidays.store','method'=>'POST')) !!}
@endif

						<div class="card card-body border-0">
						
@include('partials.form_date', ['fieldname'=>'holiday_date', 'fieldlabel'=>'Date', 'is_required'=>true])


@include('partials.form_text', ['fieldname'=>'description', 
								'fieldlabel'=>'Description', 
								'fielddefault'=> old('description', isset($publicholiday) ? $publicholiday->description : ""),
								'is_required'=>true, 
								'is_autofocus'=>true])		
					
						
@include('partials.form_checkbox', ['fieldname'=>'recurring', 
									'fieldlabel'=>'Recurring',
									'fielddefault'=>old('recurring', isset($publicholiday->recurring) ? $publicholiday->recurring : '')])	
									



                </div>
				
            </div>

	
    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-success">{{ __('Save') }}</button>
								<a href="{{ url(Request::segment(1)) }}" class="btn btn-primary">Back</a>
                            </div>
                        </div>
</div>
{!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('js')



    <script>
		
		
    </script>
@endsection