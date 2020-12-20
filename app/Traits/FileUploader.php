<?php namespace App\Traits;

use Carbon\Carbon;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileUploader
{
    /**
     * Default File Disk
     *
     * @var string
     */
    private $disk = 'public';

    /**
     * Default Upload Folder Name
     *
     * @var string
     */
    private $uploadfolderName = 'uploads';

    /**
     * Directory Seperator
     *
     * @var string
     */
    private $ds = DIRECTORY_SEPARATOR;

    /**
     * File Model
     *
     * @var string
     */
    private $model = File::class;

    /**
     * Image Sizes
     *
     * @var string
     */
    private $sizes = [
        'thumbnail' => [
            'width' => '120',
            'height' => '120'
        ],
        'small' => '',
        'medium' => '',
        'original' => '',
    ];


    public function uploadSingleImage(UploadedFile $uploadedFile, $path = 'uploads')
    {
        // dd($uploadedFile->getType());
        if($uploadedFile->isValid()) {
            $model = resolve($this->model);

            $img = Image::make($uploadedFile->getRealPath());
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $fileName = $uploadedFile->getClientOriginalName();
            $fileExt  = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize();

            $uploadPath = "{$this->ds}{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

            $fullUploadedPath = public_path($uploadPath . $this->ds . $fileName);

            $dirPath = public_path($uploadPath);

            $this->mkdir_if_not_exists($dirPath);

            if(file_exists($fullUploadedPath)) {
                $finalFileName = Carbon::now()->timestamp . "-{$fileName}";

                $img->save($dirPath . $finalFileName);

                return response()->json([
                    'data' => [
                        'url' => url($uploadPath . $this->ds . $finalFileName)
                    ]
                ]);
            }

            $img->save($fullUploadedPath);

            // $uploadedFile->move(public_path($uploadPath), $fileName);

            return response()->json([
                'data' => [
                    'url' => url($uploadPath . $this->ds . $fileName)
                ]
            ]);
        }

        return response()->json([
            'data' => 'File Not Valid!'
        ]);

    }

    // $path = $request->photo->storeAs('images', 'filename.jpg', 'disk');


    public function uploadOne(UploadedFile $uploadedFile, $filename ='', $folder = null,  $disk = 'public')
    {
        $fileName = !is_null($filename) ? $filename : $uploadedFile->getClientOriginalName();

        $file = $uploadedFile->move($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

    function mkdir_if_not_exists($dirPath) {
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
    }

}
