<?php namespace App\Lib\Mir;

use Illuminate\Database\Eloquent\Model;

class MirCsv {

    public static function export($request, $data, $filename)
    {
        // StreamedResponseの第1引数（コールバック関数）
        $response = response()->streamDownload(function () use ($data) {

                // ファイルの書き出しはfopen()
                $stream = fopen('php://output', 'w');

                if($data) {
                    foreach ($data as $line) {
                        // ストリームに対して1行ごと書き出し
                        mb_convert_variables('SJIS-win', 'UTF-8', $line);
                        fputcsv($stream, $line);
                    }
                }
                fclose($stream);
            },

            // StreamedResponseの第2引数
            $filename,

            // StreamedResponseの第3引数（レスポンスヘッダ）
            [
                'Content-Type' => 'text/csv'
            ]
        );

        return $response;
    }

    // ２次元配列から指定したファイル名でCSVファイルを作成して保存する。
    public static function save(array $matrix, string $filename): string
    {
        $fp = fopen($filename, 'w');

        if ($matrix) {
            foreach ($matrix as $cols) {
                fputcsv($fp, $cols);
            }
        }

        fclose($fp);

        return $filename;
    }
}
