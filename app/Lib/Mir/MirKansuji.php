<?php

namespace App\Lib\Mir;

/**
 * 漢字関連ライブラリ	
 */
class MirKansuji
{
    /**
     * 文字列中の漢数字を半角数字に変換する
     *
     * @param string $kanji
     * @return string
     */
    public static function kanSujiToNumber(string $kanji): string
    {
        //全角＝半角対応表
        $kan_num = [
            '０' => 0, '〇' => 0, '零' => 0,
            '１' => 1, '一' => 1, '壱' => 1,
            '２' => 2, '二' => 2, '弐' => 2,
            '３' => 3, '三' => 3, '参' => 3,
            '４' => 4, '四' => 4,
            '５' => 5, '五' => 5,
            '６' => 6, '六' => 6,
            '７' => 7, '七' => 7,
            '８' => 8, '八' => 8,
            '９' => 9, '九' => 9
        ];

        //位取り
        $kan_deci_sub = [
            '十' => 10,
            '百' => 100,
            '千' => 1000
        ];

        $kan_deci = [
            '万' => 10000,
            '億' => 100000000,
            '兆' => 1000000000000,
            '京' => 10000000000000000
        ];

        $kan_point = ['点'];

        if (mb_ereg('[０１２３４５６７８９〇一二三四五六七八九十百千万億兆京零壱弐参点]', $kanji) === false) {
            return -1;
        }

        //右側から解釈していく
        $ll = mb_strlen($kanji);
        $a = '';
        $deci = 1;
        $deci_sub = 1;
        $m = 0;
        $n = 0;
        $has_point = false;
        $o = '';
        for ($pos = $ll - 1; $pos >= 0; $pos--) {
            $c = mb_substr($kanji, $pos, 1);
            if (isset($kan_num[$c])) {
                $a = $kan_num[$c] . $a;
            } else if (isset($kan_deci_sub[$c])) {
                if ($a != '') {
                    $m = $m + $a * $deci_sub;
                } else if ($deci_sub != 1) {
                    $m = $m + $deci_sub;
                }
                $a = '';
                $deci_sub = $kan_deci_sub[$c];
            } else if (isset($kan_deci[$c])) {
                if ($a != '') {
                    $m = $m + $a * $deci_sub;
                } else if ($deci_sub != 1) {
                    $m = $m + $deci_sub;
                }
                $n = $m * $deci + $n;
                $m = 0;
                $a = '';
                $deci_sub = 1;
                $deci = $kan_deci[$c];
            } else if (in_array($c, $kan_point, true)) {
                if ($a != '') {
                    $m = $m + $a * 0.1;
                }
                $a = '';
            }
        }

        $ss = '';
        if (preg_match("/^(0+)/", $a, $regs) != FALSE) {
            $ss = $regs[1];
        }

        if ($a != '') {
            $m = $m + $a * $deci_sub;
        } else if ($deci_sub != 1) {
            $m = $m + $deci_sub;
        }
        $n = $m * $deci + $n;

        //出力書式に変換
        if ($ss == '') {
            $dest = $n;
        } else if ($n == 0) {
            $dest = $ss;
        } else {
            $dest = $ss . $n;
        }

        if ($o) {
            $dest = "{$dest}.{$o}";
        }

        return $dest;
    }    
}
