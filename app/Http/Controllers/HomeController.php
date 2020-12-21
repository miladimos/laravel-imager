<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

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

        $uploadedFilePath = $this->uploadSingleImage($request->file('file'));
        // dd($uploadedFilePath);

        // $img = Image::make($request->file('file'))->fit(200, 200)->save($request->file('file')->getClientOriginalName());


        // $img = Image::make($request->file('file'))->resize(200,10);
        // return $img->response();
        return redirect()->route('home');
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
