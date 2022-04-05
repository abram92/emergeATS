<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_candidates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:candidates {filename} {delimiter=,}';

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
////		$worksheet = $spreadsheet->getSheetByName('VUser');
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
			if ($d['ownerdomain'] == 3) {
			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
			  $username = $d['username']."--".$d['id'];
			  if (!$d['email']) 
				  $d['email'] = ['undefined'];
			  else {
				  $emailArr =  preg_split("/(,| |\||;)/", $d['email']);				
//dd($emailArr);				  
				  $d['email'] = array_unique(array_diff($emailArr,array("null","")));
			$rows[] = $d;
				  
			  }			  
			  if (!$d['lastname']) $d['lastname'] = 'undefined';
			  if (!$d['firstname']) $d['firstname'] = 'undefined';
//			  $bool = $d['prospect'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".$this->formatStr(implode(',', $d['email'])).",".$this->formatStr($d['password']).",".$this->formatStr($d['firstname']).",".$this->formatStr($d['lastname']).",".$this->formatStr($username).",".$deleted_at.",'App\Candidate', ".(int)$d['id'].", ".($d['activeflag'] ? 1 : 0)." )";
			}
			}

//			$rows[] = $cells;
		}
//	dd($rows[0]);	

	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into users(id, email, password, firstname, lastname, username, deleted_at, userable_type, userable_id, is_active)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET email = EXCLUDED.email, password = EXCLUDED.password, firstname = EXCLUDED.firstname, lastname = EXCLUDED.lastname,
	 username = EXCLUDED.username, deleted_at = EXCLUDED.deleted_at, userable_type = EXCLUDED.userable_type, userable_id = EXCLUDED.userable_id, is_active = EXCLUDED.is_active;";
	 echo $sql;
	 
	 DB::statement($sql);
	 

$roles = [1=>1, 2=>2, 7=>5];
//phone
foreach ($rows as $user) {
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\Candidate' and contact_field_type_id = ".$types['Phone']);
	if ($user['telephone']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Phone'].",".$this->formatStr($user['telephone']).",'App\Candidate',".$user['id'].");";

	 DB::statement($sql);
	}



//fax
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\Candidate' and contact_field_type_id = ".$types['Fax']);
	if ($user['faxnumber']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Fax'].",".$this->formatStr($user['faxnumber']).",'App\Candidate',".$user['id'].");";

	 DB::statement($sql);
	}
 		
	
// web
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\Candidate' and contact_field_type_id = ".$types['Cell']);
if ($user['mobilephone']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Cell'].",".$this->formatStr($user['mobilephone']).",'App\Candidate',".$user['id'].");";

	 DB::statement($sql);
}
// email
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\Candidate' and contact_field_type_id = ".$types['Email']);
/*	 
if ($user['email']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Email'].",".$this->formatStr($user['email']).",'App\Candidate',".$user['id'].");";

	 DB::statement($sql);
}
*/
if (is_array($user['email'])) {
	foreach ($user['email'] as $singleAddress) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Email'].",".$this->formatStr($singleAddress).",'App\Candidate',".$user['id'].");";

		DB::statement($sql);
	}
}

 		
    }
		
		
		
		
		
    }
}
