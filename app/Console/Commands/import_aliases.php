<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class import_aliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aliases {filename} {delimiter=,}';

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
		$filename = $this->argument('filename');
		$delimiter = $this->argument('delimiter');
		   if (!file_exists($filename) || !is_readable($filename))
        return false;

//    $header = ['id', 'description', 'activeflag'];
//    $data = array();
	
		$reader = new Reader\Xls();
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);
		$worksheet = $spreadsheet->getActiveSheet();
		$rows = [];
		
		$success = false;
		DB::beginTransaction();
		try {

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
//			if (mb_detect_encoding($d['id']) === 'UTF-8') {
    // delete possible BOM
    // not all UTF-8 files start with these three bytes
//    $d['id'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $d['id']);
//}
			

//			$rows[] = $cells;
					$deleted_at = $d['activeflag'] ? "null" : "current_timestamp";
if (is_numeric($d['lastupdatedtime'])) {
				$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d['lastupdatedtime']);
				$update_date = $date->format('Y-m-d H:i:s');
} else 
	$update_date = $d['lastupdatedtime'];
$catid = $d['catid'] ? (int)$d['catid'] : 'null';

					$sql = "insert into aliases(id, description, alias_category_id, minimum_parser_matches, updated_at, deleted_at)
							OVERRIDING SYSTEM VALUE VALUES ";

					$sql .= "(".(int)$d['id'].",".$this->formatStr($d['description']).",".$catid.",".(int)$d['minparsermatches'].",".$this->formatStr($update_date).",".$deleted_at.")";
					$sql .= " ON CONFLICT (id) 
								DO
								UPDATE
								SET description = EXCLUDED.description, 
								alias_category_id = EXCLUDED.alias_category_id, 
								minimum_parser_matches = EXCLUDED.minimum_parser_matches, 
								updated_at = EXCLUDED.updated_at,
								deleted_at = EXCLUDED.deleted_at;";
					echo $sql;
	 
					DB::statement($sql);

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
			
    }
}
