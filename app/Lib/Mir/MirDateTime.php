<?php

namespace App\Lib\Mir;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Exception;

/**
 * 日付操作関数	
 */
class MirDateTime
{
    private static $redisTravelTimeKey = 'Travel-time-';

    /**
     * 現在時刻を取得。トラベル時刻が設定されていたらトラベル時刻を返す。
     *
     * @return Illuminate\Support\Carbon
     */
    public static function now(): object
    {
        if (app()->environment('production')) {
            return now();
        }

        $travelTime = self::getTravelTime();

        if (empty($travelTime)) {
            return now();
        }

        return $travelTime;
    }

    /**
     * トラベル時間を設定する。以降 $aliveSec 秒間は、now()を取るとトラベル時刻を返すようになる
     * テスト用
     *
     * @param Carbon $dt
     * @param integer $aliveSec
     * @return void
     */
    public static function travelTo($dt, $aliveSec = 60): void
    {
        Cache::put(self::$redisTravelTimeKey, $dt, $aliveSec);

        return;
    }

    /**
     * トラベル時間を解除する。
     *
     * @return void
     */
    public static function travelBack(): void
    {
        Cache::forget(self::$redisTravelTimeKey);

        return;

    }

    /**
     * トラベルが設定されていたらトラベル時間を返す。設定されていなければ空を返す。
     *
     * @return Illuminate\Support\Carbon
     */
    public static function getTravelTime(): Object
    {
        return Cache::get(self::$redisTravelTimeKey, now());
    }    

    public static function getDateAssocByRange($fromYmd, $toYmd, $desc = false): array
    {
        $fromDt = MirUtil::parseDt($fromYmd, today()->subMonths(1));
        $toDt = MirUtil::parseDt($toYmd, today());

        $dateAssoc = [];
        $curDt = $fromDt;
        while($curDt <= $toDt) {
            $yymmdd = $curDt->format('Y-m-d');
            $dateAssoc[$yymmdd] = $curDt->copy();
            $curDt->addDay();
        }

        if ($desc) {
            krsort($dateAssoc);
        }
        return $dateAssoc;
    }

    public static function getFirstDtOfFiscalYear($dt = null)
    {
        if (empty($dt)) {
            $dt = now();
        }

        // 年度の最初の日をとる
        $dt = $dt->subMonths(3)->startOfYear()->addMonths(3);
        return $dt;
    }


    /** 元号用設定
     * 日付はウィキペディアを参照しました
     * http://ja.wikipedia.org/wiki/%E5%85%83%E5%8F%B7%E4%B8%80%E8%A6%A7_%28%E6%97%A5%E6%9C%AC%29
     */
    private static $gengoList = [
        ['name' => '令和', 'name_short' => 'Ｒ', 'timestamp' =>  1556636400],  // 2019-05-01,
        ['name' => '平成', 'name_short' => 'Ｈ', 'timestamp' =>  600188400],  // 1989-01-08,
        ['name' => '昭和', 'name_short' => 'Ｓ', 'timestamp' => -1357635600], // 1926-12-25'
        ['name' => '大正', 'name_short' => 'Ｔ', 'timestamp' => -1812186000], // 1912-07-30
        ['name' => '明治', 'name_short' => 'Ｍ', 'timestamp' => -3216790800], // 1868-01-25
    ];

    /** 日本語曜日設定 */
    private static $weekJp = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];

    /** 午前午後 */
    private static $ampm = [
        'am' => '午前',
        'pm' => '午後',
    ];

    /**
     * 和暦などを追加したformat関数
     *
     * 追加した記号
     * J : 元号
     * b : 元号略称
     * K : 和暦年(1年を元年と表記)
     * k : 和暦年
     * x : 日本語曜日(0:日-6:土)
     * E : 午前午後
     */
 
    public static function jpFormat($dt, $format = 'Y年m月d日(x) H時i分')
    {
        if (empty($dt)) {
            return '';
        }

        App::setLocale('ja');
        Carbon::setLocale('ja');

        // 和暦関連のオプションがある場合は和暦取得
        $gengo = array();
        $timestamp = $dt->getTimestamp();

        if (preg_match('/[J|b|K|k]/', $format)) {
            foreach (self::$gengoList as $g) {
                if ($g['timestamp'] <= $timestamp) {
                    $gengo = $g;
                    break;
                }
            }
            // 元号が取得できない場合はException
            if (empty($gengo)) {
                throw new Exception('Can not be converted to a timestamp : '.$timestamp);
            }
        }

        // J : 元号
        if (strpos($format, 'J') !== false) {
            $format = preg_replace('/J/', $gengo['name'], $format);
        }

        // b : 元号略称
        if (strpos($format, 'b') !== false) {
            $format = preg_replace('/b/', $gengo['name_short'], $format);
        }

        // K : 和暦用年(元年表示)
        if (strpos($format, 'K') !== false) {
            $year = date('Y', $timestamp) - date('Y', $gengo['timestamp']) + 1;
            $year = $year == 1 ? '元' : $year;
            $format = preg_replace('/K/', $year, $format);
        }

        // k : 和暦用年
        if (strpos($format, 'k') !== false) {
            $year = date('Y', $timestamp) - date('Y', $gengo['timestamp']) + 1;
            $format = preg_replace('/k/', $year, $format);
        }

        // x : 日本語曜日
        if (strpos($format, 'x') !== false) {
            $w = date('w', $timestamp);
            $format = preg_replace('/x/', self::$weekJp[$w], $format);
        }

        // 午前午後
        if (strpos($format, 'E') !== false) {
            $a = date('a', $timestamp);
            $format = preg_replace('/E/', self::$ampm[$a], $format);
        }

        // https://momentjs.com/docs/#/parsing/string-format/
        $formatted = $dt->format($format);
        $formatted = str_replace(['午前12', '午前 12'], ['午前0'], $formatted);
        $formatted = str_replace(['午後12', '午後 12'], ['午後0'], $formatted);

        return $formatted;
    }


}
