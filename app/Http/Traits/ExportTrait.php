<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Search;
use App\Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

trait ExportTrait
{
	
	protected function exportResultSet($headers, $results, $filter)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		$row = 1;
		if (!empty($filter)) {
			$sheet->setCellValue('A'.$row, 'Query Filter');
			$row++;
			foreach ($filter as $field => $condition) {
				$sheet->setCellValue('A'.$row, $field);
				$sheet->setCellValue('C'.$row, $condition);
				$row++;
			}
			$row++;
		}
		
		if (!empty($headers)) {
			foreach ($headers as $col => $header) {
				$col++;
				$sheet->setCellValueByColumnAndRow($col, $row, $header);	
			}
			$row++;
		}
		if (!empty($results)) {
			foreach ($results as $rowdata) {
				foreach ($rowdata as $col => $val) {
					$col++;
					$sheet->setCellValueByColumnAndRow($col, $row, $val);	
				}
				$row++;
			}
		}
		$writer = new Xls($spreadsheet);
		header("Content-Type: application/vnd.ms-excel");
		$writer->save('php://output');
		
		
	}
	
	
}
