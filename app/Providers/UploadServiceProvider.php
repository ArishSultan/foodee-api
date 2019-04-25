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
                $filename = $file->store('media/'.$folder);
                $filename = time().$file->getClientOriginalName();
                return $filename;
            }
    }

}
