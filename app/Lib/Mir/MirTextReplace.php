<?php

namespace App\Lib\Mir;

/**
 * 複数文字列の安全な置き換え
 */
class MirTextReplace
{
    /**
     * 西区は浜北区に置き換えたい、北区は中央区に置き換えたい場合、str_replaceは西区を浜中央区にしてしまう。
     * （西区→浜北区→浜中央区のように一度置換を適用した語句に繰り返し置換を適用するため）
     * 
     * 一度置換を適用した語句には適用しないための置き換え関数です。
     *
     * 前提1
     * $subject に全角パイプ｜は含まない。
     * 
     * 前提2
     * $replacesAssoc の連想配列のキーに置換元、値に置換後の語句が入ってくる。
     * 置換元の文字列(キー)が長い順にキーがソートされて与えられる。
     * 
     * 北区 → 中央区
     * 浜北区 → 浜名区
     * としたい場合に、北区が先にくると中央区になってしまい、浜北区が適用されなくなってしまうため。
     * 
     * 前提3
     * $replacesAssoc の連想配列のキーの数字は全角数字とする（内部で一時的に半角数字は全て全角数字に置き換えるため）
     * 
     * @param array $replacesAssoc
     * @param string|null $subject
     * @return array
     */
    public static function textReplaces(array $replacesAssoc, string|null $subject): array
    {
        // 置き換元語句の配列
        $replaceFroms = array_keys($replacesAssoc);

        // A	「半角」英数字を「全角」に変換します （"a", "A" オプションに含まれる文字は、U+0022, U+0027, U+005C, U+007Eを除く U+0021 - U+007E の範囲です）。
        // 置換対象文の半角数字を全角にする。置き換えの最中で半角数字を制御コードとして利用するため、バッティングを防ぎたいので、置き換え元の数字は全部全角にしておきたい。
        // 置き換え語句も
        //$normalized_subject = mb_convert_kana($original_subject, 'A');
        // 置換対象文のアルファベットを小文字に統一する。(大文字・小文字のゆらぎがあってもマッチさせるため)
        //$normalized_subject = mb_strtolower($normalized_subject); 

        // 高速化のため、一旦対象語句全部で置換してみて長さが変わるかを調べる。長さが変わらなければこの文章に置換対象語句はないので、そのまま戻してよい。
        $tmp_subject = self::convertSubject($subject);
        $testReplaced = str_replace($replaceFroms, '', $tmp_subject);
        if ($testReplaced === $subject) {
            $replacedItem = [
                'replaced' => $subject, // 置換後の文字列
                'usedReplaces' => [], // 今回の置き換えで使った置換元文字列
            ];            
            return $replacedItem;
        }

        // 適用したの置換元語句と置換後語句を記録しておく箱
        $usedReplaces = [];

        // <speak>などのタグの中身を置換するのを防ぐために、タグと文章を配列に並べる。< を含むタグには置換を適用しない。
        // タグで行分割。  <p>こんにちは</p> は
        // <p>
        // こんにちは
        // </p> 的にしたい。（そしてタグを含む行は置換対象外としたい）
        $lines = explode('｜', str_replace(['<', '>'], ['｜<', '>｜'], $subject));

        // 
        // 置換語の単語を出現順に
        $step2replaceFrom = [];
        $step2replaceTo = [];

        // 置き換えた行の配列
        $step1Replaced = '';

        // 置き換え文字列の入れ子を防ぐために、2段階の置換にする。
        // 第一段階目は、置き換えたい語句を固有番号の付いたマーカーに置き換えてしまう。
        foreach ($lines as $line) {
            if (str_contains($line, '<')) {
                // タグ類は置換しない
                $step1Replaced .= $line;
                continue;
            }

            $original_line = $line;
            $normalized_line = self::convertSubject($line);

            // A	「半角」英数字を「全角」に変換します （"a", "A" オプションに含まれる文字は、U+0022, U+0027, U+005C, U+007Eを除く U+0021 - U+007E の範囲です）。
            // 置換対象文の半角数字を全角にする。置き換えの最中で半角数字を制御コードとして利用するため、バッティングを防ぎたいので、置き換え元の数字は全部全角にしておきたい。
            //$normalized_line = mb_convert_kana($original_line, 'A');
            // 置換対象文のアルファベットを小文字に統一する。(大文字・小文字のゆらぎがあってもマッチさせるため)
            //$normalized_line = mb_strtolower($normalized_line); 

            // 高速化のため、一旦対象語句全部で置換してみる。
            // 本文が変わらなければ、この行に置換対象語句はないということなので、後続の処理を飛ばす
            $testReplaced = str_replace($replaceFroms, '', $normalized_line);

            if ($testReplaced === $normalized_line) {
                $step1Replaced .= $original_line;

                continue;
            }
    
            // 各置換対象語句について置き換える。
            $replacing = $normalized_line;
            foreach ($replacesAssoc as $replaceFrom => $replaceTo) {
                $replaceFrom = strip_tags($replaceFrom);

                if (! str_contains($replacing, $replaceFrom)) {
                    continue;
                }

                // この行に置換語句があるようだ
                $markerIdx = count($step2replaceTo);
                $marker = "[___{$markerIdx}___]";

                // 第1段階では、一旦、置換元語句をマーカーに置換する
                $replacing = str_replace($replaceFrom, $marker, $replacing);

                // 第二段階の置き換えで使うために、置換元（マーカー）と置き換え語を記録
                $step2replaceFrom[] = $marker;
                $step2replaceTo[] = $replaceTo;
        
                // 今回の置換で使った置換元文字列と置換後の文字列を記録
                $usedReplaces[$replaceFrom] = $replaceTo;
            }

            // 全角数字を半角に戻す。<break time="4s"/>
            // r	「全角」英字を「半角」に変換します。
            // n	「全角」数字を「半角」に変換します。
            // s	「全角」スペースを「半角」に変換します（U+3000 -> U+0020）。
            // テレホンガイダンス用の mp3 のURLなどもあるため、半角に戻す
            $replacing = mb_convert_kana($replacing, 'rns'); 

            $step1Replaced .= $replacing;
        }

        // 第二段階
        $step2Replaced = str_replace(
            $step2replaceFrom, // 置換マーカー
            $step2replaceTo,   // 置換マーカーに対応する置換後の語句
            $step1Replaced     // 第一
        );

        // 全角数字を半角に戻す。<break time="4s"/>
        // $step2Replaced = mb_convert_kana($step2Replaced, 'n'); 

        // 未移行
        // $replaced_ssml = MmsUtil::add_telephone_speech_ssml_tag($replaced_ssml);
        // $replaced_ssml = MmsUtil::add_date_speech_ssml_tag($replaced_ssml);

        $replacedItem = [
            'replaced' => $step2Replaced, // 置換後の文字列
            'usedReplaces' => $usedReplaces, // 今回の置き換えで使った置換元文字列と置換先文字列のペア
        ];

        return $replacedItem;
    }     

    /**
     * 単語として登録する際の文字種を統一する
     * 半角カタカナは全角カタカナに
     * 半角英数字は全角英数字に
     * アルファベットは小文字に
     *
     * @param string $src
     * @return string
     */
    public static function convertSubject(string $src): string
    {
        // PollyのSSMLで使用できない <>'&"は、全角といえども除去しておく
        $src = str_replace(['＜', '＞', '’', '＆', '”'], '', $src);

        // R 「半角」英字を「全角」に変換します。
        // N 「半角」数字を「全角」に変換します。
        // S 「半角」スペースを「全角」に変換します
        // K 「半角カタカナ」を「全角カタカナ」に変換します。
        // V 濁点付きの文字を一文字に変換します。
        $src = mb_convert_kana($src, 'RNSKV');
        $src = mb_strtolower($src);

        return $src;
    }
       
}
