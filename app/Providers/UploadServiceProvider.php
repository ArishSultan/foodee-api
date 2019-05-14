<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider
{

    public static function upload($request, $product, $folder)
    {
        global $filename;
        $file = $request->file('photo');
        $allowedFileExtension=['jpg', 'png'];
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $check=in_array($extension,$allowedFileExtension);
            if($check)
            {
//                $filename = $file->store('media/'.$folder.'/'.$product->id);
                $filename = time().".".$extension;
                $file->storeAs('media/'.$folder.'/'.$product->id, $filename);

                return $filename;
            }
    }

    public static function multiUploads($request, $folder)
    {
        global $array;
        $array = [];
        $files = $request->file('photos');
        foreach($files as $file){
            $allowedFileExtension=['jpg', 'png', 'gif', 'jpeg'];
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $check=in_array($extension,$allowedFileExtension);
            if($check)
            {
//                $filename = $file->store('media/'.$folder.'/'.$product->id);
                $filename = time().".".$extension;
                $file->storeAs('media/'.$folder, $filename);

                array_push($array, $filename);

            }
        }
        return implode(",", $array);
    }

}
