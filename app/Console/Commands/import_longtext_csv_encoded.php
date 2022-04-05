<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_longtext_csv_encoded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:longtext_csv_encoded {fieldname} {type} {filename} {delimiter=,} {delete=1}';

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

		$valid = ['client'=>['class'=>'App\Client','fields'=>['techenvironment', 'agencynotes']],
		'candidate'=>['class'=>'App\Candidate','fields'=>['sellme', 'textcv', 'interviewnotes', 'agencynotes', 'idealjob', 'summary']],
		'job'=>['class'=>'App\JobAd','fields'=>['cvsendinstructions', 'summary', 'agencynotes', 'skills', 'technicalarea', 'fulldescription']]
		];
		$fieldname = $this->argument('fieldname');
		$type = $this->argument('type');
		if(!isset($valid[$type]))
			return;
		if(!in_array($fieldname, $valid[$type]['fields']))
			return;
		
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

 //   $header = ['id', 'modelid', 'chunk'];
    $data = array();
	




		$reader = new Reader\Csv();
		$reader->setReadDataOnly(true);
		var_dump($reader->getInputEncoding());
		$reader->setInputEncoding('CP1252');
		$reader->setEnclosure('');
		$reader->setSheetIndex(0);		
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		var_dump($reader->getInputEncoding());
//exit;		
		$data = [];
		$success = false;
		DB::beginTransaction();
		try {

			$doDelete = $this->argument('delete');
			if ($doDelete) {
				$sql = "delete from long_full_texts where field_type = ".$this->formatStr($type)." and longtextable_type = ".$this->formatStr($valid[$type]['class']).";";
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

if ($d['chunk'] !== null) {
		$sql = "insert into long_full_texts(id, chunk, field_type, longtextable_id, longtextable_type, search_transl)
OVERRIDING SYSTEM VALUE VALUES ";
    $decodedField = $this->decodeField($d['chunk']);
	$searchTrans = $decodedField;
$sql .= "(".(int)$d['id'].",".$this->formatStr($decodedField).",".$this->formatStr($fieldname).",".(int)$d['modelid'].",".$this->formatStr($valid[$type]['class']).", ".$this->formatStr($decodedField)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET chunk = EXCLUDED.chunk, field_type = EXCLUDED.field_type, longtextable_id=EXCLUDED.longtextable_id, longtextable_type=EXCLUDED.longtextable_type;";
//	 echo $sql;
//	 var_dump($sql);
//	 exit;
	 DB::statement($sql);
}

			}
		}		
		
	/*	
		
		    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 10000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            else {
				try {
                $d = array_combine($header, $row);
				} catch (\Exception $e) {
					dd($row);
				}
				if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
					$d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
				}
				if (is_int($d['id'])){
//				$data[] = $d;
if ($d['chunk'] !== null) {
		$sql = "insert into long_full_texts(id, chunk, field_type, longtextable_id, longtextable_type)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".(int)$d['id'].",".$this->formatStr($this->decodeField($d['chunk'])).",".$this->formatStr($fieldname).",".(int)$d['modelid'].",".$this->formatStr($valid[$type]['class'])." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET chunk = EXCLUDED.chunk, field_type = EXCLUDED.field_type, longtextable_id=EXCLUDED.longtextable_id, longtextable_type=EXCLUDED.longtextable_type;";
	 echo $sql;
	 var_dump($sql);
	 exit;
	 DB::statement($sql);
}		
			}
			}
		}
        fclose($handle);
    }
	if (empty($data))
		return;
		*/
		

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
 
    }
}
