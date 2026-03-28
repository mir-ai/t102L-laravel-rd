<?php namespace App\Lib\Mir;

use Intervention\Image\Facades\Image;

/**
 * 画像をリサイズする
 */
class MirImageResize
{
    public static function resize(string $local_abs_path, int $size = 300)
    {
        $image = Image::make($local_abs_path)->orientate();

        # ex) width = 800, height = 500
        $width = $image->width();
        $height = $image->height();

        // ex) 'image.jpg' -> 'jpg'
        $ext = pathinfo($local_abs_path, PATHINFO_EXTENSION);

        // 'image.jpg' -> 'image-300.jpg'
        $new_filename = $local_abs_path;
        $new_filename = str_replace(".{$ext}", '', $new_filename);
        $new_filename = "{$new_filename}-{$size}.{$ext}";

        // 縦横大きい方を、sizeに合わせる
        if ($width > $height) {
            $image = $image->widen($size)->save($new_filename);
        } else {
            $image = $image->heighten($size)->save($new_filename);
        }

        // 新しいファイル名を返却
        return $new_filename;
    }
}
