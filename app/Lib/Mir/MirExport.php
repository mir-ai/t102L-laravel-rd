<?php namespace App\Lib\Mir;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MirExport
{
    static string $delete_column = 'Delete1';

    public static function make_matrix($collections, array $export_kvs, bool $append_delete_column = false): array
    {
        return self::makeBoxMatrix($collections, $export_kvs, $append_delete_column);
    }

    /**
     * エクスポートデータ作成用の関数。
     * データのコレクションと、エクスポートするキーとカラム名のを受取り、エクスポート用の２次元配列を返す。
     * 頭にはカラム名の行と、カラム名のキーの行を追加する。
     *
     * @param [type] $collections エクスポートするコレクション。
     * @param array $export_kvs エクスポートするカラムをキーに入れた、連想配列。
     * @return array 
     */
    public static function makeBoxMatrix($collections, array $export_kvs, bool $append_delete_column = false): array
    {
        $matrix = [];

        // ヘッダーの配列を作る
        $header1s = ['#'];
        $header2s = ['#'];
        foreach ($export_kvs as $k => $v) {
            $header1s[] = $v;
            $header2s[] = $k;
        }

        if ($append_delete_column) {
            $header1s[] = '削除は1';
            $header2s[] = self::$delete_column;
        }

        $matrix[] = $header1s; // カラム名の行を追加
        $matrix[] = $header2s; // カラムキーの行を追加

        // 本体の配列を作る
        foreach ($collections as $collection) {
            $cols = [''];
            foreach ($export_kvs as $k => $v) {
                if ($collection->$k instanceof \UnitEnum) {
                    $val = $collection->$k->value;
                } else if (is_array($collection->$k)) {
                    $val = json_encode($collection->$k);
                } else {
                    $val = strval($collection->$k ?? '');
                }
                $val = MirUtil::wtrim($val, 10000); // 32,767 hankaku, 10000 zenkaku, maybe

                $cols[] = $val;
            }

            if ($append_delete_column) {
                $cols[] = '';
            }

            $matrix[] = $cols;
        }

        return $matrix;
    }

    /**
     * fastExcelでのエクスポートデータ作成用の関数。
     * データのコレクションと、エクスポートするキーとカラム名のを受取り、エクスポート用の２次元配列を返す。
     * 頭にはカラム名の行と、カラム名のキーの行を追加する。
     *
     * @param [type] $collections エクスポートするコレクション。
     * @param array $export_kvs エクスポートするカラムをキーに入れた、連想配列。
     * @return array 
     */
    public static function makeFastMatrix($collections, array $export_kvs, bool $append_delete_column = false): array
    {
        // ヘッダー配列を作成
        $header_kvs = [];
        $header_kvs['#'] = '#';
        foreach ($export_kvs as $column_key => $column_name) {
            $header_kvs[$column_name] = $column_key;
        }
        if ($append_delete_column) {
            $header_kvs['削除は1'] = self::$delete_column;
        }

        // 1行目を追加
        $fast_matrix = [];
        $fast_matrix[] = $header_kvs;

        foreach ($collections as $collection) {
            $item = [];
            foreach ($header_kvs as $k => $v) {
                $val = $collection->$v ?? '';

                if (is_array($val)) {
                    $item[$k] = json_encode($val);
                } else if (is_numeric($val)) {
                    $item[$k] = $val;
                } else {
                    // 日付は文字列型にしたい
                    $item[$k] = strval($val);
                }
            }
            $fast_matrix[] = $item;
        }

        return $fast_matrix;
    }

    public static function checkUploadFileNameError(Request $request, string $prefix, string $input, array $exts = ['.xls', '.xlsx'])
    {
        if (!$request->hasFile($input)) {
            return "ファイルが指定されていません。ファイルを指定して下さい。";
        }

        if (!$request->file($input)->isValid()) {
            return "ファイルのアップロードに失敗しました。もう一度お試しください。";
        }

        $original_name = $request->file($input)->getClientOriginalName();

        // UTF8-MACとUTF8の差分を吸収する
        // ゴ (UTF-8 3バイト)と ゴ (UTF-8-MAC 6バイト)は違う
        $original_name = normalizer_normalize($original_name);
        $prefix = normalizer_normalize($prefix);

        if ($prefix) {
            if (!str_starts_with($original_name, $prefix)) {
                return "ファイル名が違います。誤アップロードを防止するため、ファイル名が {$prefix} から始まるファイルをアップロードして下さい。";
            }
        }

        foreach ($exts as $ext) {
            if (str_ends_with($original_name, $ext)) {
                return '';
            }
        }

        return "ファイル名が " . implode(' または ', $exts) . " で終わるファイルを指定して下さい。";
    }

    /**
     * アップロードされたエクセルの内容を配列として取得
     *
     * @param Request $request
     * @param string $input
     * @return array $uploaded_matrix
     */
    public static function getUploadedMatrix(Request $request, string $input): array
    {
        $localPath = $request->file($input)->store('uploaded_xlsx');
        $realpath = storage_path('app/private/' . $localPath);
        $uploaded_matrix = MirExcelV2::import($realpath);
        return $uploaded_matrix;
    }

    /**
     * 指定されたエクセルの内容を配列として取得
     *
     * @param string $import_file_path
     * @return array $uploaded_matrix
     */
    public static function getImportedMatrix(string $import_file_path): array
    {
        $uploaded_matrix = MirExcelV2::import($import_file_path);
        return $uploaded_matrix;
    }
    
    // アップロードされたエクセルファイルをDBに一時保存
    public static function saveToTmpStorage(string $prefix, array $uploaded_matrix, ?string $original_file_name = null, ?int $original_file_size = null, ?int $user_id = null)
    {
        $data_key = uniqid("{$prefix}_");

        DB::table('mir_serialized_vars')
        ->insert([
            'var_yymm'       => intval(now()->format('ym')),
            'var_name'       => $data_key,
            'serialized_var' => igbinary_serialize($uploaded_matrix),
            'expired_at'     => now()->addDay(),
            'original_file_name' => $original_file_name,
            'file_size'      => $original_file_size,
            'user_id'        => $user_id ?? 0,
            'created_at'     => now()
        ]);

        return $data_key;
    }

    // アップロードされたエクセルファイルをDBから取得
    public static function loadFromTmpStorage(string $data_key)
    {
        $mir_serialized_var = DB::table('mir_serialized_vars')
        ->where('var_name', $data_key)
        ->where('expired_at', '>=', now())
        ->first();

        if (empty($mir_serialized_var)) {
            return null;
        }

        $serialized_var = igbinary_unserialize($mir_serialized_var->serialized_var);

        return $serialized_var;
    }

    /**
     * ファイル中からIDの配列を取得する。
     *
     * @param array $matrix
     * @param bool $is_int IDがint型とわかっていればtrue, 文字列型ならfalse
     * @return array $ids
     */
    public static function getIdsFromMatrix(array $matrix, bool $is_int = true): array
    {
        $ids = [];
        foreach ($matrix as $rows) {
            $col0 = $rows[0] ?? '';
            $col1 = $rows[1] ?? '';

            if (str_contains($col0, '#')) {
                continue;
            }

            if ($is_int) {
                if (intval($col1) > 0) {
                    $ids[] = intval($col1);
                }
            } else {
                if ($col1) {
                    $ids[] = $col1;
                }
            }
        }

        return $ids;
    }

    /**
     * 2つのマトリクスから、行を特定するキーの集合を取得する。
     *
     * @param array $matrix1
     * @param array $matrix2
     * @return array $row_keys
     */
    public static function uniqRowKeys(array $matrix1, int $x_of_key): array 
    {
        $keys1 = self::getRowKeys($matrix1, $x_of_key);
        $row_keys = $keys1;
        $row_keys = array_unique($row_keys, SORT_REGULAR);
        $row_keys = Arr::where($row_keys, fn ($value, $key) => $value);

        //asort($row_keys);
        return $row_keys;
    }

    private static function getRowKeys(array $matrix, int $x_of_key): array
    {
        $rowKeys = [];
        for ($y = 2; $y < count($matrix); $y++) {

            $rowKey = self::getRowKey($matrix[$y], $x_of_key, $y);
            $rowKeys[] = $rowKey;
        }

        return $rowKeys;
    }

    private static function getRowKey(array $columns, int $x_of_key, int $y): string
    {
        try {
            $line = implode('', $columns);
        } catch (\Exception $e) {
            dd($columns);
        }

        if (empty($line)) {
            return '';
        }

        if (str_starts_with($line, '#')) {
            return '';
        }

        $row_key = $columns[$x_of_key] ?? '';

        if (is_numeric($row_key)) {
            $row_key = intval($row_key);
        }

        if (blank($row_key)) {
            return sprintf("1%06d", $y);
        }

        return $row_key;
    }

    public static function getRowColValue(array $matrix, int $y_of_key, int $x_of_key): array
    {
        if (empty($matrix)) {
            return [];
        }

        $colKeys = $matrix[$y_of_key] ?? [];

        $rowColValue = [];
        for ($y = 2; $y < count($matrix); $y++) {
            $rowKey = self::getRowKey($matrix[$y], $x_of_key, $y);

            for ($x = 0; $x < count($matrix[$y]); $x++) {
                $columnKey = $colKeys[$x] ?? '';

                $v = $matrix[$y][$x] ?? '';
                $rowColValue[$rowKey][$columnKey] = $v;
            }
        }
        return $rowColValue;
    }    

    /**
     * Undocumented function
     *
     * @param array $matrix
     * @param integer $xOfKey
     * @return array
     */
    public static function convColValues(array $matrix, int $x_of_key = 1): array
    {
        $col_values = [];
        for ($y = 0; $y < count($matrix); $y++) {
            $row_key = $matrix[$y][$x_of_key] ?? '';

            if (empty($row_key)) {
                continue;
            }

            if (str_starts_with($row_key, '#')) {
                continue;
            }

            $serialized = implode(',', $matrix[$y]);
            $serialized = preg_replace('/,+$/', '', $serialized);
            $col_values[$row_key] = $serialized;
        }

        return $col_values;
    }    

}
