<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\DataFile;

use Illuminate\Support\Str;

class DataFileController extends Controller
{
	
	private $model_class = ['candidates'=>'App\Candidate',
							'clients'=>'App\Client',
							'jobs'=>'App\JobAd',
							'emails'=>'App\LoggedEmail',
							'events'=>'App\CalendarEvent'
										];	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		       $request()->validate([
         'file'  => 'required|mimes:doc,docx,pdf,txt|max:2048',
       ]);
 
       if ($files = $request->file('fileUpload')) {
           $destinationPath = 'public/files/'; // upload path
           $profilefile = date('YmdHis') . "." . $files->getClientOriginalExtension();
           $files->move($destinationPath, $profilefile);
           $insert['file'] = "$profilefile";
        }
         
        $check = DataFile::insertGetId($insert);
 
        return Redirect::to("file")
        ->withSuccess('File has been successfully uploaded.');
 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

	
    public function download($fileid)
    {
        //
		list($id, $model, $modelid) = explode('_', $fileid);
        $file = DataFile::find($id);

//dd($file->datafileable());
		if (!(($file->datafileable_type == $this->model_class[$model]) && ($file->datafileable_id == $modelid))) {
			return false;
		}
		$filepath = "";
		if ($file->host)
			$filepath .= $file->host.'/';
		$filepath .= $file->location;
//		exit;
		$headers = array(
              'Content-Type: '.$file->filetype,
        );
		

//		return response()->download($store_path.$filepath, $file->filename, $headers);
		return Storage::disk('uploads')->download($filepath, $file->filename, $headers);
//      return response()->file(storage_path("files/".$filepath));
        // view("files.download", compact('$download'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rename(Request $request, $fileid)
    {
        //
 		list($id, $model, $modelid) = explode('_', $fileid);
		
        $file = DataFile::findOrFail($id);

		if (($file->datafileable_type == $this->model_class[$model]) && ($file->datafileable_id == $modelid)) {
			
			$extension = pathinfo($file->filename, PATHINFO_EXTENSION);
			$newname = $request->input('filename');
			// regex to valid filename ??
			$newname = str_replace(array('\\','/',':','*','?','"','<','>','|'),'',$newname);
			
			if ($newname) {
				if ($extension && !Str::endsWith($newname, $extension))
				   $newname .= ".$extension";
				$file->filename = $newname;
				$file->save();
				return $newname;
			}
		}
		return false;
	}
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fileDestroy($fileid)
    {
        //
		list($id, $model, $modelid) = explode('_', $fileid);
		
        $file = DataFile::findOrFail($id);

		if (($file->datafileable_type == $this->model_class[$model]) && ($file->datafileable_id == $modelid)) {
			$file->delete();
			return true;
		}
		return false;
    }	
}
