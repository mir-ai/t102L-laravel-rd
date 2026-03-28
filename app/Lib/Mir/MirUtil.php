<?php namespace App\Lib\Mir;

use Carbon\Carbon;
use App\Models\User;
use App\Lib\CityUtil;
use Illuminate\Support\Str;
use App\Lib\Mir\MirDateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;

class MirUtil
{

    public static function get_my_user()
    {
        $my_user = Auth::user();

        if ($my_user) {
            return $my_user;
        }

        $my_user = User::orderBy('id')->first();

        if ($my_user) {
            return $my_user;
        }

        DB::table('users')->updateOrInsert([
            'email' => 'obata@mir-ai.co.jp',
        ],[
            'name' => 'ミライエ小幡',
            'password' => bcrypt('MmsM1ra1e'), 
            'role' => '10', 
        ]);

        $my_user = User::orderBy('id')->first();

        return $my_user;
    }


    public static function json_to_array($json_str_with_bom)
    {
        $json_str_without_bom = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json_str_with_bom);
        return json_decode($json_str_without_bom, true);
    }

    public static function has_permission(int $level, $cur_allowed_apps = '|ALERT=1|')
    {
        # |ALERT=12345| など
        if (!empty(Auth::user())) {
            $cur_allowed_apps = Auth::user()->allowed_apps;
        }

        $app_code = config('_env.APP_CODE');
        $level_str = '';

        # 与えられた level に応じた権限文字列を生成
        # 4 -> |ALERT=1234
        for ($i = 1; $i <= $level; $i++) {
            $level_str .= $i;
        }
        $test_allowed_apps = "|{$app_code}={$level_str}";

        if (strpos($cur_allowed_apps, $test_allowed_apps) !== false) {
            # ユーザーのDBの allowed_apps カラムに、十分な権限が記載されていた
            return True;
        } else {
            return False;
        }
    }

    // 新しい関数は最上部に作成すること。
    public static function error_abort($message)
    {
        self::logError($message);
        abort(513, $message);
    }

    public static function log($msg)
    {
        if (app()->runningInConsole()) {
            print "{$msg}\n";
        } else {
            print "{$msg}<br />\n";
        }
        logger($msg);
    }

    
    /**
     * logger()->alertを呼ぶが、一度書いたら $intervalSec は再出力しない。
     * Slackが毎分鳴ったりしないようにしたい。
     *
     * @param string $msg
     * @param integer $intervalSec
     * @param string $key あれば、重複チェックにこちらのキーを使う
     * @return void
     */
    public static function alertInterval(string $msg, int $intervalSec = 600, $key = ''): void
    { 
        if (! $key) {
            $key = md5($msg);
        }

        $cacheKey = "alert-interval-{$msg}";
        if (Cache::get($cacheKey)) {
            logger($msg);
            return;
        }

        self::logAlert("{$msg}  (suppress same message for {$intervalSec} sec.)");

        Cache::put($cacheKey, $intervalSec);
        return;
    }

    public static function host_prefix_of_uri($uri = '')
    {
        if (empty($uri)) {
            $uri =  config('_env.APP_URL');
        }
		$host = parse_url($uri, PHP_URL_HOST);
        $first_hosts = explode('.', $host);
        return $first_hosts[0];
    }

    //-------------------------------------------------------------------------------
    // 日付関数

    /**
     * 日付文字列をCarbonに解釈する。
     * 解釈不可の場合は現在の日付を返す
     *
     * @param string|null $dateStr
     * @param mixed $onError
     * @return Carbon
     */
    public static function parseDt(?string $dateStr): Carbon
    {
        if (empty($dateStr)) {
            return now();
        }

        try {
            return Carbon::parse($dateStr)->timezone('Asia/Tokyo');

        } catch (\Exception $e) {
            self::logAlert("MirUtil::parseDt parse error '{$dateStr}' {$e}");
            return now();
        }
    }

    /**
     * 日付を日本語的にフォーマットする(曜日をつけたいときに使う)
     * 
     * isoFormat
     * 
     * 'YY年M月D日(ddd) a h時m分' -> "25年8月11日(月) 午後 1時8分"
     * 'YYYY年MM月DD日(dddd) a hh時mm分' -> "2025年08月11日(月曜日) 午後 01時09分"
     * 
     * YYYY  2025
     * YY    25
     * Y     2025
     * 
     * MMM   8月
     * MM    08
     * M     8
     * 
     * DD    07
     * D     7
     *
     * dddd  木曜日
     * ddd   木
     * 
     * A     午後
     * a     午後
     * 
     * HH    13
     * H     13
     * h     1
     * 
     * mm    06
     * m     6
     * 
     * 
     * @param $dt
     * @param string $format
     * @return string
     */
    public static function isoFormat($dt, string $format = 'YY年M月D日(ddd) a h時mm分'): string
    {
        if (empty($dt)) {
            return '';
        }

        $lang_code = App::getLocale();

        App::setLocale('ja');
        Carbon::setLocale('ja');

        $formatted = $dt->isoFormat($format);
        $formatted = str_replace(['am', 'pm'], ['午前', '午後'], $formatted);
        $formatted = str_replace(['午前12'], ['午前0'], $formatted);
        $formatted = str_replace(['午後12'], ['午後0'], $formatted);

        if ($lang_code != 'ja') {
            App::setLocale($lang_code);
            Carbon::setLocale($lang_code);
        }
        
        return $formatted;
    }

    /**
     * 日付を日本語的にフォーマットする(令和をつけたいときに使う)
     * 
     * J : 元号
     * b : 元号略称
     * K : 和暦年(1年を元年と表記)
     * k : 和暦年
     * x : 日本語曜日(0:日-6:土)
     * E : 午前午後
     *
     * @param [type] $dt
     * @param string $format
     * @return string
     */
    public static function jpFormat($dt, string $format = 'JK年n月j日(x) Eg時i分'): string
    {
        return MirDateTime::jpFormat($dt, $format);
    }

    /**
     * 時刻のドロップダウン用に、
     * 午前0時、午前1時、午前2時、、、、午前11時
     * 午後0時、午後１時、午後２時、、、、午後11時
     * と時刻部分が日本語表記の連想配列を返す
     *
     * @return array
     */
    public static function jpHoursKv(): array
    {
        return [
            '00' => '午前0時',
            '01' => '午前1時',
            '02' => '午前2時',
            '03' => '午前3時',
            '04' => '午前4時',
            '05' => '午前5時',
            '06' => '午前6時',
            '07' => '午前7時',
            '08' => '午前8時',
            '09' => '午前9時',
            '10' => '午前10時',
            '11' => '午前11時',
            '12' => '午後0時',
            '13' => '午後1時',
            '14' => '午後2時',
            '15' => '午後3時',
            '16' => '午後4時',
            '17' => '午後5時',
            '18' => '午後6時',
            '19' => '午後7時',
            '20' => '午後8時',
            '21' => '午後9時',
            '22' => '午後10時',
            '23' => '午後11時',
        ];
    }

    /**
     * 日付の文字列を解釈して、それを別形式に変換し直す
     * 
     * dateStr: 20250811
     * format: YY年M月D日(ddd) a h時mm分 → "25年8月11日(月)"
     *
     * @param string|null $dateStr
     * @param string $format
     * @return string
     */
    public static function parseFormat(?string $dateStr, string $format = 'YY年M月D日(ddd) a h時mm分'): string
    {
        $dt = self::parseDt($dateStr);
        return self::isoFormat($dt, $format);
    }

    // 日付時刻を表示する。直近の場合、緑にハイライトする
    public static function hilightDt($dt, int $hilight_minutes = 21, string $hilight_class = 'text-success', $format = 'y/m/d H:i:s'): string
    {
        $htmls = [];
        $class = '';

        if (! $dt) {
            return '';
        }

        if ($dt > now()->subMinutes($hilight_minutes)) {
            $class = $hilight_class;
        }
        $htmls[] = "<span class=\"{$class}\">";
        $htmls[] = $dt?->format($format);
        $htmls[] = "</span>";

        return implode('', $htmls);
    }

    // 日付時刻とどのくらい前かを表示する。直近の場合、緑にハイライトする
    public static function hilightDtWithDiff($dt, int $hilight_minutes = 21, string $hilight_class = 'text-success', $format = 'y/m/d H:i:s'): string
    {
        $htmls = [];
        $class = '';

        if (! $dt) {
            return '';
        }

        if ($dt > now()->subMinutes($hilight_minutes)) {
            $class = $hilight_class;
        }
        $htmls[] = "<span class=\"{$class}\">";
        $htmls[] = $dt?->format($format);
        $htmls[] = '&nbsp(';
        $htmls[] = $dt?->diffForHumans();
        $htmls[] = ")</span>";

        return implode('', $htmls);
    }

    // 日付時刻と曜日を表示する。直近の場合、緑にハイライトする
    public static function hilightJpDt($dt, int $hilight_minutes = 21, string $hilight_class = 'text-success', $format = 'YY/MM/DD(ddd) HH:mm'): string
    {
        App::setLocale('ja');
        Carbon::setLocale('ja');

        if (! $dt) {
            return '';
        }

        $class = '';

        if ($dt > now()->subMinutes($hilight_minutes)) {
            $class = $hilight_class;
        }

        $formatted = $dt->isoFormat($format);
        $formatted = str_replace(['am', 'pm'], ['午前', '午後'], $formatted);
        $formatted = str_replace(['午前12', '午前 12'], ['午前0'], $formatted);
        $formatted = str_replace(['午後12', '午後 12'], ['午後0'], $formatted);

        $htmls = [];
        $htmls[] = "<span class=\"{$class}\">";
        $htmls[] = $formatted;
        $htmls[] = "</span>";

        return implode('', $htmls);
    }

    //-------------------------------------------------------------------------------
    // ファイル関数

    /**
     * storage 以下のパスを絶対パスに変換する
     * 
     * "mir_tmp/2501/999999/audio_678d141f9be51.mp3"
     * ->"/Users/obatasusumu/MIRAiE/laravel/300L-00-core-alert-v3/storage/app/mir_tmp/2501/999999/audio_678d141f9be51.mp3"
     *
     * @param string $appPath
     * @return string
     */
    public static function appPathToFullPath(string $appPath): string
    {
        return storage_path("app/private/{$appPath}");
    }

    /**
     * 絶対パスを storage/app パスに変換する
     * 
     * '/Users/obatasusumu/MIRAiE/laravel/300L-00-core-alert-v3/storage/app/mir_tmp/2501/999999/audio_678d141f9be51.mp3'
     * -> "mir_tmp/2501/999999/audio_678d141f9be51.mp3"
     *
     * @param string $fullPath
     * @return string
     */
    public static function fullPathToAppPath(string $fullPath): string
    {
        $path = str_replace(storage_path("app/private/"), '', $fullPath);
        return $path;
    }

    /**
     * ファイルサイズの数値をMBなどの単位に直す
     *
     * @param integer|null $size
     * @param integer $precision
     * @return string
     */
    public static function fileSizeForHuman(int|null $size, int $precision = 1): string
    {
        if (is_null($size)) {
            return '';
        }

        $size = $size ?? 0;
        for($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}

        return round($size, $precision).' '.['B','KB','MB','GB','TB','PB','EB','ZB','YB'][$i];
    }

    //-------------------------------------------------------------------------------
    // 配列・文字列操作関数

    /**
     * 連続した数字（時刻、個数、レベル）を下に配列を作成する
     * ウェブのフォームのドロップダウンを作成するのに便利
     *
     * @param integer $start
     * @param integer $end
     * @param integer $step
     * @param string|null $key_format
     * @param string|null $val_format
     * @param string|null $blank
     * @param integer $blank_pos
     * @return array
     */
    public static function range(int $start, int $end, int $step, ?string $key_format, ?string $val_format, ?string $blank = '', int $blank_pos = 0): array
    {
        $ret = [];
        if ($blank && $blank_pos == 0) {
            $ret[''] = $blank;
        }

        if ($step > 0) {
            for ($i = $start; $i <= $end; $i += $step) {
                if ($blank && $blank_pos == $i) {
                    $ret[''] = $blank;
                }

                $ret[sprintf($key_format, $i)] = sprintf($val_format, $i);
            }
        } else {
            for ($i = $start; $i >= $end; $i += $step) {
                if ($blank && $blank_pos == $i) {
                    $ret[''] = $blank;
                }
                $ret[sprintf($key_format, $i)] = sprintf($val_format, $i);
            }
        }

        return $ret;
    }

    /**
     * 配列を連結文字で接続する。間が空いている場合は詰める。
     * 
     * ['日本', '', '東京', '', '品川区']
     *        ↓
     * 日本 > 東京 > 品川区
     *
     * @param array $array
     * @param string $dlmt
     * @return string
     */
    public static function join(array $array, $dlmt = ' ＞ '): string
    {
        $array = array_filter($array, fn($q) => (bool)$q);
        return implode($dlmt, $array);
    }

    public static function cleanEmail(string|null $email): string
    {
        if (! $email) {
            return '';
        }

        // 全角を半角に
        $email = mb_convert_kana($email, 'as');
        $email = mb_ereg_replace("[‐‑–—―−ｰ]", "-", $email);
        $email = str_replace(['＠', '＋', '．', '＿'], ['@', '+', '.', '_'], $email);
        $email = strtolower($email);
        $email = trim($email);

        return $email;
    }

    public static function cleanPhone(string|null $phone): string
    {
        if (! $phone) {
            return '';
        }

        // 全角を半角に
        $phone = mb_convert_kana($phone, 'as');
        $phone = mb_ereg_replace("[‐‑–—―−ｰ]", "-", $phone);
        $phone = preg_replace('/\D/', '', $phone);
        $phone = trim($phone);

        return $phone;
    }

    /**
     * 定型文の分類と定型文の名称
     * 先頭に数字をいれることがあるので、それらは半角に統一して、
     * ソートが正しくされるようにしたい。
     *
     * @param string $str
     * @return string
     */
    public static function cleanTemplateName(string|null $str): string
    {
        if (! $str) {
            return '';
        }

        // n	「全角」数字を「半角」に変換します。
        // s	「全角」スペースを「半角」に変換します
        $str = mb_convert_kana($str, 'ns');

        // 全角 ＿ を半角に直す
        $str = str_replace('＿', '_', $str);

        return $str;
    }

    /**
     * 配信されるメッセージ本文（表示用）から有害な文字を取り除くなど
     *
     * @param string $str
     * @return string
     */
    public static function cleanTextBody(string|null $str): string
    {
        if (! $str) {
            return '';
        }

        $str = MirKanji::replaceMailBreakChars($str);

        return $str;
    }

    /**
     * 配信されるメッセージ本文（表示用）から有害な文字を取り除くなど
     *
     * @param string $str
     * @return string
     */
    public static function cleanSsmlBody(string|null $str): string
    {
        if (! $str) {
            return '';
        }

        $str = self::cleanTextBody($str);

        // SSML における有害文字 (< > " & ') を全角などに置き換える
        $str = str_replace(["<", ">", '"', '&', "'", 'no.'], ["＜", "＞", '”', '＆', '’', 'number'], $str);

        return $str;
    }

    public static function cleanUrl(string|null $str): string
    {
        if (! $str) {
            return '';
        }

        $replaces = [
            '＿' => '_',
            '．' => '.',
            '！' => '!',
            "’" => "'",
            '（' => '(',
            '）' => ')',
            '：' => ':',
            '／' => '/',
            '？' => '?',
            '。' => '.',
            '＃' => '#',
            '＠' => '@',
            '％' => '%',
            '＄' => '$',
            '＆' => '&',
            '＊' => '*',
            '＋' => '+',
            '，' => ',',
            '、' => ',',
            '；' => ';',
            '＝' => '=',
        ];

        // 全角を半角に
        $str = mb_convert_kana($str, 'as');

        // さまざまなハイフンを統一
        $str = mb_ereg_replace("[‐‑–—―−ｰ]", "-", $str);

        // 利用される可能性のある全角記号を半角に
        $str = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $str
        );

        // 小文字に
        $str = strtolower($str);

        // 前後のスペースを除去
        $str = trim($str);

        return $str;
    }

    /**
     * 文字列を英数字_-のみに変換する
     *
     * @param string|null $file_name
     * @return string
     */
    public static function cleanFileBaseName(string|null $file_name): string
    {
        if (empty($file_name)) {
            return '';
        }

        $file_name = $file_name ?? '';
        $file_name = Str::before($file_name, '.');
        $file_name = trim(mb_convert_kana($file_name, 'as', 'UTF-8'));
        $file_name = str_replace(' ', '', $file_name);
        $file_name = preg_replace('/[^0-9a-zA-Z\_\-]/', '', $file_name);

        if ($file_name == '') {
            $file_name = uniqid();
        }

        return $file_name;
    }
    
    /**
     * ファイル名の拡張子以外の部分を英数字_-のみに変換する
     *
     * @param string|null $org_file_name
     * @return string
     */
    public static function cleanFileName(string|null $org_file_name): string
    {
        if (empty($org_file_name)) {
            return '';
        }

        $ext = pathinfo($org_file_name, PATHINFO_EXTENSION); //拡張子のみ
        $filename_body = pathinfo($org_file_name, PATHINFO_FILENAME); //ファイル名のみ
        $filename_body = self::cleanFileBaseName($filename_body);
        $new_file_name = "{$filename_body}.{$ext}";

        //logger("clean_file_name {$org_file_name} -> {$new_file_name}");
        return $new_file_name;
    }

    /**
     * 文字列を一定の長さで配列に分割する
     *
     * @param string|null $text
     * @param integer $len
     * @return array
     */
    public static function explodeByLength(string|null $text, int $len = 60): array
    {
        $splits = [];
        $rest = $text ?? '';

        while (mb_strlen($rest) > $len) {
            $splits[] = mb_substr($rest, 0, $len);
            $rest = mb_substr($rest, $len);
        }

        if ($rest) {
            $splits[] = $rest;
        }

        return $splits;
    }

    // 画像のURLをふくむか
    public static function isImgUrl(string|null $url): bool
    {
        if (! str_starts_with($url, 'https://')) {
            return false;
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            return true;
        }

        if (str_starts_with($url, 'https://app.fastalert.jp/assets/')) {
            return true;
        }

        return false;
    }

    // 動画のURLをふくむか
    public static function isMovieUrl(string|null $url): bool
    {
        if (! str_starts_with($url, 'https://')) {
            return false;
        }

        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        [$ext, $d] = explode('?', $ext . '?');

        if (in_array($ext, ['mp4'])) {
            return true;
        }

        return false;
    }

    // 電話番号をマスクする
    public static function mask_phone($personal_info, $force = false)
    {
        if (config('_env.UI_MASK_OFF') == 'Y') {
            if (! $force) {
                return $personal_info;
            }
        }

        if (empty($personal_info)) {
            return $personal_info;
        }

        $chars = preg_split("//u", $personal_info, -1, PREG_SPLIT_NO_EMPTY);

        $result = '';
        $i = 0;

        foreach ($chars as $chr) {
            if ($chr == '-') {
                $result .= $chr;
                // $i のカウントをスキップ
                continue;
            } else if (2 <= $i && $i <= 7) {
                $result .= '*';
            } else {
                $result .= $chr;
            }
            $i++;
        }
        return $result;
    }

    // メールアドレスをマスクする
    public static function mask_email($email, $force = false)
    {
        if (config('_env.UI_MASK_OFF') == 'Y') {
            if (! $force) {
                return $email;
            }
        }

        // 開発者の場合、個人情報はマスク
        if (empty($email)) {
            return $email;
        }

        $chars = preg_split("//u", $email, -1, PREG_SPLIT_NO_EMPTY);

        $result = '';
        $i = 0;

        foreach ($chars as $chr) {
            if ($chr == '@' || $chr == '.') {
                $result .= $chr;
                // $i のカウントをスキップ
                continue;
            } else if (2 <= $i && $i <= 8) {
                $result .= '*';
            } else {
                $result .= $chr;
            }
            $i++;
        }

        return $result;
    }

    // 名前をマスクする
    public static function mask_name($personal_info, $force = false)
    {
        if (config('_env.UI_MASK_OFF') == 'Y') {
            if (! $force) {
                return $personal_info;
            }
        }

        if (empty($personal_info)) {
            return $personal_info;
        }

        $chars = preg_split("//u", $personal_info, -1, PREG_SPLIT_NO_EMPTY);

        $result = '';
        $i = 0;
        $from = 1;
        $to = 2;
        if ($c = mb_strpos($personal_info, '】')) {
            $from = $c + 2;
            $to = $c + 3;
        }

        foreach ($chars as $chr) {
            if ($chr == ' ' || $chr == '　') {
                $result .= $chr;
                // $i のカウントをスキップ
                continue;
            } else if ($from <= $i && $i <= $to) {
                $result .= '＊';
            } else {
                $result .= $chr;
            }
            $i++;
        }

        return $result;
    }

    // 電話番号をハイフン付きで表現
    public static function phone_to_human($phone)
    {
        return MirPhone::to_human($phone);
    }
    
    /**
     * 81から始まる番号形式にする 819012345678
     * Twilioに送信する際の形式
     *
     * @param string|null $phone_no
     * @return string
     */
    public static function to8190(string|null $phone_no): string
    {
        $stripped = MirPhone::_strip($phone_no);

        if (empty($stripped)) {
            return '';
        }

        return "81{$stripped}";
    }

    /**
     * 090から始まる番号形式 09012345678
     * Faximoに送信する際の形式
     *
     * @param string|null $phone_no
     * @return void
     */
    public static function to090(string|null $phone_no): string
    {
        $stripped = MirPhone::_strip($phone_no);

        if (empty($stripped)) {
            return '';
        }

        return "0{$stripped}";
    }


    /**
     * 文字列中からメールアドレス部分を除去する
     *
     * @param string|null $text
     * @return string
     */
    public static function removeEmail(string|null $text): string
    {
        $text = mb_ereg_replace(
            "[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}", 
            '', 
            $text ?? ''
        );

        return $text;
    }

    /**
     * 文字列中からURL部分を除去する
     *
     * @param string|null $text
     * @return string
     */
    public static function removeUrl(string|null $text): string
    {
        $text = mb_ereg_replace(
            "http[s]*://[\w!\?/\+\-_~=;\.,\*&@#\$%\(\)'\[\]]+", 
            '', 
            $text ?? ''
        );
        return $text;
    }

    /**
     * メールの登録リンクは除去する
     *
     * @param [type] $text
     * @return string
     */
    public static function remove_regist_line($text): string
    {
        $regist_words = [
            'URL',
            'アクセス',
            'スマートフォン',
            'パソコン',
            'フィーチャーフォン',
            'リンク',
            '携帯電話',
            '空メール',
            'メルマガ',
            '登録変更',
            '購読',
            '配信',
            'ＵＲＬ',
            '===',
            '---',
        ];

        // 登録変更・解除を案内する行は削除
        $lines = [];
        foreach (explode("\n", $text) as $line) {
            $marker = '|REG_WORD|';
            $check = str_replace($regist_words,  $marker, $line);
            if (str_contains($check, $marker)) {
                break;
            }
            $lines[] = $line;
        }
        $regist_removed = implode("\n", $lines);
        
        return $regist_removed;
    }

    // 文字列を一定の長さで分割する
    public static function split_by_len($long_text, $max_mb_len = 60)
    {
        $splits = [];
        $rest = $long_text;

        while (mb_strlen($rest) > $max_mb_len) {
            $splits[] = mb_substr($rest, 0, $max_mb_len);
            $rest = mb_substr($rest, $max_mb_len);
        }

        if ($rest) {
            $splits[] = $rest;
        }

        return $splits;
    }

    // テキスト中のURLを リンク可能な href に変換する
    public static function tel2href($text)
    {
        $pattern = '/(0[0-9-]{10,11})/';
        $replace = '<a href="tel:$1">$1</a>';
        $ret = preg_replace($pattern, $replace, $text);
        return $ret;
    }

    // テキスト中のURLを リンク可能な href に変換する
    public static function url2href($text, $target = '_new')
    {
        $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/';
        $replace = '<a href="$1">$1</a>';
        if ($target) {
            $replace = '<a href="$1" target="__TARGET__">$1</a>';
        }
        $ret = preg_replace( $pattern, $replace, $text);
        $ret = str_replace('__TARGET__', $target, $ret);
        return $ret;
    }

    /**
     * condsの中身に有効な値を含む配列があった場合、それを|区切りの文字列にする
     * 配列が無効な場合は空文字列にする。
     *
     * @param array $conds
     * @return array
     */
    public static function stringifyCondsElements(array $conds): array
    {
        // 
        foreach ($conds as $k => $v) {
            if (is_array($v)) {
                if (array_filter($v)) {
                    $v = implode('|', $v);
                } else {
                    $v = '';
                }
            }
            $conds[$k] = $v;
        }
        return $conds;
    }    

    // 安全な復号化
    public static function decrypt($encrypted, $default = '')
    {
        if (! $encrypted) {
            return $default;
        }
        
        try {
            return decrypt($encrypted);
        } catch (DecryptException $e) {
            //return $encrypted ?: $default;
            return $default;
        }
    }

    // 日本語を一定文字で分割する
    public static function wtrim($str, $len)
    {
        if (is_null($str)) {
            // $request->val でnull のものは null のままわたす
            // table の index で null を期待しているものもあるので
            // '' を渡すと unique 一意制約エラーになったりする
            return null;
        }
        
        if (is_array($str)) {
            return $str;
        }
        
        return mb_substr(trim($str), 0, $len, 'UTF-8');
    }
    // int を 0-255に収める
    public static function tinyint($v)
    {
        $v = intval($v);
        return ($v < 255) ? $v : 255;
    }

    /**
     * ハイフン記号を統一する処理
     * $string 中の全さまざまなハイフンを統一した記号 - に置き換える
     *
     * @param string|null $string
     * @return void
     */
    public static function unifyHyphens(string|null $string)
    {
        return str_replace(['ー','ー','‐','‑','–','—','―','−','ｰ', '－'], '-', $string);
    }  
    
    // UUIDv4を作成する
    public static function uuid_v4()
    {
        $pattern = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
    
        $chars = str_split($pattern);

        foreach ($chars as $i => $char) {
            if ($char === 'x') {
                $chars[$i] = dechex(random_int(0, 15));
            } elseif ($char === 'y') {
                $chars[$i] = dechex(random_int(8, 11));
            }
        }

        return implode('', $chars);
    }    

    /**
     * HTML上で消されない空白記号を指定回数分追加する。
     *
     * @param integer $times
     * @return void
     */
    public static function addWebSpace(int $times = 1)
    {
        return str_repeat('&emsp;', $times);
    }

    /**
     * 文字列の右側から指定した文字数を取り出す方法
     *
     * @param string|null $str
     * @param integer $num
     * @param string $encoding
     * @return void
     */
    public static function mb_right(?string $str, int $num, $encoding = 'UTF-8')
    {
        return mb_substr($str, $num * -1, $num, $encoding); 
    } 

    /**
     * 指定桁数の乱数を生成する
     *
     * @param integer $length
     * @return string
     */
    public static function randomNumberWithSpecifiedLength($length = 13): string
    {
        $max = pow(10, $length) - 1;                    // コードの最大値算出
        $rand = random_int(0, $max);                    // 乱数生成
        $code = sprintf('%0'. $length. 'd', $rand);     // 乱数の頭0埋め
        return $code;
    }

    // 配列を文字列として出力する
    public static function printArray(array|null $array): void
    {
        print json_encode($array, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }

    // パーセント表示を作る
    public static function percent($numerator, $denominator, $if_error = '')
    {
        if ($denominator == 0) {
            return $if_error;
        }

        return ((int)(($numerator / $denominator) * 100));
    }

    // 値があれば、number_format
    public static function number_format($cnt)
    {
        if (empty($cnt)) {
            return '';
        }

        return number_format(intval($cnt));
    }

    /**
     * ログなどの日付指定欄で、始まりの日時を返す
     * 
     * 日付指定がなければ、現在時刻-2時間の日の0時を指定。
     * 日付指定がなければ、その日の終りを指定。
     *
     * @param string|null $from_str
     * @return Carbon
     */
    public static function fromDt(?string $from_str): Carbon
    {
        if (! $from_str) {
            return now()->subHours(2)->startOfDay();
        }

        $dt = self::parseDt($from_str, today())->startOfDay();

        //logger("fromDt from_str='{$from_str}' from_dt=" . $dt);
        return $dt;
    }

    /**
     * ログなどの日付指定欄で、終わりの日時を返す
     * 
     * 日付指定がなければ、本日の終わりを指定。
     * 日付指定があれば、その日の終りを指定。
     *
     * @param string|null $to_str
     * @return Carbon
     */
    public static function toDt(?string $to_str): Carbon
    {
        if (! $to_str) {
            return today()->addDay(1)->startOfDay();
        }

        $dt = self::parseDt($to_str, today())->addDay(1)->startOfDay();
        //logger("toDt to_str='{$to_str}' to_dt=" . $dt);
        return $dt;
    }    

    // 連想配列のキーの文字列ある文字列で始まり、- で区切られているもの後半部分を配列で返す
    // フォームのチェックで利用者一覧やグループ一覧を取り扱う想定
    // IN:
    //      getSelectedIdsFromRequests([
    //          'client-1' => 1,
    //          'client-3' => 1,
    //          'client-4' => 1,
    //      ], 'client-');
    // OUT:
    //      [1,3,4]
    public static function getSelectedIdsFromRequests(array $requests, ?string $keyPrefix)
    {
        $values = [];
        foreach ($requests as $k => $v) {
            if (str_starts_with($k, $keyPrefix)) {
                $v = Str::after($k, $keyPrefix);
                $values[] = $v;
            }
        }

        return $values;
    }

    // ドロップダウンの先頭に「選択」（または $prompt ）をつける
    public static function addPrompt(array $options, ?string $prompt = '選択')
    {
        $options = array_reverse($options, true);
        $options[''] = $prompt;
        $options = array_reverse($options, true);

        return $options;
    }

    // 改行抜きでログ出力用の行を作る
    public static function line1(string $text, array $array = [])
    {
        $array_line = (! $array) ? 
            '' :
            (is_array($array) ? json_encode($array, JSON_UNESCAPED_UNICODE) : $array);

        return str_replace(["\n", "\r"], '\n', "{$text} {$array_line}");
    }
    
    // 改行抜きで debug
    public static function logDebug(string $text, array $array = [])
    {
        $message = self::line1($text, $array);
        logger($message);
    }

    // 改行抜きで info
    public static function logInfo(string $text, array $array = [])
    {
        $message = self::line1($text, $array);
        logger($message);
    }

    /**
     * アラートログを出力する。市区町村コードと環境を付加する。
     *
     * @param string $text
     * @param array $array
     * @return void
     */
    public static function logAlert(string $text, $array = [])
    {
        $app = config('app.name');
        $env = config('app.env');
        $message = self::line1($text, $array);

        logger()->alert("[{$app}@{$env}] {$message}");
    }

    /**
     * エラーログを出力する。市区町村コードと環境を付加する。
     *
     * @param string $text
     * @param array $array
     * @return void
     */
    public static function logError(string $text, $array = [])
    {
        $app = config('app.name');
        $env = config('app.env');
        $message = self::line1($text, $array);

        logger()->error("[{$app}@{$env}] {$message}");
    }

    /**
     * エラーログを出力する。市区町村コードと環境を付加する。
     *
     * @param string $text
     * @param array $array
     * @return void
     */
    public static function logAbort(string $text, $array = [])
    {
        $app = config('app.name');
        $env = config('app.env');
        $message = self::line1($text, $array);

        logger()->error("[{$app}@{$env}] {$message}");
        abort(513, $message);
    }

    /**
     * ローカル環境の場合、NGROKによるローカルURLを作成する
     *
     * @param string $url
     * @return string
     */
    public static function myCallbackUrl(string $url): string
    {
        if (! app()->isLocal()) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            $query = "?{$query}";
        }

        $local_ngrok_host = config('_env.LOCAL_NGROK_ENDPOINT');

        $local_url = "{$local_ngrok_host}{$path}{$query}";

        return $local_url;
    }

    /**
     * タグの値を取得する
     * 
     * １行で記載されているタグのみ有効
     * ネスト不可
     * 
     * <Title>気象警報・注意報（Ｈ２７）</Title>
     *        ↓
     * 気象警報・注意報（Ｈ２７）
     *
     * @param string $line
     * @param string $tag
     * @return string
     */
    public static function getTagValue(string $line, string $tag): string
    {
        if (! str_contains($line, $tag)) {
            return '';
        }

        $start_tag = "<{$tag}>";
        $end_tag = "</{$tag}>";
        
        $pattern = "@{$start_tag}(.*?){$end_tag}@";

        if (preg_match_all(
            $pattern,
            $line,
            $result,
            PREG_SET_ORDER
        )) {
            $v = $result[0][1] ?? '';
            $v = trim($v);
            $v = strip_tags($v);
            return $v;
        };

        return '';
    }  
    
    /**
     * １分毎のバッチを実行する際に、今バッチを終了すべき時間を取得する。
     * 現在の分数-n秒の時刻のオブジェクトを返す
     * 
     * 例: 12時13分10秒に実行すると、12時13分55秒を返す。
     */
    public static function getEndOfThisMinute(int $sub_seconds = 5, $dt = null)
    {
        if (! $dt) {
            $dt = now();
        }

        $next_minute_str = $dt->copy()->addMinute(1)->format('Y-m-d H:i:00');

        $dt2 = Carbon::parse($next_minute_str)->subSeconds($sub_seconds);
        
        return $dt2;
    }

}
