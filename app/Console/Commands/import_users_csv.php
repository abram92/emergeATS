<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;
use Role;
class import_users_csv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users_csv {filename} {delimiter=,}';

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

    $header = ['id', 'username', 'password', 'firstname', 'lastname', 'telephone', 'faxnumber', 'mobilephone',  
				'email', 'visitcount', 'question_phrase', 'pass_phrase', 'subscribe_code', 'ownerdomain', 'activeflag', 'emailsignature'];
    $data = array();
	$staff = array();
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
			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
				$d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
			}
			$users[] = $d;
			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
//			  $bool = $d['prospect'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".$this->formatStr($d['email']).",".$this->formatStr($d['password']).",".$this->formatStr($d['firstname']).",".$this->formatStr($d['lastname']).",".$this->formatStr($d['username']).",".$deleted_at.",".$this->formatStr($d['subscribe_code']).",".$this->formatStr($d['emailsignature'])." )";
			}
			}
        fclose($handle);
    }
	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into users(id, email, password, firstname, lastname, username, deleted_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET email = EXCLUDED.email, password = EXCLUDED.password, firstname = EXCLUDED.firstname, lastname = EXCLUDED.lastname,
	 username = EXCLUDED.username, deleted_at = EXCLUDED.deleted_at, jobcode = EXCLUDED.jobcode, emailsignature = EXCLUDED.emailsignature;";
	 echo $sql;
	 
	 DB::statement($sql);
	 

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
//	 DB::statement("delete from contact_fields where contactable_id = ".$user['id']." and contactable_type = 'App\User' and contact_field_type_id = ".$types['Email']);
	 DB::statement("delete from model_has_roles where model_id = ".$user['id']." and model_type = 'App\User'");
	if ($user['ownerdomain']) {
		$domain = $user['ownerdomain'];
		$roleid = $roles[$domain];
		$sql = "insert into model_has_roles(role_id, model_type, model_id)
		     VALUES
			 (".$roleid.",'App\User',".$user['id'].");";
DB::statement($sql);	 }

 		
    }
		
		
    }
}
