@extends('layouts.admin')


@section('content')

<div class="container col-md-12">
@include('partials.flashmessages')

	<div class="card">
		<div class="card-header job">
			<div class="row">
				<div class="col-md-8">
				@if ($candidate)
					<h5>Selected Candidate</h5>
					{{ $candidate->user->listname }}
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
					<h5>Jobs Successfully Linked</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
@foreach ($success as $jobad)			
			<div class="row">
				<div class="col-md-8">
					<h5>{{ $jobad->jobref }}</h5>
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
					<h5>Jobs Already Linked</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
@foreach ($fail as $jobad)			
			<div class="row">
				<div class="col-md-8">
					<h5>{{ $jobad->jobref }}</h5>
				</div>
			</div>
@endforeach

		</div>
	</div>
@endif

</div>

@endsection 
