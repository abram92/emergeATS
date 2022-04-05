<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ContactFieldType;
use DB;

class import_clients_csv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:clients {filename} {delimiter=,}';

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
       //
       $types = ContactFieldType::pluck('id', 'name')->all();
//dd($icons);
//exit;	   
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = ['id', 'name', 'addr1', 'addr2', 'town', 'county', 'country', 'zip', 
				'phone', 'fax', 'url', 'prospect', 'activeflag', 
				'agencynotes', 'consultantid', 'techenvironment'];
    $data = array();
	$clients = array();
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
			$clients[] = $d;
			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
			  $bool = $d['prospect'] ? "'t'" : "'f'";
			$data[] = "(".(int)$d['id'].",".$this->formatStr($d['name']).",".$d['activeflag'].",".$this->formatStr($d['consultantid']).", ".$bool.")";
			}
			}
        fclose($handle);
    }
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename);
		$sql = "insert into clients(id, name, status_id, consultant_id, prospect)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET name = EXCLUDED.name, status_id = EXCLUDED.status_id, consultant_id = EXCLUDED.consultant_id;";
	 echo $sql;
	 
	 DB::statement($sql);
	 
dd(1);	

//address


foreach ($clients as $client) {
	 DB::statement("delete from addresses where addressable_id = ".$client['id']." and addressable_type = 'App\Client'");
		$sql = "insert into addresses(address1, address2, city, province, postal_code, country, addressable_type, addressable_id)
		     VALUES
			 (".$this->formatStr($client['addr1']).",".
				$this->formatStr($client['addr2']).",".
				$this->formatStr($client['town']).",".
				$this->formatStr($client['county']).",".
				$this->formatStr($client['zip']).",".
				$this->formatStr($client['country']).",'App\Client',".$client['id'].");";
		DB::statement($sql);	 

}
//phone
foreach ($clients as $client) {
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\Client' and contact_field_type_id = ".$types['Phone']);
	if ($client['phone']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Phone'].",".$this->formatStr($client['phone']).",'App\Client',".$client['id'].");";
DB::statement($sql);	 }


//fax
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\Client' and contact_field_type_id = ".$types['Fax']);
	if ($client['fax']) {
	$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Fax'].",".$this->formatStr($client['fax']).",'App\Client',".$client['id'].");";

	 DB::statement($sql);
	}
 		
	
// web
	 DB::statement("delete from contact_fields where contactable_id = ".$client['id']." and contactable_type = 'App\Client' and contact_field_type_id = ".$types['Website']);
if ($client['url']) {
		$sql = "insert into contact_fields(contact_field_type_id, data, contactable_type, contactable_id)
		     VALUES
			 (".$types['Website'].",".$this->formatStr($client['url']).",'App\Client',".$client['id'].");";

	 DB::statement($sql);
}
 		
    }
	}
	
}
