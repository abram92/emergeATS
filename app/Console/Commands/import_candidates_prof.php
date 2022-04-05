<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_candidates_prof extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cand_prof {filename} {delimiter=,}';

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
		$types = ContactFieldType::pluck('id', 'name')->all();
//dd($icons);
//exit;	   
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

/*		$header = ['id', 'username', 'password', 'firstname', 'lastname', 
				'telephone', 'faxnumber', 'mobilephone', 'email', 'visitcount', 
				'question_phrase', 'pass_phrase', 'subscribecode', 'ownerdomain', 'activeflag', 'emailsignature', 
				'id', 'addr1', 'addr2', 'town', 'county', 'country', 'zip', 'workphone', 'birthdate', 'IDNumber', 'licno', 'url', 
				'filename', 'filedate', 'filesize', 'fileupl', 'createdate', 'consultantid', 'duplicate', 
				'id', 'curlocation', 'location', 'qualification', 'availdate', 'availdescr', 'jobtype', 'jobrole', 
				'currency', 'carallowance', 'pension', 'medical', 'monthly', 'bonus', 'salary', 'perhourmin', 
				'perdaymin', 'sellme', 'textcv', 'interviewnotes', 'refcodes', 'candlevel', 'candrating', 
				'salarycat', 'eestatus', 'interviewed', 'agencynotes', 'jobtitletxt', 'qmcomments', 'idealjob', 'summary', 'qmstatus', 'refcodesmax'
		];
*/		
		$reader = new Reader\Xls();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
//		dd($spreadsheet->getSheetNames());
////		$worksheet = $spreadsheet->getSheetByName('CndProfInfo');
		$worksheet = $spreadsheet->getActiveSheet();

		$rows = [];
		foreach ($worksheet->getRowIterator() as $row) {
			$cellIterator = $row->getCellIterator();
			$cells = [];
			foreach ($cellIterator as $cell) {
				$cells[] = $cell->getValue();
			}
            if (!isset($header))
                $header = $cells;
             else {
				try {
                $d = array_combine($header, $cells);
				} catch (\Exception $e) {
					dd($cells);
				}
			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
				$d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
			}
			if (!$d['candlevel'])
				$d['candlevel'] = 3;
			if (!$d['candrating'])
				$d['candrating'] = 3;
			if (!$d['salarycat'])
				$d['salarycat'] = 3;
			$rows[] = $d;
//			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
			  $bool = $d['interviewed'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".$this->formatStr($d['curlocation']).",".$this->formatStr($d['jobrole']).",".$this->formatStr($d['jobtitletxt']).",".
			$this->formatStr($d['salary']).",".$this->formatStr($d['candlevel']).",".$this->formatStr($d['candrating']).",".$this->formatStr($d['salarycat']).",".
			$this->formatStr($d['eestatus']).",".$bool.", 5 )";
			}

//			$rows[] = $cells;
		}
//	dd($rows[0]);	

	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into candidates(id, current_location_id, jobtitle_id, jobtitle_text, salary, candidate_level_id, 
				candidate_rating_id, salary_category_id,
					ee_status_id, interviewed, consultant_id)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET current_location_id = EXCLUDED.current_location_id, jobtitle_id = EXCLUDED.jobtitle_id, jobtitle_text = EXCLUDED.jobtitle_text, 
	 salary = EXCLUDED.salary,
	 candidate_level_id = EXCLUDED.candidate_level_id, ee_status_id = EXCLUDED.ee_status_id, interviewed = EXCLUDED.interviewed;";
	 echo $sql;
	 
	 DB::statement($sql);
	 

		
		
		
		
    }
}
