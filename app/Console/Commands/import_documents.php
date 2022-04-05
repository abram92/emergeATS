<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_documents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:documents {type} {filename} {ext=xls} {deleteBefore=0} {delimiter=,}';

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
	
	  return (strlen($field) > 0) ? "'".str_replace("'", "''", $field)."'" : 'null';
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

		$valid = ['client'=>['class'=>'App\Client','fields'=>'clientid', 'host'=>'client', 'prefix'=>'clnt_'],
		'candidate'=>['class'=>'App\Candidate','fields'=>'candid', 'host'=>'cand', 'prefix'=>'c_'],
		'job'=>['class'=>'App\JobAd','fields'=>'jobid', 'host'=>'job', 'prefix'=>'jd_'],
		'email'=>['class'=>'App\LoggedEmail','fields'=>'emailid', 'host'=>'email', 'prefix'=>'ea_'],
		];
		
		$type = $this->argument('type');
		if(!isset($valid[$type]))
			return;
		$class = $valid[$type]['class'];
		$classcolumn = $valid[$type]['fields'];
		$classprefix = isset($valid[$type]['prefix']) ? $valid[$type]['prefix'] : '';
		$host = $valid[$type]['host'];

		$filename = $this->argument('filename');
		$ext = $this->argument('ext');
		$delimiter = $this->argument('delimiter');
		$deleteBefore = $this->argument('deleteBefore');
		
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

 //   $header = ['id', 'modelid', 'chunk'];
    $data = array();
	



		if ($ext == 'xls')
		   $reader = new Reader\Xls();
	    else
		   $reader = new Reader\Csv();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

		if ($deleteBefore) {
			$sql = "delete from data_files where datafileable_type = ".$this->formatStr($valid[$type]['class']).";";
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
			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
}
			

//			$rows[] = $cells;

		$sql = "insert into data_files(filename, filetype, size, host, location, datafileable_id, datafileable_type, created_at)
OVERRIDING SYSTEM VALUE VALUES ";
$folder = ceil($d['id']/5000);
$location = $folder."/".$classprefix.$d['id'];

if (is_numeric($d['filedate'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['filedate']);
				$filedate = $date->format('Y-m-d H:i:s');
} else 
	$filedate = $d['filedate'];

$extension = pathinfo($d['filename'], PATHINFO_EXTENSION);
if (!$extension)
$extension = "undefined";
 
$sql .= "(".$this->formatStr($d['filename']).","
			.$this->formatStr($extension).","
			.(int)$d['filesize'].","
			.$this->formatStr($host).","
			.$this->formatStr($location).","
			.$d[$classcolumn].","
			.$this->formatStr($valid[$type]['class']).","
			.$this->formatStr($filedate)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET filename = EXCLUDED.filename, filetype = EXCLUDED.filetype, size=EXCLUDED.size, host=EXCLUDED.host,
	 location = EXCLUDED.location, datafileable_id = EXCLUDED.datafileable_id, datafileable_type=EXCLUDED.datafileable_type, created_at=EXCLUDED.created_at;";
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
