<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Http\Traits\SearchTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_longtext extends Command
{
	use SearchTrait;
	
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:longtext {type} {fieldname} {filename} {delete=0} {delimiter=,} ';

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
//		$str = str_replace(" ?", " ", $str);
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
		'job'=>['class'=>'App\JobAd','fields'=>['cvsendinstructions', 'summary', 'agencynotes', 'skills', 'technicalarea', 'fulldescription', 'projectplan']],
		'jobapplication'=>['class'=>'App\JobApplication','fields'=>['comments']],
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
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
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
			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
}
			
//			$rows[] = $cells;
if ($d['chunk'] !== null) {
	
	    $decodedField = $this->decodeField($d['chunk']);
	$searchTrans = $this->FTS_mapSpecialChars($decodedField);

if (!isset($d['editor_id'])) {
				$editorid = '';
} else 
	$editorid = $d['editor_id'];

if (isset($d['created_at'])) {
if (is_numeric($d['created_at'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['created_at']);
				$createdat = $date->format('Y-m-d H:i:s');
} else 
	$createdat = $d['created_at'];
} else {
	$createdat = '';
}
		$sql = "insert into long_full_texts(id, chunk, field_type, longtextable_id, longtextable_type, search_transl, editor_id, created_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "(".(int)$d['id'].",".$this->formatStr($decodedField).",".$this->formatStr($fieldname).",".(int)$d['modelid'].",".$this->formatStr($valid[$type]['class']).",".$this->formatStr($searchTrans).",".$this->formatStr($editorid).",".$this->formatStr($createdat)." )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET chunk = EXCLUDED.chunk, field_type = EXCLUDED.field_type, longtextable_id=EXCLUDED.longtextable_id, longtextable_type=EXCLUDED.longtextable_type,
	 search_transl = EXCLUDED.search_transl, editor_id=EXCLUDED.editor_id, created_at=EXCLUDED.created_at;";
	 echo $sql;
	 
	 DB::unprepared($sql);
	 
	 
/*		$sql = "insert into long_full_texts(id, chunk, field_type, longtextable_id, longtextable_type, search_transl, editor_id, created_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= "( :id,chunk, :field_type, :longtextable_id, :longtextable_type, :search_transl, :editor_id, :created_at )";
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET chunk = EXCLUDED.chunk, field_type = EXCLUDED.field_type, longtextable_id=EXCLUDED.longtextable_id, longtextable_type=EXCLUDED.longtextable_type,
	 search_transl = EXCLUDED.search_transl, editor_id=EXCLUDED.editor_id, created_at=EXCLUDED.created_at;";
	 echo $sql;
	 
	 $stmt = DB::statement($sql);	 
	 $stmt->bindParam(':id', (int)$d['id']);
     $stmt->bindParam(':chunk', $decodedField);
     $stmt->bindParam(':field_type', $fieldname);
     $stmt->bindParam(':longtextable_id', (int)$d['modelid']);
     $stmt->bindParam(':longtextable_type', $valid[$type]['class']);
     $stmt->bindParam(':search_transl', $searchTrans);
     $stmt->bindParam(':editor_id', $editorid);
     $stmt->bindParam(':created_at', $createdat);

     $stmt->execute(); */
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
