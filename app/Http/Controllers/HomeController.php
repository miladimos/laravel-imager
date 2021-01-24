<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $file = File::latest()->first();
        return view('home', compact('file'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);

        $uploadedFilePath = $this->uploadSingleImage($request->file('file'));

        return redirect()->route('home');
    }

      /**
     * check timestamp and return download
     *
     * @param $id
     * @return BinaryFileResponse
     * @throws InternalErrorException
     */
    public function download($file)
    {
        /** @var File $file */
        $file = File::query()
            ->where("id", $file)
            ->orWhere("name", $file)
            ->firstOrFail();

        $config = filemanager_config();

        if ($file->isPublic) {
            return $file->download();
        } else {
            $secret = "";
            if ($config['secret']) {
                $secret = $config['secret'];
            }

            $hash = $secret . $file->id . request()->ip() . request('t');

            if ((Carbon::createFromTimestamp(request('t')) > Carbon::now()) &&
                Hash::check($hash, request('mac'))) {
                return $file->download();
            } else {
                throw new InternalErrorException("link not valid");
            }
        }
    }

//     public function fileUpload(Request $req){
//         $req->validate([
//         'file' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
//         ]);

//         $fileModel = new File;

//         if($req->file()) {
//             $fileName = time().'_'.$req->file->getClientOriginalName();
//             $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');

//             $fileModel->name = time().'_'.$req->file->getClientOriginalName();
//             $fileModel->file_path = '/storage/' . $filePath;
//             $fileModel->save();

//             return back()
//             ->with('success','File has been uploaded.')
//             ->with('file', $fileName);
//         }
//    }

}
