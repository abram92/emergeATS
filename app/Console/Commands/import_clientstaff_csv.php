<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;

class import_clientstaff_csv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:client_contacts {filename} {delimiter=,}';

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
		
       $types = ContactFieldType::pluck('id', 'name')->all();
//dd($icons);
//exit;	   
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = ['id', 'clientid', 'position', 'firstname', 'lastname', 'telephone', 'cellphone', 'faxnumber', 
				'email', 'visitcount', 'activeflag', 'comments', 'qmstatus'];
    $data = array();
	$staff = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
//		dd($delimiter);
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
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
			$staff[] = $d;
			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
//			  $bool = $d['prospect'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".(int)$d['clientid'].",".$this->formatStr($d['position']).",".$this->formatStr($d['firstname']).",".$this->formatStr($d['lastname']).",".$deleted_at." )";
			}
			}
        fclose($handle);
    }
	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename);
		$sql = "insert into client_contacts(id, client_id, position, firstname, lastname, deleted_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET client_id = EXCLUDED.client_id, position = EXCLUDED.position, firstname = EXCLUDED.firstname, lastname = EXCLUDED.lastname ;";
	 echo $sql;
	 
	 DB::statement($sql);
	 


//phone
foreach ($staff as $client) {
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\ClientContact' and contact_field_type_id = ".$types['Phone']);
	if ($client['telephone']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Phone'].",".$this->formatStr($client['telephone']).",'App\ClientContact',".$client['id'].");";
DB::statement($sql);	 }


//fax
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\ClientContact' and contact_field_type_id = ".$types['Fax']);
	if ($client['faxnumber']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Fax'].",".$this->formatStr($client['faxnumber']).",'App\ClientContact',".$client['id'].");";

	 DB::statement($sql);
	}
 		
	
// web
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\ClientContact' and contact_field_type_id = ".$types['Cell']);
if ($client['cellphone']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Cell'].",".$this->formatStr($client['cellphone']).",'App\ClientContact',".$client['id'].");";

	 DB::statement($sql);
}
// email
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\ClientContact' and contact_field_type_id = ".$types['Email']);
if ($client['email']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Email'].",".$this->formatStr($client['email']).",'App\ClientContact',".$client['id'].");";

	 DB::statement($sql);
}

 		
    }
		
		
		
    }
}
