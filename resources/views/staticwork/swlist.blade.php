@section('content')


@php
	$legend = array();
	$legend['hotleads'] = array('1'=>'Please convert me or you are going to lose me. its been 3 days and you haven\'t called the client yet.',
								'2'=>'This is your last chance as you still haven\'t contacted the client. Please convert ME in the next 24 business hours or this HOT LEAD will be passed onto someone who\'s motivated to secure new work',
								'3'=>'You\'ve chosen to snooze by not converting the lead therefore this lead will be passed onto someone else for converting.'
								);
	$legend['activejobs'] = array('1'=>'Please send CV\'s to this JOB ASAP as you have made the job active.',
								'2'=>'This is your last chance to service this work so please send CV\'s to the client for consideration in the next 72 business hours or this JOB will be passed onto someone who\'s motivated to service the work effectively',
								'3'=>'You\'ve chosen to snooze by not sending CV\'s to your JOB, therefore, you lose it and the work will be passed onto someone motivated to service the work'
								);
	$legend['inprocesscandidates'] = array('1'=>'Please finalise this CANDIDATE\'s e-Merge ASAP as you have made the in-process 2 days back',
								'2'=>'This is your last chance to finalise this candidates CV and send the CV out. Please ensure you do this within the next 48 hours otherwise the candidate will be passed on to a consultant who\'s more motivated to finalise the candidate\'s application.',
								'3'=>'You\'ve chosen to snooze by not finalising the candidates e-Merge CV therefore the candidate will be passed onto someone who\'s motivated to finalise the candidates application.'
								);
	$legend['activecandidates'] = array('1'=>'Please send this CANDIDATE\'s CV to jobs ASAP as you have made the candidate active',
								'2'=>'This is your last chance to introduce this candidate to work you have on the system and please do so within the next 48 hours otherwise the candidate will be passed on to a consultant who\'s more motivated to generate the candidate interviews',
								'3'=>'You\'ve chosen to snooze by not sending your CANDIDATE to work therefore the candidate will be passed onto someone who\'s motivated to send the candidate out'
								);
	$alertLevels = ['1'=>'alertcol1', '2'=>'alertcol2', '3'=>'alertcol3'];
@endphp


<div class="modal fade" id="staticWork" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
<div class="modal-content"> 
 <div class="modal-header">
                <h5 class="modal-title" id="staticWorkTitle">Static Work</h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
        <div class="modal-body">


@if (!empty($arrLeads))
@include('staticwork.alertblock', ['title'=>'Hot Leads', 'sw_cat'=>'job1', 'alerttype'=>'job', 'legend'=>$legend['hotleads'], 'arr'=>$arrLeads, 'alertLevels'=>$alertLevels])
@endif
@if (!empty($arrActiveJobs))
@include('staticwork.alertblock', ['title'=>'Active Jobs', 'sw_cat'=>'job2',  'alerttype'=>'job','legend'=>$legend['activejobs'], 'arr'=>$arrActiveJobs, 'alertLevels'=>$alertLevels])
@endif
@if (!empty($arrInprocessCandidates))
@include('staticwork.alertblock', ['title'=>'In Process Candidates', 'sw_cat'=>'cand1', 'alerttype'=>'candidate', 'legend'=>$legend['inprocesscandidates'], 'arr'=>$arrInprocessCandidates, 'alertLevels'=>$alertLevels])
@endif
@if (!empty($arrActiveCandidates))
@include('staticwork.alertblock', ['title'=>'Active Candidates', 'sw_cat'=>'cand2', 'alerttype'=>'candidate', 'legend'=>$legend['activecandidates'], 'arr'=>$arrActiveCandidates, 'alertLevels'=>$alertLevels])
@endif
</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
	</div>
</div>
</div>
</div>

@section('js')

    <script>
	
		$("document").ready(function() {
@if ($staticalerts_new)
        $("#staticWork").modal('show');
@endif			
					
		});
		
		
    </script>
@endsection	
