<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Newelement\Neutrino\Models\ActivityLog;

class MediaController extends Controller
{
    private $pathRoot = 'uploads';
    private $disk = 'public';
    private $fileIgnores = [
        '.DS_Store',
    ];

    private $folderIgnores = [
        '_thumb',
        '_small',
        '_medium',
        '_large',
        '_original'
    ];

    public function __construct(){
        $this->disk = config('neutrino.storage.filesystem');
    }

    public function index()
    {
        return view('neutrino::admin.media.index');
    }

    public function get(Request $request)
    {

        $uploadsExists = Storage::disk($this->disk)->has('uploads');
        if( !$uploadsExists ){
            Storage::disk($this->disk)->makeDirectory('uploads');
        }

        $path = $request->path;
        $file_type = $request->file_type;

        if( !$path ){
            $path = $this->pathRoot;
        }

        $data = [];
        $items = [];
        $filesArr = [];

        $folders = Storage::disk($this->disk)->directories($path);
        $files = Storage::disk($this->disk)->files($path);

        $i = 0;
        foreach( $folders as $folder ){
            foreach ($this->folderIgnores as $ignore) {
                if (strpos($folder, $ignore) !== FALSE) {
                    unset($folders[$i]);
                }
            }
            $i++;
        }

        $folders = array_values($folders);

        foreach( $folders as $key => $folder ){
            $folders[$key] = [ 'path' => $folder, 'loading' => false ];
        }

        foreach( $files as $file ){
            $pathInfo = pathinfo($file);
            if( in_array($pathInfo['basename'], $this->fileIgnores) ){
                continue;
            }

            $isImage = $this->imageType($pathInfo['extension']);

            $arr = [
                'id' => uniqid(),
                'path' => $pathInfo['dirname'],
                'info' => $pathInfo,
                'filename' => $pathInfo['basename'],
                'url' => Storage::disk($this->disk)->url($file),
                'image' => $isImage,
                'selected' => false
            ];

            if( $file_type === 'image' && $isImage){
                $arr['sizes'] = $this->getSizes($file);
                $filesArr[] = $arr;
            }

            if( $file_type === 'file' && !$isImage){
                $filesArr[] = $arr;
            }

            if( $file_type === 'all' ){
                $arr['sizes'] = $this->getSizes($file);
                $filesArr[] = $arr;
            }
        }

        $folders = collect(['folders' => $folders]);
        $files = collect(['files' => $filesArr]);

        $items = $folders->merge($files);

        $data['items'] = $items;

        return response()->json([ 'fileData' => $data ]);
    }

    public function uploadFiles(Request $request)
    {
        $files = $request->file('file');
        $path = $request->path;
        $filesArr = [];
        $success = true;
        $message = '';
        $status = 200;

        $path = $path.'/';

        $i = 0;
        foreach($files as $file){
            if( $file->isValid() ){
                $imageName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $pathInfo = pathinfo($path.$imageName);

                $imageName = $this->sanitizeFilename($imageName);

                if( $this->fileExists($path.$imageName) ){
                    $imageName = uniqid().'-'.$imageName;
                }

                if( !$this->imageType( $mimeType ) ){
                    Storage::disk($this->disk)->put($path.$imageName, $file, 'public');
                  } else {

                    try{
                        $image = Image::make($file);
                    } catch (\Exception $e){
                        return response()->json(['message' => $e->getMessage() ], 500);
                    }

                    $image->backup();

                    $resource = $image->stream()->detach();
                    Storage::disk($this->disk)->put($path.'_original/'.$imageName, $resource, 'public');

                    $imageSizes = config('neutrino.media.image_sizes');

                    foreach( $imageSizes as $key => $size ){

                        //$image->backup();
                        //$image->reset();

                        if( $key === 'thumb' && config('neutrino.media.thumb_crop') === 'square' ){
                            $thumbData = Image::make($file)->fit( $size );
                            $resource = $thumbData->stream()->detach();
                        } else {
                            $image->resize( $size , null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                            $resource = $image->stream()->detach();
                        }
                        if( $key === 'thumb' ){
                            Storage::disk($this->disk)->put($path.$imageName, $resource, 'public');
                        } else {
                            Storage::disk($this->disk)->put($path.'_'.$this->sanitizeSizeName($key).'/'.$imageName, $resource, 'public');
                        }

                    }

                    $image->destroy();
                }

                $filesArr[] = [
                    'id' => uniqid(),
                    'info' => $pathInfo,
                    'path' => $pathInfo['dirname'],
                    'filename' => $pathInfo['basename'],
                    'url' => Storage::disk($this->disk)->url($path.$imageName),
                    'image' => $this->imageType($pathInfo['extension']),
                    'selected' => false,
                    'sizes' => $this->getSizes($path.$imageName)
                ];

                $i++;
            } else {
                $success = false;
                $message = 'One or more files were not uploaded:<br>';
                $invalids[] =  $file->getClientOriginalName().' ['.$file->getErrorMessage().'] ';
                $message .= implode('<br>', $invalids);
                $status = 500;
            }
        }

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'media.upload',
            'content' => 'Files uploaded '.count($files).' - '.$message,
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json(['success' => $success, 'files' => $filesArr, 'message' => $message], $status);
    }

    private function imageType( $mimeType )
    {
        $image = false;
        switch( strtolower($mimeType) ){
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/webp':
            case 'jpg':
            case 'webp':
            case 'jpeg':
            case 'png':
            case 'gif':
                $image = true;
            break;
        }
        return $image;
    }

    public function createFolder(Request $request)
    {
        $path = $request->path;
        $folderName = $request->folder_name;
        $folder = Storage::disk($this->disk)->makeDirectory($path.'/'.$folderName);

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'media.folder.create',
            'content' => $folderName.' was created',
            'log_level' => 0,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json(['folder' => $folder]);
    }

    public function deleteFolder(Request $request)
    {
        $path = $request->path;
        $folderName = $request->folder_name;
        $delete = false;
        $slash = $this->disk === 's3' || $this->disk === 'S3'? '/': '';
        if( strlen($folderName) > 2 ){
            $delete = Storage::disk($this->disk)->deleteDirectory($folderName.$slash);
        }

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'media.folder.delete',
            'content' => $folderName.' was deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json(['delete' => $delete]);
    }

    public function deleteFile(Request $request)
    {
        $path = $request->path;
        $deleted = Storage::disk($this->disk)->delete($path);
        $pathinfo = pathinfo($path);
        $adjpath = $pathinfo['dirname'];
        $imageName = $pathinfo['basename'];
        $sizes = config('neutrino.media.image_sizes');

        if(Storage::disk($this->disk)->exists($adjpath.'/_original/'.$imageName)){
            Storage::disk($this->disk)->delete($adjpath.'/_original/'.$imageName);
        }
        foreach( $sizes as $key => $size ){
            if(Storage::disk($this->disk)->exists($adjpath.'/_'.$this->sanitizeSizeName($key).'/'.$imageName)){
                Storage::disk($this->disk)->delete($adjpath.'/_'.$this->sanitizeSizeName($key).'/'.$imageName);
            }
        }

        ActivityLog::insert([
            'activity_package' => 'neutrino',
            'activity_group' => 'media.file.delete',
            'content' => $path.' was deleted',
            'log_level' => 1,
            'created_by' => auth()->user()->id,
            'created_at' => now()
        ]);

        return response()->json(['deleted' => $deleted]);
    }

    public function listDirsFiles()
    {
        $files = Storage::disk($this->disk)->files($directory);
    }

    public function editImage(Request $request)
    {
        $inputImage = $request->image;
        $path = $request->path;
        $imageName = $request->current_image;
        $success = true;
        $message = '';
        $file = [];

        if (preg_match('/^data:image\/(\w+);base64,/', $inputImage)) {

            $data = substr($inputImage, strpos($inputImage, ',') + 1);
            $data = base64_decode($data);

            $pathInfo = pathinfo($path.'/'.$imageName);

            try{
                $image = Image::make($data);
                $image->backup();
            } catch(\Exception $e){
                $success = false;
                $message = 'Could not edit image. '.$e->getMessage();
                return response()->json(['success' => $success, 'message' => $message]);
            }

            $imageSizes = config('neutrino.media.image_sizes');

            foreach( $imageSizes as $key => $size ){

                $image->reset();

                if( $key === 'thumb' && config('neutrino.media.thumb_crop') === 'square' ){
                    try{
                        $thumbData = Image::make($image)->fit( $size );
                        $resource = $thumbData->stream()->detach();
                    } catch(\Exception $e){
                        $success = false;
                        $message = 'Could not create image. '.$e->getMessage();
                        return response()->json(['success' => $success, 'message' => $message]);
                    }
                } else {
                    $image->resize( $size , null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $resource = $image->stream()->detach();
                }
                if( $key === 'thumb' ){
                    Storage::disk($this->disk)->put($path.$imageName, $resource);
                } else {
                   Storage::disk($this->disk)->put($path.'_'.$this->sanitizeSizeName($key).'/'.$imageName, $resource);
                }
            }

            $image->destroy();

            $file = [
                'id' => uniqid(),
                'path' => $pathInfo['dirname'],
                'info' => $pathInfo,
                'filename' => $pathInfo['basename'],
                'url' => Storage::disk($this->disk)->url($path.$imageName),
                'image' => true,
                'selected' => false,
                'sizes' => $this->getSizes($path.'/'.$imageName)
            ];

            ActivityLog::insert([
                'activity_package' => 'neutrino',
                'activity_group' => 'media.image.edit',
                'content' => $path.'/'.$imageName.' '.$message,
                'log_level' => 0,
                'created_by' => auth()->user()->id,
                'created_at' => now()
            ]);

        }

        return response()->json(['success' => $success, 'file' => $file, 'message' => $message]);

    }

    private function sanitizeSizeName($string)
    {
        $string = strtolower($string);
        $string = str_replace(' ', '', $string);
        $string = preg_replace("/[^a-zA-Z]+/", "", $string);
        return $string;
    }

    private function fileExists($file)
    {
        //$parsed = parse_url($file);
        $exists = Storage::disk($this->disk)->exists($file);
        return $exists;
    }

    private function getSizes($file)
    {
        $sizes = [];
        $basename = basename($file);
        $urlInfo = parse_url($file);
        $fullpath = str_replace('/storage/', '', $urlInfo['path']);
        $justPath = str_replace( $basename, '', $fullpath);

        $imageSizes = config('neutrino.media.image_sizes');

        foreach( $imageSizes as $key => $value ){
            $path = $justPath.'_'.$key.'/'.$basename;

            $url = Storage::disk(config('neutrino.storage.filesystem'))->url($path);
            if($url){
                $sizes[$key] = $url;
            }

        }

        $ogPath = $justPath.'_original/'.$basename;

        $url = Storage::disk(config('neutrino.storage.filesystem'))->url($ogPath);
        if($url){
            $sizes['original'] = $url;
        }

        unset($sizes['thumb']);

        return $sizes;
    }

    private function sanitizeFilename($filename)
    {
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
        $filename = preg_replace( '/[^a-z0-9.]+/', '-', strtolower( $filename ) );
        return $filename;
    }

    private function getFileType($filename)
    {

    }

}
