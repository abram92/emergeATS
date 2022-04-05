<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_emails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:emails {filename} {deleteBefore=0} {delimiter=,}';

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

		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		$deleteBefore = $this->argument('deleteBefore');
		
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
			$sql = "delete from logged_emails;";
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

		$sql = "insert into logged_emails(id, date, address_from, address_to, address_cc, subject, body)
OVERRIDING SYSTEM VALUE VALUES ";

if (is_numeric($d['timestamp'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['timestamp']);
				$filedate = $date->format('Y-m-d H:i:s');
} else 
	$filedate = $d['timestamp'];

$subject = $d['subject'] ? $d['subject'] : " "; 
$messagebody = $d['messagebody'] ? $d['messagebody'] : " "; 

$sql .= "(".$this->formatStr($d['id']).","
			.$this->formatStr($filedate).","
			.$this->formatStr($d['consultantid']).","
			.$this->formatStr($d['toEmail']).","
			.$this->formatStr($d['ccEmail']).","
			.$this->formatStr($subject).","
			.$this->formatStr($messagebody)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET date = EXCLUDED.date, address_from = EXCLUDED.address_from, address_to=EXCLUDED.address_to, address_cc=EXCLUDED.address_cc,
	 subject = EXCLUDED.subject, body = EXCLUDED.body;";
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
