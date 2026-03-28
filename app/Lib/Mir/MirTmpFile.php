<?php namespace App\Lib\Mir;

use Illuminate\Support\Facades\Storage;

class MirTmpFile
{
    /**
     * 一時ファイルに保存し、保存先の絶対パス名を返す
     * save('mp3/intl_', '.mp3', $content)
     *
     * @param string $prefix storage/app 以下のディレクトリ、ファイル名先頭部分。/を含んでも良い。直後にuniqid()がつく。
     * @param string $ext 拡張子。.は不要
     * @param mixed $content テキストまたはバイナリのファイルコンテンツ
     * @return string 保存された絶対パス
     */
    public static function save(string $prefix, string $ext, mixed $content): string
    {
        return self::save_and_get_abs_path($prefix, $ext, $content);
    }

    /**
     * 一時ファイルに保存し、保存先の絶対パス名を返す
     * save('mp3/intl_', '.mp3', $content)
     *
     * @param string $prefix storage/app 以下のディレクトリ、ファイル名先頭部分。/を含んでも良い。直後にuniqid()がつく。
     * @param string $ext 拡張子。.は不要
     * @param mixed $content テキストまたはバイナリのファイルコンテンツ
     * @return string 保存された絶対パス
     */    
    public static function save_and_get_abs_path(string $prefix, string $ext, mixed $content): string
    {
        $local_rel_path = self::save_and_get_app_path($prefix, $ext, $content);
        $local_abs_path = storage_path("app/private/{$local_rel_path}");
        return $local_abs_path;
    }

    /**
     * 一時ファイルに保存し、保存先の相対パス名を返す
     * save('mp3/intl_', '.mp3', $content)
     *
     * @param string $prefix storage/app 以下のディレクトリ、ファイル名先頭部分。/を含んでも良い。直後にuniqid()がつく。
     * @param string $ext 拡張子。.は不要
     * @param mixed $content テキストまたはバイナリのファイルコンテンツ
     * @return string 保存された絶対パス
     */        
    public static function save_and_get_app_path(string $dir, string $ext, mixed $content): string
    {
        $tmpAppPath = self::newAppPath($dir, $ext);

        $isSuccess = Storage::disk('local')->put(
            $tmpAppPath,
            $content
        );

        if (! $isSuccess) {
            MirUtil::logAlert("[TmpAppFileHelper] save failed '{$tmpAppPath}'");
            return '';
        }

        // logger("TmpAppFileHelper::save({$tmpAppPath})");

        return $tmpAppPath;
    }

    /**
     * ユニークな一時ファイル名を storage/app 以下に作成し、app/ 以下のパス名を返す
     *
     * @param string $dir
     * @param string $ext
     * @return string $tmpStoragePath
     */
    public static function newAppPath(string $dir, string $ext): string
    {
        $tmpAppPath = self::newAppPathString($dir, $ext);

        // mkdirもしてあげる
        $uniqid = uniqid();
        $mkdirAppPath = self::newAppPathString($dir, $uniqid);
        $is_success = Storage::disk('local')->put($mkdirAppPath, '1');
        if ($is_success) {
            Storage::disk('local')->delete($mkdirAppPath);
        }

        return $tmpAppPath;
    }

    public static function make_tmp_rel_path(string $dir, string $ext): string
    {
        return self::newAppPath($dir, $ext);
    }    

    /**
     * ユニークな一時ファイル名を storage/app 以下に作成し、フルパスを返す
     *
     * @param string $dir
     * @param string $ext
     * @return string $tmpStoragePath
     */
    public static function newFullPath(string $dir, string $ext): string
    {
        $newAppPath = self::newAppPath($dir, $ext);
        $newFullPath = storage_path("app/private/{$newAppPath}");

        return $newFullPath;
    }

    public static function make_tmp_abs_path(string $dir, string $ext): string
    {
        return self::newFullPath($dir, $ext);
    }

    /**
     * 一時ファイル名を作成する
     *
     * @param string $dir
     * @param string $ext
     * @return string
     */
    private static function newAppPathString(string $dir, string $ext): string
    {
        // $prefix はディレクトリとファイル名先頭が混在
        $yymm = now()->format('ym');
        $dir = rtrim($dir, '/');
        $uniqid = uniqid();
        $ext = str_replace('.', '', $ext);

        $newAppPath = "mir_tmp/{$yymm}/{$dir}/{$uniqid}.{$ext}";
        return $newAppPath;        
    }

    /**
     * ユニークな一時ディレクトリを storage/app 以下で作成する
     *
     * @param string $dir
     * @return string $tmpStoragePath
     */
    public static function newDir(): string
    {
        $tmpAppPath = self::newAppPath(uniqid(), '');
        $tmpAppDir = pathinfo($tmpAppPath, PATHINFO_DIRNAME);

        return $tmpAppDir;
    }

    /**
     * 絶対パスで指定した一時ファイルを削除する。
     * ファイルが存在しなくても削除可能。
     *
     * @param ?string $unlink_abspath
     * @return bool
     */
    public static function safe_unlink(?string $unlink_abspath): bool
    {
        return self::safe_unlink_abs_path($unlink_abspath);
    }

    /**
     * 絶対パスで指定した一時ファイルを削除する。
     * ファイルが存在しなくても削除可能。
     *
     * @param ?string $abs_path
     * @return bool
     */
    public static function safe_unlink_abs_path(?string $abs_path): bool
    {
        if ($abs_path && file_exists($abs_path)) {
            //logger("[DEL] {$unlink_abspath}");
            unlink($abs_path);
            return true;
        }  
        
        return false;
    }

    /**
     * 絶対パスで指定した一時ファイルを削除する。
     * ファイルが存在しなくても削除可能。
     *
     * @param ?string $app_path
     * @return bool
     */
    public static function safe_unlink_app_path(?string $app_path): bool
    {
        $abs_path = storage_path("app/private/{$app_path}");
        return self::safe_unlink_abs_path($abs_path);
    }
}
