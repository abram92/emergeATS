The consultant has NOT serviced the work there for you need to delegate the work to some one else.
@if (isset($data['jobsHotleads']))

HOT LEADS:
@foreach ($data['jobsHotleads'] as $j)
{{ __($j->jobref) }}
@endforeach	
@endif
@if (isset($data['jobsActive']))

ACTIVE JOBS:
@foreach ($data['jobsActive'] as $j)
{{ __($j->jobref) }}
@endforeach	
@endif
@if (isset($data['candidatesInprocess']))

IN PROCESS CANDIDATES:
@foreach ($data['candidatesInprocess'] as $j)
{{ __($j->user->listname) }}
@endforeach	
@endif
@if (isset($data['candidatesActive']))

ACTIVE CANDIDATES:
@foreach ($data['candidatesActive'] as $j)
{{ __($j->user->listname) }}
@endforeach	
@endif
@if (isset($data['jobsReminder']))

JOBS REMINDER:
@foreach ($data['jobsReminder'] as $j)
{{ __($j->jobref) }}
@endforeach	
@endif
@if (isset($data['candidatesReminder']))

CANDIDATES REMINDER:
@foreach ($data['candidatesReminder'] as $j)
{{ __($j->user->listname) }}
@endforeach	
@endif
