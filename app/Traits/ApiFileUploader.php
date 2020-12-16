<?php


namespace App\Traits;


use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\v1\Site\FileUploadRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ApiFileUploader
{

    /**
     * @var string
     */
    protected $uploadPath = 'uploads';

    /**
     * @var
     */
    public $folderName;

    /**
     * @var string
     */
    public $rule = 'image|max:2000';

    /**
     * Directory Seperator
     *
     * @var string
     */
    private $ds = DIRECTORY_SEPARATOR;

    /**
     * @return bool
     */
    private function createUploadFolder(): bool
    {
        if (!file_exists(config('filesystems.disks.public.root') . '/' . $this->uploadPath . '/' . $this->folderName)) {
            $attachmentPath = config('filesystems.disks.public.root') . '/' . $this->uploadPath . '/' . $this->folderName;
            mkdir($attachmentPath, 0777);

            Storage::put('public/' . $this->uploadPath . '/' . $this->folderName . '/index.html', 'Silent Is Golden');

            return true;
        }

        return false;

    }

    /**
     * For handle validation file action
     *
     * @param $file
     * @return fileUploadTrait|\Illuminate\Http\RedirectResponse
     */
    private function validateFileAction($file)
    {

        $rules = array('fileupload' => $this->rule);
        $file  = array('fileupload' => $file);

        $fileValidator = Validator::make($file, $rules);

        if ($fileValidator->fails()) {

            $messages = $fileValidator->messages();

            return redirect()->back()->withInput(request()->all())
                ->withErrors($messages);

        }
    }

    /**
     * For Handle validation file
     *
     * @param $files
     * @return fileUploadTrait|\Illuminate\Http\RedirectResponse
     */
    private function validateFile($files)
    {

        if (is_array($files)) {
            foreach ($files as $file) {
                return $this->validateFileAction($file);
            }
        }

        return $this->validateFileAction($files);
    }

    /**
     * For Handle Put File
     *
     * @param $file
     * @return bool|string
     */
    private function putFile($file)
    {
        $fileName = preg_replace('/\s+/', '_', time() . ' ' . $file->getClientOriginalName());
        $path     = $this->uploadPath . '/' . $this->folderName . '/';

        if (Storage::putFileAs('public/' . $path, $file, $fileName)) {
            return $path . $fileName;
        }

        return false;
    }

    /**
     * For Handle Save File Process
     *
     * @param $files
     * @return array
     */
    public function saveFiles($files)
    {
        $data = [];

        if($files != null){

            $this->validateFile($files);

            $this->createUploadFolder();

            if (is_array($files)) {

                foreach ($files as $file) {
                    $data[] = $this->putFile($file);
                }

            } else {

                $data[] = $this->putFile($files);
            }

        }

        return $data;
    }



    protected function upload(UploadedFile $uploadedFile, $path = 'uploads', $filename = 'file', $disk ='' , $mimes = '')
    {

        if($uploadedFile->isValid()) {

            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $fileName = $uploadedFile->getClientOriginalName();
            $fileExt  = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();

            $uploadPath = "{$this->ds}{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

            // dd(public_path($uploadPath . $this->ds . $fileName));

            if(file_exists(public_path("{$uploadPath}/{$fileName}"))) {
                $finalFileName = Carbon::now()->timestamp . "-{$fileName}";
            }

            $finalFileName = $fileName;

            dd(public_path($uploadPath . $finalFileName));


            $file->move(public_path($finalFileName), $fileName);

           return response()->json([
               'data' => [
                   'url' => url("{$filePath}/{$fileName}")
               ]
           ]);
        }
    }

    public function uploadOne(Request $request, $filename = null, $folder = null,  $disk = 'public')
    {
        $fileName = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        $path = $request->photo->path();

        $extension = $request->photo->extension();
        $path = $request->photo->store('images');
        $path = $request->photo->storeAs('images', 'filename.jpg');

        return $file;
    }

    public function deleteOne($folder = null, $filename = null, $disk = 'public')
    {
          Storage::disk($disk)->delete($folder.$filename);
    }


    public function verifyAndStoreImage( Request $request, $fieldname = 'image', $directory = 'unknown' ) {

        if( $request->hasFile( $fieldname ) ) {

            if (!$request->file($fieldname)->isValid()) {

                flash('Invalid Image!')->error()->important();

                return redirect()->back()->withInput();

            }

            return $request->file($fieldname)->store('image/' . $directory, 'public');

        }

        return null;

    }

    // public function up()
    // {
    //     $image_name = str_random(20);
    //     $ext = strtolower($query->getClientOriginalExtension()); // You can use also getClientOriginalName()
    //     $image_full_name = $image_name.'.'.$ext;
    //     $upload_path = 'image/';    //Creating Sub directory in Public folder to put image
    //     $image_url = $upload_path.$image_full_name;
    //     $success = $query->move($upload_path,$image_full_name);

    //     return $image_url; // Just return image
    //     public function store(Request $request)
    //     {
    //       $request->validate([
    //        'image' => 'required'
    //      ]);

    //      if ($request->hasFile('image')) {
    //      foreach($request->file('image ') as $file){

    //        $filePath = $this->UserImageUpload($file); //passing parameter to our trait method one after another using foreach loop

    //          Image::create([
    //            'name' => $filePath,
    //          ]);
    //        }
    //      }

    //      return redirect()->back();
    //     }
    // }


    // public function ups(Request $request)
    // {
    //     $req->validate([
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


    //             $this->validate($request, [
    //                 'name' => 'required',
    //                 'imgFile' => 'required|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
    //             ]);

    //             $image = $request->file('imgFile');
    //             $input['imagename'] = time().'.'.$image->extension();

    //             $filePath = public_path('/thumbnails');

    //             $img = Image::make($image->path());
    //             $img->resize(110, 110, function ($const) {
    //                 $const->aspectRatio();
    //             })->save($filePath.'/'.$input['imagename']);

    //             $filePath = public_path('/images');
    //             $image->move($filePath, $input['imagename']);

    //             return back()
    //                 ->with('success','Image uploaded')
    //                 ->with('fileName',$input['imagename']);
    //         }
    // }
}




