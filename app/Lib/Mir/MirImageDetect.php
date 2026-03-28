<?php namespace App\Lib\Mir;

use Intervention\Image\Facades\Image;

/**
 * 画像の種別を判別する
 */
class MirImageDetect
{
    public static function guessExtention($content): string
    {
        // Guess extention
        $image_info = getimagesizefromstring($content);
        $extension = 'JPEG';
        if ($image_info !== false) {
            // The mime type will be something like 'image/jpeg'
            $mime_type = $image_info['mime']; 
            
            // Extract the extension from the mime type (gets 'jpeg' from 'image/jpeg')
            $extension = substr(strrchr($mime_type, '/'), 1); 
        }

        return $extension;
    }
}
