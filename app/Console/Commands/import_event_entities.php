<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_event_entities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:evententity {filename} {deleteBefore=0} {delimiter=,}';

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
		$valid = ['1'=>['class'=>'App\Candidate'],
		'2'=>['class'=>'App\User'],
		'3'=>['class'=>'App\ClientContact'],
		'4'=>['class'=>'App\JobAd'],
		'5'=>['class'=>'App\LoggedEmail'],
		'6'=>['class'=>'App\Client'],
		
		];
		
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
	




		$reader = new Reader\Csv();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

		if ($deleteBefore) {
			$sql = "delete from calendar_event_entities"; // where auditable_type = ".$this->formatStr($valid[$type]['class']).";";
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


if (isset($valid[$d['entitytypeid']])) {
$entitytype = $valid[$d['entitytypeid']];
} else {
	dd($d['entitytypeid']);
	continue;
}
		$sql = "insert into calendar_event_entities(event_id, entityable_id, entityable_type)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".(int)$d['eventid'].",".(int)$d['entityid'].",'".$entitytype['class']."' )";
/*$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET user_type = EXCLUDED.user_type, user_id = EXCLUDED.user_id, auditable_id=EXCLUDED.auditable_id, auditable_type=EXCLUDED.auditable_type,
	 new_values = EXCLUDED.new_values, created_at = EXCLUDED.created_at;";
*/	 echo $sql;
	 
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
