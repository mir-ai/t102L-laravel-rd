<?php namespace App\Lib\Mir;

use Exception;
use Carbon\Carbon;
use App\Lib\Mir\MirUtil;
use Illuminate\Http\File;
use App\Lib\Mir\MirTmpFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image; // v3のFacadeを使用

class MirS3V2 {

    // 1つの画像をリサイズしてS3にファイルを保存する
    public static function saveSingleImageToS3($abs_path, $ext, $mode = '', $width = 1200)
    {
        // Imageの作成
        $image = Image::read($abs_path);

        // 一時保存先の作成
        $local_img_l_path = MirTmpFile::make_tmp_abs_path('image', $ext);

        if ($mode == 'scale') {
            // サイズ調整して保存
            $image->scale(width: $width);
        }

        if ($mode == 'crop') {
            // 中央部分を長方形に切り抜き
            $w = $image->width;
            $h = $image->height;

            $x = ($w - $width) / 2;
            $y = ($h - $width) / 2;

            $image->crop($width, $width, $x, $y);
        }
        
        $image->save($local_img_l_path);

        // S3にアップロード
        $yymm = now()->format('ym');
        $s3_prefix = "uploaded_images/$yymm";

        $path = Storage::disk('s3')->putFile(
            $s3_prefix,
            new File($local_img_l_path),
            'public'
        );
        $img_url = str_replace('//', '/', config('_env.S3_PREFIX') . '/' . $path);
        logger()->info(">> {$img_url}");

        MirTmpFile::safe_unlink($local_img_l_path);

        return $img_url;
    }

    // 2種類のサムネイル付きでS3にファイルを保存する
    public static function save_image_to_s3($file, $width_s = 240, $width_l = 1024)
    {
        // Imageの作成
        $image = Image::read($file->getRealPath());


        $ext = $file->guessClientExtension();
        $local_img_s_path = MirTmpFile::make_tmp_abs_path('image', $ext);
        $local_img_l_path = MirTmpFile::make_tmp_abs_path('image', $ext);

        $yymm = now()->format('ym');

        $image->scale(width: $width_l);
        $image->save($local_img_l_path);

        $image->scale(width: $width_s);
        $image->save($local_img_s_path);

        $s3_prefix = "uploaded_images/$yymm";
        $path = Storage::disk('s3')->putFile(
            $s3_prefix,
            new File($local_img_s_path),
            'public'
        );
        $s3_img_s_url = str_replace('//', '/', config('_env.S3_PREFIX') . '/' . $path);
        logger()->info(">> {$s3_img_s_url}");

        $path = Storage::disk('s3')->putFile(
            $s3_prefix,
            new File($local_img_l_path),
            'public'
        );
        $s3_img_l_url = str_replace('//', '/', config('_env.S3_PREFIX') . '/' . $path);
        logger()->info(">> {$s3_img_l_url}");

        MirTmpFile::safe_unlink($local_img_s_path);
        MirTmpFile::safe_unlink($local_img_l_path);

        return [$s3_img_l_url, $s3_img_s_url];
    }

    public static function save_audio_to_s3($file, $reserve_file = false) {
        ini_set("memory_limit", "400M");

        $abs_local_path = $file->getRealPath();
        $ext = $file->guessClientExtension();

        $yymm = now()->format('ym');

        $s3_prefix = "uploaded_audios/$yymm";
        $path = Storage::disk('s3')->putFile(
            $s3_prefix,
            new File($abs_local_path),
            'public'
        );
        $s3_audio_url = Storage::disk('s3')->url($path);
        logger()->info(">> {$s3_audio_url}");

        return $s3_audio_url;
    }    
    
    // Upload local file to S3.
    public static function upload(string $local_abs_path, string $remote_rel_path, $remote_basepath = '') : string
    {
        $retry_count = 3;

        logger("MirS3::upload(local_abs_path='{$local_abs_path}', remote_basepath = '{$remote_basepath}' remote_rel_path='{$remote_rel_path}')");

        for ($i = 0; $i < $retry_count; $i++) {
            try {
                if ($remote_rel_path) {
                    $s3_path = Storage::disk('s3')->putFileAs(
                        $remote_basepath,
                        new File($local_abs_path),
                        $remote_rel_path,
                        'public'
                    );
                } else {
                    $s3_path = Storage::disk('s3')->putFile(
                        $remote_basepath,
                        new File($local_abs_path),
                        'public'
                    );
                }

                if ($s3_path === false) {
                    logger()->info("Failed to upload to S3: '$local_abs_path' -> remote_basepath={$remote_basepath}, remote_rel_path={$remote_rel_path}");
                } else {
                    $remote_url = config('_env.CLOUD_FRONT_PREFIX', '') . "/" . $s3_path;
                    
                    //logger("MmsS3:upload('$remote_basepath', '$remote_rel_path') -> '{$s3_path}'");
                    logger()->info(">> {$remote_url}");
                    return $remote_url;
                }

            } catch (Exception $e) {
                logger()->info("S3 upload failed to upload s3 {$e}");
            }
        }

        MirUtil::error_abort("Abort S3 upload. Failed {$retry_count} retry. [{$local_abs_path} -> basepath={$remote_basepath} remote_relpath={$remote_rel_path}]");

        return '';
    }

    #public static function download($s3_bucket, $s3_filename) : string
    public static function download($abs_source_url, $message_id = 0, $ext_default = '') : string
    {
        if (! $abs_source_url) {
            return '';
        }

        $ext = pathinfo($abs_source_url, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = $ext_default;
        }
        $content = file_get_contents($abs_source_url);

        $local_abs_path = MirTmpFile::save('download/', $ext, $content);
        logger()->info("[M={$message_id}] s3 download [{$abs_source_url} -> {$local_abs_path}]");

        return $local_abs_path;
    }

    /**
     * リモートディレクトリからダウンロードする。
     * 一度ダウンロードしたファイルはキャッシュしておき、次回以降また利用する。
     * httpで始まっていなかったらそのまま返す
     *
     * @param string $remoteUrl
     * @param bool $forceDownload
     * @return string $appPath
     */
    public static function cachedRemoteDownloader(string $remoteUrl, bool $forceDownload = false): string
    {
        $save_key = self::urlToUniqString($remoteUrl);
        $cache_dir = 'audio_cache';

        $appPath = "{$cache_dir}/{$save_key}";
        if (Storage::exists($appPath)) {
            logger("found from cache.");
            if (! $forceDownload) {
                return $appPath;
            }
        }

        $localAbsPath = self::download($remoteUrl);

        if (! $localAbsPath) {
            return '';
        }

        $file = new File($localAbsPath);

        Storage::disk('local')->putFileAs($cache_dir, $file, $save_key);

        return $appPath;
    }

    /**
     * URLを一意にファイル名に変換する
     *
     * @param string $remoteUrl
     * @return string
     */
    public static function urlToUniqString(string $remoteUrl): string
    {
        $pattern = '/[^a-zA-Z0-9\_\.\-]/';
        $replacement = '_';
        $sujbect = $remoteUrl;

        $replaced = preg_replace($pattern, $replacement, $sujbect);

        return $replaced;
    }
        

}
