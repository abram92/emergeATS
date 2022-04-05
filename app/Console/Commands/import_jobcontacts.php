<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class import_jobcontacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:job_contacts {filename} {delimiter=,}';

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
	
	  return ($field && ($field != 'NULL')) ? "'".str_replace("'", "''", $field)."'" : 'null';
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
		
//       $types = ContactFieldType::pluck('id', 'name')->all();
//dd($icons);
//exit;	   
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null; //['jobid', 'contactid'];
    $data = array();
	$jobcontacts = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
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
			if (mb_detect_encoding($d['jobid']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
				$d['jobid'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['jobid']);
			}
			$jobs[] = $d;
			$data[] = "(".(int)$d['jobid'].",".(int)$d['contactid']." )";
			}
			}
        fclose($handle);
    }
//dd(1);	
	if (empty($data))
		return;
	 DB::statement("delete from client_contact_job_ad");
	
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into client_contact_job_ad(job_ad_id, client_contact_id) VALUES ";

$sql .= implode(',', $data);
$sql .= " ;";
	 echo $sql;
	 
	 DB::statement($sql);
	 
/*
$roles = [1=>1, 2=>2, 7=>5];
//phone
foreach ($users as $user) {
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Phone']);
	if ($user['telephone']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Phone'].",".$this->formatStr($user['telephone']).",'App\User',".$user['id'].");";

	 DB::statement($sql);
	}



//fax
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Fax']);
	if ($user['faxnumber']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Fax'].",".$this->formatStr($user['faxnumber']).",'App\User',".$user['id'].");";

	 DB::statement($sql);
	}
 		
	
// web
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Cell']);
if ($user['mobilephone']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Cell'].",".$this->formatStr($user['mobilephone']).",'App\User',".$user['id'].");";

	 DB::statement($sql);
}
// email
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Email']);
if ($user['email']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Email'].",".$this->formatStr($user['email']).",'App\User',".$user['id'].");";

	 DB::statement($sql);
}



// roles
	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Email']);
	 DB::statement("delete from model_has_roles where model_id = ".$user['id']." and model_type = 'App\User'");
	if ($user['ownerdomain']) {
		$domain = $user['ownerdomain'];
		$roleid = $roles[$domain];
		$sql = "insert into model_has_roles(role_id, model_type, model_id)
		     VALUES
			 (".$roleid.",'App\User',".$user['id'].");";
DB::statement($sql);	 }

 		
    } */
		
    }
}
