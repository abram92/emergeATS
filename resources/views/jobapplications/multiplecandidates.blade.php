@extends('layouts.admin')


@section('content')

<div class="container col-md-12">
@include('partials.flashmessages')

	<div class="card">
		<div class="card-header job">
			<div class="row">
				<div class="col-md-8">
				@if ($jobad)
					<h5>Selected Job</h5>
					{{ $jobad->jobref }}
				@endif
				</div>
			</div>
			<div class="row container-fluid">
				<div class="col-md-8">

				 </div>
			</div>
		</div>
	</div>

@if (isset($success) && !empty($success))
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-8">
					<h5>Candidates Successfully Linked</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
@foreach ($success as $cand)			
			<div class="row">
				<div class="col-md-8">
					<h5>{{ $cand->user->listname }}</h5>
				</div>
			</div>
@endforeach

		</div>
	</div>
@endif

@if (isset($fail) && !empty($fail))
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-8">
					<h5>Candidates Already Linked</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
@foreach ($fail as $cand)			
			<div class="row">
				<div class="col-md-8">
					<h5>{{ $cand->user->listname }}</h5>
				</div>
			</div>
@endforeach

		</div>
	</div>
@endif

</div>

@endsection 
