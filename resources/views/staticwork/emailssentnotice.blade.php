@if ($newSent['toClient'] || $newSent['toCandidate'])
		<div class="alert alert-warning">
@switch($viewobj)
@case ('jobad')	
This job has been emailed to {{ $newSent['toClient'] }} candidates and {{ $newSent['toCandidate'] }} CVs have been sent to the client since the static work escalation.
@break
@case ('candidate')
This candidate has been emailed {{ $newSent['toCandidate'] }} job specs and {{ $newSent['toClient'] }} CVs have been sent to the client since the static work escalation.
@break
@endswitch
		</div>
@endif			
