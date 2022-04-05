<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class map_event_entity_job_applications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:evententity_jobapplications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

	private function formatStr($field) {
	
	  return $field ? "'".str_replace("'", "''", $field)."'" : 'null';
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
		ini_set('memory_limit', '2048M');

/*		$valid = ['client'=>['class'=>'App\Client','fields'=>'clientid'],
		'candidate'=>['class'=>'App\Candidate','fields'=>'candidateid'],
		'job'=>['class'=>'App\JobAd','fields'=>'jobid'],
		];
*/
		$valid = ['1'=>['class'=>'App\Candidate','fields'=>'clientid'],
		'2'=>['class'=>'App\User','fields'=>'clientid'],
		'3'=>['class'=>'App\ClientContact','fields'=>'clientid'],
		'4'=>['class'=>'App\JobAd','fields'=>'clientid'],
		'5'=>['class'=>'App\LoggedEmail','fields'=>'clientid'],
		
		];
		
//		$type = $this->argument('type');
//		if(!isset($valid[$type]))
//			return;
//		$class = $valid[$type]['class'];
//		$classcolumn = $valid[$type]['fields'];
/*		select e1.event_id, e1.entityable_id as cand_id, 
e2.entityable_id as jobid from calendar_event_entities e1, 
calendar_event_entities e2 where e1.event_id = e2.event_id
and e1.entityable_type = 'App\Candidate' and 
e2.entityable_type = 'App\JobAd'
*/		
$res = DB::select("select e1.event_id, e1.entityable_id as cand_id, 
e2.entityable_id as jobid, a.id as jobapp_id from calendar_event_entities e1, 
calendar_event_entities e2, job_applications a where e1.event_id = e2.event_id
and e1.entityable_type = 'App\Candidate' and 
e2.entityable_type = 'App\JobAd'
and e1.entityable_id = a.candidate_id and e2.entityable_id = a.applicationable_id and a.applicationable_type = 'App\JobAd'");

    $data = array();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

			$sql = "delete from calendar_event_entities where entityable_type = 'App\JobApplication';";
			DB::statement($sql);
		
		foreach ($res as $row) {

		$sql = "insert into calendar_event_entities(event_id, entityable_id, entityable_type)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".$row->event_id.",".$row->jobapp_id.",'App\JobApplication' )";
/*$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET user_type = EXCLUDED.user_type, user_id = EXCLUDED.user_id, auditable_id=EXCLUDED.auditable_id, auditable_type=EXCLUDED.auditable_type,
	 new_values = EXCLUDED.new_values, created_at = EXCLUDED.created_at;";
*/	 echo $sql;
	 
	 DB::statement($sql);

		}
		
$res = DB::select("select e1.event_id, e1.entityable_id as cand_id, 
e2.entityable_id as jobid, a.id as jobapp_id from calendar_event_entities e1, 
calendar_event_entities e2, job_applications a where e1.event_id = e2.event_id
and e1.entityable_type = 'App\Candidate' and 
e2.entityable_type = 'App\Client'
and e1.entityable_id = a.candidate_id and e2.entityable_id = a.applicationable_id and a.applicationable_type = 'App\Client'");		
//	dd($rows);	

		foreach ($res as $row) {

		$sql = "insert into calendar_event_entities(event_id, entityable_id, entityable_type)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".$row->event_id.",".$row->jobapp_id.",'App\JobApplication' )";
/*$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET user_type = EXCLUDED.user_type, user_id = EXCLUDED.user_id, auditable_id=EXCLUDED.auditable_id, auditable_type=EXCLUDED.auditable_type,
	 new_values = EXCLUDED.new_values, created_at = EXCLUDED.created_at;";
*/	 echo $sql;
	 
	 DB::statement($sql);

		}
$success = true;

		} catch (\Exception $e) {
			dd($e);
			
		}


		if ($success) {		
			DB::commit();
		} else {
			DB::rollback();
		}

/*
	
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            else
                $d = array_combine($header, $row);
			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
}


        }
        fclose($handle);
    }
	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename); */
	 
    }
}
