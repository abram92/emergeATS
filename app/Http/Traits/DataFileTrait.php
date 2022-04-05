<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\DataFile;

use Illuminate\Support\Facades\Storage;

trait DataFileTrait
{
	
	private $base_path = ['App\Candidate'=>'cand',
							'App\Client'=>'client',
							'App\JobAd'=>'job',
							'App\LoggedEmail'=>'email',
							'App\CalendarEvent'=>'event'
										];

	private $url_model = ['App\Candidate'=>'candidates',
							'App\Client'=>'clients',
							'App\JobAd'=>'jobs',
							'App\LoggedEmail'=>'emails',
							'App\CalendarEvent'=>'events'
										];	
	public function fileupload(Request $request, $modelid) 
	{


			$model = $this->model_class::findOrFail($modelid);			
// dd($model->documents());

        $image = $request->file('file');
//		dd(storage_path());
        $imageName = $image->getClientOriginalName();
        
        $imageUpload = new DataFile();
		$imageUpload->filename = $imageName;
		$imageUpload->filetype = $image->getMimeType();
		$imageUpload->size = $image->getSize(); 
		$imageUpload->host = $this->base_path[$this->model_class]; //'.'; 
		
		$classTotal = DataFile::where('datafileable_type', $this->model_class)->count();
		$classTotal++;
		$subfolder = floor($classTotal/5000).'/';
		
	   $newFolder = Storage::disk('uploads')->path($imageUpload->host.'/'.$subfolder);
	   $newFilename = $classTotal.'_'.$model->id; //imageName;
		
		$imageUpload->location = $subfolder.$newFilename;
		
	//	$imageUpload->save();
       $image->move($newFolder, $newFilename);
// dd([$newFolder, $newFilename]);
			$newrecord = $model->documents()->save($imageUpload); 
			$all_ids[] = $newrecord->id;
			
$route = route('file.download', ['id' => $newrecord->id.'_'.$this->url_model[$this->model_class].'_'.$modelid]);
$routedel = route('file.delete', ['id' => $newrecord->id.'_'.$this->url_model[$this->model_class].'_'.$modelid]);
$routerename = route('file.rename', ['id' => $newrecord->id.'_'.$this->url_model[$this->model_class].'_'.$modelid]);
		return ['success' => true, 'id'=>$newrecord->id, 'url'=>$route, 'deleteurl'=>$routedel, 'renameurl'=>$routerename];
	}
	

	
	public function filedelete(Array $request, Model $model) 
	{

		
		$all_ids = [];
		foreach ($addresses as $address) {
			$id = isset($address["id"]) ? $address["id"] : null;
			/*
			$data = isset($address["data"]) ? $address["data"] : null;
			$type = isset($address["type"]) ? $address["type"] : null;

			$newrecord = $client->addresses()->updateOrCreate(['id' => $id], 
								['data' => $data,
								 'contact_field_type_id' => $type]); */
			unset($address['id']);					 
			$newrecord = $model->documents()->destroy($id); 
			$all_ids[] = $newrecord->id;
			
		}

		return ['success' => true];
	}	
	
	
}
