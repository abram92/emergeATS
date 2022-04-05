<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\JobApplicationStatus;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_job_applications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:jobapplications {type} {filename} {deleteBefore=0} {delimiter=,}';

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

		$valid = ['client'=>['class'=>'App\Client'],
		'job'=>['class'=>'App\JobAd']
		];
		
		$filename = $this->argument('filename');
		$type = $this->argument('type');
		if(!isset($valid[$type]))
			return;		
		$deleteBefore = $this->argument('deleteBefore');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

 //   $header = ['id', 'modelid', 'chunk'];
    $data = array();
	



		$status_codes = JobApplicationStatus::get()->pluck('id', 'system_code');

		$reader = new Reader\Csv();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

if ($deleteBefore) {
$sql = "delete from job_applications where applicationable_type = ".$this->formatStr($valid[$type]['class']).";";
	 DB::statement($sql);
}
		
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
//			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
//    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
//}
			

//			$rows[] = $cells;

$results = DB::select( DB::raw("SELECT count(*) from users where id =  :somevariable"), array(
   'somevariable' => (int)$d['userid'],
 ));

if ($type == 'job') {
	$applicationableid = (int)$d['jobid'];
$results1 = DB::select( DB::raw("SELECT count(*) from job_ads where id =  :somevariable"), array(
   'somevariable' => $applicationableid,
 ));
}
else {
	$applicationableid = (int)$d['clientid'];
$results1 = DB::select( DB::raw("SELECT count(*) from clients where id =  :somevariable"), array(
   'somevariable' => $applicationableid,
 )); 
}
$k = $d['status'];

if($results[0]->count && $results1[0]->count && isset($status_codes[$k])) { 
$statusid = $status_codes[$k];

		$decodedField = $this->decodeField($d['comments']);
		$sql = "insert into job_applications(applicationable_id, applicationable_type, candidate_id, status_id, created_at, comments)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".$applicationableid.", ".$this->formatStr($valid[$type]['class']).", ".(int)$d['userid'].",".(int)$statusid.",".$this->formatStr($d['createdate']).",".$this->formatStr($decodedField)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET applicationable_id = EXCLUDED.applicationable_id, applicationable_type = EXCLUDED.applicationable_type, candidate_id = EXCLUDED.candidate_id, status_id=EXCLUDED.status_id, created_at=EXCLUDED.created_at, comments = EXCLUDED.comments;";
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
