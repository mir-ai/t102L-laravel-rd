<?php namespace App\Lib\Mir;

use DateTimeImmutable;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;

class MirExcelV2
{
    /**
     * エクセルに出力する。
     * https://github.com/openspout/openspout/blob/4.x/tests/Writer/RowCreationHelper.php
     *
     * @param array $matrix
     * @return string
     */
    public static function export(array $matrix): string
    {
        $tmp_abs_path = MirTmpFile::newFullPath("xlsx", 'xlsx');

        $writer = new XLSXWriter();
        $writer->openToFile($tmp_abs_path); // write data to a file or to a PHP stream
        $rows = [];

        foreach ($matrix as $cellValues) {
            $rows[] = Row::fromValues($cellValues);
        }

        $writer->addRows($rows);
        $writer->close();

        return $tmp_abs_path;
    }

    /**
     * エクセルの内容を配列に取り込む
     * https://github.com/openspout/openspout/blob/4.x/tests/Reader/XLSX/ReaderTest.php
     *
     * @param string $filename
     * @return array
     */
    public static function import(string $filename): array
    {
        // セルデータ配列
        $allRows = [];

        // インスタンス作成
        $reader = new XLSXReader();

        // ファイルを開く
        $reader->open($filename);    

        // Excelファイル全体の読み込み
        foreach ($reader->getSheetIterator() as $sheet) {
            // 一行ずつ行の内容を取得
            foreach ($sheet->getRowIterator() as $rowObject) {
                $row = [];
                foreach ($rowObject->getCells() as $cellObject) {
                    $cell = $cellObject->getValue();
                    if ($cellObject instanceof FormulaCell) {
                        // Use getComputedValue() for formula cells
                        $cell = $cellObject->getComputedValue();
                    }

                    if ($cell instanceof DateTimeImmutable) {
                        $cell = $cell->format('Y/m/d H:i:s');
                    }

                    $row[] = $cell;
                }

                $allRows[] = $row;
            }
            
            // 1シートで終了
            break;
        }
        // ファイルを閉じる
        $reader->close();

        return $allRows;
    }
}
