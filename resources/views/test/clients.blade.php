@extends('layouts.tab')

@section('title', __( 'name'))

@section('content')

<div class="card card-header client sticky-top mb-1">

	<div class="card candidate-outline">
		<div class="card-header coloration job1 candidate2">
			<h4>Candidates Linked To Jobs</h4>
		</div>
		<div class="card-body">
				@include('partials.clients.jobapplication_summary_list_test',['tableid'=>'ja_summary', 'jobapplications'=>$paginated])
		</div>
	</div>
</div>


@include('partials.footer.padding')

@endsection 


@section('js')
@parent
					
<script>					
		$("document").ready(function() {
						
@include('scripts.ready_datatables')
@include('scripts.ready_datatables_group')
		

		$("#maindetail").css("margin-bottom", $("#buttonfooter").css("height"));

        $(window).scroll(function() {
            if ($("body").height() <= ($(window).height() + $(window).scrollTop()) || $(window).scrollTop() <= 70) {
                $(".consultant").show();
        $('.client').css('height', '100%' );
            }else {
                $(".consultant").hide();
        $('.client').css('height', '60%' );
            }
        });
		});					
</script>
					
@stop