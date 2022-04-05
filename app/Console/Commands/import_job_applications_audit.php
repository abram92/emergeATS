<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\JobApplicationStatus;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_job_applications_audit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:jobapplicationsaudit {type} {filename} {deleteBefore=0} {delimiter=,}';

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

	private function decodeField($field) {
		
		$str = $field ? rawurldecode($field) : '';
		$str = iconv('ISO-8859-1', 'UTF-8', $str);
	  return $str;
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

		$valid = ['client'=>['class'=>'App\Client','fields'=>'clientid'],
		'candidate'=>['class'=>'App\Candidate','fields'=>'candidateid'],
		'job'=>['class'=>'App\JobAd','fields'=>'jobid'],
		];
		
		$type = $this->argument('type');
		if(!isset($valid[$type]))
			return;
		$class = 'App\JobApplication'; //$valid[$type]['class'];
//		$classcolumn = $valid[$type]['fields'];
		
		$filename = $this->argument('filename');
		$deleteBefore = $this->argument('deleteBefore');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

 //   $header = ['id', 'modelid', 'chunk'];
    $data = array();
	




		$reader = new Reader\Csv();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

		if ($deleteBefore) {
			$sql = "delete from audits where auditable_type = '".$class."';";
			DB::statement($sql);
		}
		
		$status_codes = JobApplicationStatus::get()->pluck('id', 'system_code');
		
foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
            if (!isset($header))
                $header = $cells;
            else {
                $d = array_combine($header, $cells);
////			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
////    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
////}
	$newvalues = [];
	if (isset($d['activeflag']) && ($d['activeflag']))
		$newvalues['status_id'] = $d['activeflag'];
	if (isset($d['consultantid']) && ($d['consultantid']))
		$newvalues['consultant_id'] = $d['consultantid'];
		
	if (isset($d['activedate']) && ($d['activedate']))
		$newvalues['activated_at'] = $d['activedate'];
	if (isset($d['comments']) && ($d['comments']))
		$newvalues['comments'] = $this->decodeField($d['comments']);

 $json =  json_encode($newvalues);
//			$rows[] = $cells;

if (is_numeric($d['createdate'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['createdate']);
				$auditdate = $date->format('Y-m-d H:i:s');
} else 
	$auditdate = $d['createdate'];
$candidateid = isset($d['candid']) ? (int)$d['candid'] : (int)$d['userid'];

if ($type == 'job') {
	$applicationableid = (int)$d['jobid'];
}
else {
	$applicationableid = (int)$d['clientid'];
} 

$results = collect(DB::select( DB::raw("SELECT id from job_applications where candidate_id =  :var1 and applicationable_id = :var2 and applicationable_type=:var3"), array(
   'var1' => $candidateid, 'var2' =>$applicationableid, 'var3' => $valid[$type]['class'] /*, 'var3' => $auditdate */
 )));

$k = $d['status'];
//dd($results);
if($results->isNotEmpty() && isset($status_codes[$k])) { 
$statusid = $status_codes[$k];

	$newvalues = [];
		$newvalues['status_id'] = $statusid;
	if (isset($d['salary']) && ($d['salary']))
		$newvalues['salary'] = $d['salary'];
		
	if (isset($d['activedate']) && ($d['activedate']))
		$newvalues['activated_at'] = $d['activedate'];
	if (isset($d['comments']) && ($d['comments']))
		$newvalues['comments'] = $d['comments'];

 $json =  json_encode($newvalues);

		$sql = "insert into audits(user_type, user_id, event,auditable_id, auditable_type, new_values, created_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "('App\User',".(int)$d['actionuserid'].",'Edit',".(int)$results[0]->id.",'".$class."',".$this->formatStr($json).",".$this->formatStr($auditdate)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET user_type = EXCLUDED.user_type, user_id = EXCLUDED.user_id, auditable_id=EXCLUDED.auditable_id, auditable_type=EXCLUDED.auditable_type,
	 new_values = EXCLUDED.new_values, created_at = EXCLUDED.created_at;";
	 echo $sql;
	 
	 DB::statement($sql);
}
			}
		}
//	dd($rows);	

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
