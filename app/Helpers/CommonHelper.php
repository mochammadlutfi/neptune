<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\AutoEncoder;
use Illuminate\Support\Str;

if (!function_exists('errorBag')) {
    /**
     * Example custom helper function.
     *
     * @param  string  $param
     * @return string
     */
    function errorBag($data)
    {
        return (object) collect($data)->map(function ($errors) {
            return $errors[0];
        })->toArray();
    }
}


if (!function_exists('uploadFile')) {
    /**
     * Example custom helper function.
     *
     * @param  string  $param
     * @return string
     */
    function uploadFile($file, $dest, $image = false)
    {
        // Generate unique filename
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
        $fileName .= '_' . uniqid();
        $fileName .= '.' . $file->getClientOriginalExtension();
        
        // Create destination path if not exists
        if (!Storage::disk('public')->exists($dest)) {
            Storage::disk('public')->makeDirectory($dest);
        }

        if ($image) {
            // Handle image file
            $imgFile = Image::read($file->getRealPath())
                ->encode(new AutoEncoder(quality: 90));
            $imgFileData = (string) $imgFile;
            
            // Save the file
            Storage::disk('public')->put($dest . '/' . $fileName, $imgFileData);
        } else {
            // Handle regular file
            Storage::disk('public')->putFileAs($dest, $file, $fileName);
        }

        // Return the relative path only
        return '/uploads/'.$dest . '/' . $fileName;
    }
}

if (!function_exists('deleteFile')) {
    /**
     * Example custom helper function.
     *
     * @param  string  $param
     * @return string
     */
    function deleteFile($file)
    {
        $cek = Storage::disk('public')->exists($file);
        if($cek)
        {
            Storage::disk('public')->delete($file);
        }
    }
}