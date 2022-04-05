<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class import_joblocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:job_locations {filename} {deleteBefore=0} {delimiter=,}';

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
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null; //['jobid', 'locationid'];
    $data = array();
	$joblocations = array();
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
			if ($d['jobid'] && $d['locationid'])
			$data[] = "(".(int)$d['jobid'].",".(int)$d['locationid']." )";
			}
			}
        fclose($handle);
    }
//dd(1);	
	if (empty($data))
		return;
	 DB::statement("delete from job_ad_location");
	
//	dd($data);
//		dd($tablename); userable_type, user_ableid
		$sql = "insert into job_ad_location(job_ad_id, location_id) VALUES ";

$sql .= implode(',', $data);
$sql .= " ;";
	 echo $sql;
	 
	 DB::statement($sql);
	 
    }
}
