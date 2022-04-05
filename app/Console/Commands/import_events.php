<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_events extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:event {filename} {deleteBefore=0} {delimiter=,}';

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
			$sql = "delete from calendar_events;"; // where auditable_type = ".$this->formatStr($valid[$type]['class']).";";
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

if (is_numeric($d['createdate'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['createdate']);
				$createdate = $date->format('Y-m-d H:i:s');
} else 
	$createdate = $d['createdate'];

		$sql = "insert into calendar_events(id, type_id, user_id,created_user_id, title, time_start, time_end, created_at, comments, is_all_day)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".(int)$d['id'].",".(int)$d['typeid'].",".(int)$d['respuser'].",".(int)$d['createuser'].",".$this->formatStr($d['description']).",".$this->formatStr($createdate).",".$this->formatStr($createdate).",".$this->formatStr($createdate).",".$this->formatStr($d['comment']).", 'f' )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET type_id = EXCLUDED.type_id, user_id = EXCLUDED.user_id, created_user_id=EXCLUDED.created_user_id, title=EXCLUDED.title,
	 time_start = EXCLUDED.time_start, time_end = EXCLUDED.time_end, created_at = EXCLUDED.created_at, comments = EXCLUDED.comments;";
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
