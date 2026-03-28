<?php namespace App\Lib\Mir;

use HeadlessChromium\BrowserFactory;

class MirPdf
{
    // HTMLコンテンツからPDFを生成する。既に作成済みならそれを返す
    public static function buildPdfOrGetFromCache(string $html_content, string $model, int $id, string $lang_code = 'ja'): string
    {
        // PDFを保存するパスを取得する。
        $abs_path = self::getAbsPath($model, $id, $lang_code);

        if (file_exists($abs_path) && is_file($abs_path) && filesize($abs_path) > 0) {
            // PDFが既に作成されていたら、それを返す。
            return $abs_path;
        }

        // PDFを生成してそのパスに保存する。
        $abs_path = self::buildPdfFromHtml(
            html_content: $html_content,
            model: $model,
            id: $id,
            lang_code: $lang_code,
        );

        return $abs_path;
    }

    // HTMLをもとにPDFを作成し、指定されたパスにPDFを保存する
    public static function buildPdfFromHtml(string $html_content, string $model, int $id, string $lang_code = 'ja'): string
    {
        $browser = null;

        // PDFを保存するパスを取得する。
        $abs_path = self::getAbsPath($model, $id, $lang_code);

        try {
            logger("[PDF] creating browser for model={$model} id={$id}");
            
            // chrome 立ち上げ
            $browserFactory = new BrowserFactory();
            $browser = $browserFactory->createBrowser([
                'headless' => true,
            ]);

            logger("[PDF] browser created for model={$model} id={$id}");

            // ページ生成
            $page = $browser->createPage();
            $page->setHtml($html_content);

            $page->pdf([
                'landscape' => false,
                'paperWidth' => 8.27, // A4 width in inch
                'paperHeight' => 11.69, // A4 height in inch
                'printBackground' => true,
            ])->saveToFile($abs_path);

            logger("[PDF] saved to {$abs_path} for model={$model} id={$id}");

        } catch (\Exception $e) {
            MirUtil::logAlert("Failed to make PDF model={$model} id={$id}: $e");
        } finally {
            // chrome を閉じる
            $browser?->close();
        }            

        return $abs_path;
    }

    // 置き場の絶対パスを取得する
    private static function getAbsPath(string $model, int $id, string $lang_code = ''): string
    {
        return storage_path("app/private/pdf/{$model}-{$id}-{$lang_code}.pdf");
    }
}
