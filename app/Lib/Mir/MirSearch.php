<?php

namespace App\Lib\Mir;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

/**
 * 検索機能用ライブラリ	
 */
class MirSearch
{
    /**
     * モデル $model に応じた検索条件 $conds をセッションから取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $model
     * @param array $conds
     */
    public static function getLast(Request $request, string $model): array
    {
        $conds_json = $request->session()->get("conds_json_{$model}");

        if (empty($conds_json)) {
            return [];
        }

        return json_decode($conds_json, true);
    }

    /**
     * モデル $model に応じて条件 $conds をセッションに保存
     *
     * @param  \Illuminate\Http\Request  $request
     * @param array $conds
     * @param string $model
     */
    public static function setLast(Request $request, array $conds, string $model): void
    {
        $conds_json = json_encode($conds);

        $request->session()->put("conds_json_{$model}", $conds_json);
    }

    /**
     * 条件 $conds と $merge をマージ。値画からの要素は除去。
     *
     * @param array $conds
     * @param array $merge
     * @return array $conds
     */
    public static function cond(array $cond, array $merge = [])
    {
        $conds = array_merge($cond, $merge);
        $conds = Arr::where($conds, fn ($value, $key) => $value);

        return $conds;
    }

    /**
     * 条件 $conds から日時範囲検索の開始日時を組み立て
     *
     * @param array $conds
     * @return string $startDateTime
     */
    public static function startDateTime(array $conds): string
    {
        return sprintf(
            "%s %02d:%02d:00",
            $conds['_sd'],
            $conds['_sh'] ?: '00',
            $conds['_sm'] ?: '00'
        );
    }

    /**
     * 条件 $conds から日時範囲検索の終わりの日時を組み立て
     *
     * @param array $conds
     * @return string $endDateTime
     */
    public static function endDateTime(array $conds): string
    {
        return sprintf(
            "%s %02d:%02d:59",
            $conds['_ed'],
            $conds['_eh'] ?: '23',
            $conds['_em'] ?: '59'
        );
    }

    /**
     * 全文検索用に検索文字列の前後に % を付加する
     *
     * @param array $val
     * @param array $before
     * @param array $after
     * @return string $search
     */
    public static function escapeLike(string $val, string $before = '%', string $after = '%'): string
    {
        return "{$before}{$val}{$after}";
    }

    /**
     * 条件 $conds をもとに order by の方向を返す
     *
     * @param array $conds
     * @return string $direction
     */
    public static function orderDirection(array $conds, string $default = 'desc'): string
    {
        if (str_ends_with($conds['o'] ?? '', '-')) {
            return 'desc';
        }

        if (! empty($conds['o'])) {
            return 'asc';
        }

        return $default;
    }
}
