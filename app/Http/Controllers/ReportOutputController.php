<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

class ReportOutputController extends Controller {

	protected $fileName = 'export';
	protected $view = 'reports.output';

	public function callAction($method, $params)
	{
		$response = parent::callAction($method, $params);

    // default JSON. you may wish to change this to be HTML instead
		if ( ! isset($params['format'])) return $response;

		if ($params['format'] == '.csv')
		{
			return $this->asCsv($response);
		}

		if ($params['format'] == '.html')
		{
			return $this->asHtml($response);
		}

		return $response;
	}

	protected function asCsv($response)
	{
		$csv = '';

		if ($response && count($response))
		{
			ob_start();
			$handle = fopen('php://output', 'r+');

			$first = ($response instanceof Collection) ? $response->first() : reset($response);
			fputcsv($handle, array_keys($first));

			foreach ($response as $r)
			{
				fputcsv($handle, array_values($r));
			}

			$csv = ob_get_clean();
			fclose($handle);
		}

		return Response::make($csv, 200, [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment;filename='.$this->fileName.'.csv'
		]);
	}

	protected function asHtml($response)
	{
		$path = '/'.str_replace('.html', '', Request::path());
		$query = Request::getQueryString();

		$first = ($response instanceof Collection) ? $response->first() : reset($response);
		return View::make($this->view)
			->with('title', $this->fileName)
			->with('headers', $first ? array_keys($first) : [])
			->with('rows', $response)
			->with('error', $first ? null : 'No data found for criteria.')
			->with('urls', (object) [
				'csv' => $path.'.csv?'.$query,
				'json' => $path.'.json?'.$query,
			]);
	}


	protected function exportCsv(String $fileName, Array $data)
	{

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = $data['columns']; 

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data['results'] as $record) {
				$row = [];
				foreach($columns as $colKey=>$colVal) {
                     $row[] =  $record[$colKey];
				}
				fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
	
	
}