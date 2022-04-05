<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
class import_basedata_csv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:basedata {tablename} {filename} {delimiter=,}';

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
		$tablename = $this->argument('tablename');
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = ['id', 'description', 'activeflag'];
    $data = array();
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
			  $deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
			$data[] = "(".(int)$d['id'].",".$this->formatStr($d['description']).",".$deleted_at.")";
        }
        fclose($handle);
    }
	
	if (empty($data))
		return;
//	dd($data);
//		dd($tablename);
		$sql = "insert into $tablename(id, description, deleted_at)
OVERRIDING SYSTEM VALUE VALUES ";

$sql .= implode(',', $data);
$sql .= " ON CONFLICT (id) 
DO
      UPDATE
     SET description = EXCLUDED.description, deleted_at = EXCLUDED.deleted_at;";
	 echo $sql;
	 
	 DB::statement($sql);
	 
    }
}
