<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_static_work_alert_jobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:static_work_alert_jobs {filename} {deleteBefore=0} {delimiter=,}';

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
//		$type = $this->argument('type');
//		if(!isset($valid[$type]))
//			return;
//		$class = $valid[$type]['class'];
//		$classcolumn = $valid[$type]['fields'];
		
		$filename = $this->argument('filename');
		$deleteBefore = $this->argument('deleteBefore');
		$delimiter = $this->argument('delimiter');
		if (!file_exists($filename) || !is_readable($filename))
			return false;

 //   $header = ['id', 'modelid', 'chunk'];
    $data = array();
	




		$reader = new Reader\Xls();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

		if ($deleteBefore) {
			$sql = "delete from static_work_alert_job_ads;"; // where auditable_type = ".$this->formatStr($valid[$type]['class']).";";
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
////			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
////    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
////}
//			$rows[] = $cells;


		$sql = "insert into static_work_alert_job_ads(alert_id, job_ad_id, alert_level, level_ordinal, status_id)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".(int)$d['alertid'].",".(int)$d['jobid'].",".(int)$d['alertlevel'].",".(int)$d['levelordinal'].",".(int)$d['statusid']." )";
	 echo $sql;
	 
	 DB::statement($sql);

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
