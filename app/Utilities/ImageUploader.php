<?php
namespace App\Utilities ;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class ImageUploader 
{
    public static function upload($image,$path,$diskType = 'public_storage')
    {
        //$fullPath = $path  . '_' . $image->getClientOriginalName();
        Storage::disk($diskType)->put($path,File::get($image)); 
    } 

    public static function uploadMany(array $images,$path,$diskType = 'public_storage')
    {
        $imagesPath = [] ;
        foreach($images as $key => $image)
        {
          
           $fullPath = $path . $key . '_' . $image->getClientOriginalName();
           self::upload($image,$fullPath,$diskType);
           //Storage::disk($diskType)->put($fullPath,File::get($image)); 
           $imagesPath += [$key => $fullPath] ;
        }

        return $imagesPath ;
        
    }
}