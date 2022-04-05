<?php

return [
    'static_status' => [
        'job_active' => '2',
        'job_hot_lead' => '126',
        'candidate_active' => '2',
        'candidate_inprocess' => '127'
    ],
	'trigger_days' => [
	    'job_hot_lead' => ['1'=>3, '2'=>5, '3'=>6],
		'job_active' => ['1'=>2, '2'=>4, '3'=>7],
	 	'candidate_inprocess' => ['1'=>2, '2'=>4, '3'=>6],
		'candidate_active' => ['1'=>2, '2'=>4, '3'=>6]
	],
	'emailvalidrules' => 'rfc',
];