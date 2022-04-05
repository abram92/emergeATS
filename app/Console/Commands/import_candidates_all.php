<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_candidates_all extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cand_all {filename} {delimiter=,}';

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
////		$worksheet = $spreadsheet->getSheetByName('CndPersInfo');
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
			if ($d['birthdate']) {
//				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['birthdate']);
//				$birthdate = $date->format('Y-m-d');
				$birthdate = substr($d['birthdate'], 0, 23);
			} else 
				$birthdate = null;
			if ($d['createdate']) {
//				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(substr($d['createdate'], 0, 21));
//				$createdate = $date->format('Y-m-d');
				$createdate = substr($d['createdate'], 0, 23);
			} else 
				$createdate = null;			
//			dd($date);
			if (!$d['candlevel'])
				$d['candlevel'] = 3;
			if (!$d['candrating'])
				$d['candrating'] = 3;
			if (!$d['salarycat'])
				$d['salarycat'] = 3;
			if (!$d['consultantid'])
				$d['consultantid'] = 1;
			if (!$d['eestatus'])
				$d['eestatus'] = 9;
			
			$rows[] = $d;
//			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
			  $bool_dup = $d['duplicate'] ? "'t'" : "'f'";
			  $bool_itv = $d['interviewed'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".$this->formatStr($birthdate).",".$this->formatStr($d['IDNumber']).",".$this->formatStr($createdate).",".$this->formatStr($d['consultantid']).",".$bool_dup.",".$this->formatStr($createdate).",".$this->formatStr($d['curlocation']).",".$this->formatStr($d['jobrole']).",".$this->formatStr($d['jobtitletxt']).",".
			$this->formatStr($d['salary']).",".$this->formatStr($d['candlevel']).",".$this->formatStr($d['candrating']).",".$this->formatStr($d['salarycat']).",".
			$this->formatStr($d['eestatus']).",".$bool_itv."," .(int)$d['activeflag'].",".
			$this->formatStr($d['availability']).",".
			$this->formatStr($d['gender']).")";
			}

//			$rows[] = $cells;
		}
//	dd($rows[0]);	

	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into candidates(id, birthdate, idnumber, created_at, consultant_id, duplicate, activated_at, current_location_id, jobtitle_id, jobtitle_text, salary, candidate_level_id, 
				candidate_rating_id, salary_category_id,
					ee_status_id, interviewed, status_id, availability_id, gender_id)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET birthdate = EXCLUDED.birthdate, idnumber = EXCLUDED.idnumber, created_at = EXCLUDED.created_at, consultant_id = EXCLUDED.consultant_id,
	 duplicate = EXCLUDED.duplicate, activated_at=EXCLUDED.activated_at, current_location_id = EXCLUDED.current_location_id, jobtitle_id = EXCLUDED.jobtitle_id, jobtitle_text = EXCLUDED.jobtitle_text, 
	 salary = EXCLUDED.salary,
	 candidate_level_id = EXCLUDED.candidate_level_id, ee_status_id = EXCLUDED.ee_status_id, interviewed = EXCLUDED.interviewed, status_id = EXCLUDED.status_id,
	 availability_id = EXCLUDED.availability_id, gender_id = EXCLUDED.gender_id;";
	 echo $sql;
	 
	 DB::statement($sql);

$roles = [1=>1, 2=>2, 7=>5];
//phone
foreach ($rows as $user) {
	 DB::statement("delete from addresses where addressable_id = ".$user['id']." and addressable_type = 'App\Candidate'");
		$sql = "insert into addresses(address1, address2, city, province, postal_code, country, addressable_type, addressable_id)
		     VALUES
			 (".$this->formatStr($user['addr1']).",".
				$this->formatStr($user['addr2']).",".
				$this->formatStr($user['town']).",".
				$this->formatStr($user['county']).",".
				$this->formatStr($user['zip']).",".
				$this->formatStr($user['country']).",'App\Candidate',".$user['id'].");";
		DB::statement($sql);	 



 		
    }
		
		
		
		
		
    }
}
