<?php namespace App\Lib\Mir;

use Illuminate\Database\Eloquent\Model;

use \HeadlessChromium\BrowserFactory;
use \HeadlessChromium\Exception\BrowserConnectionFailed;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Exception\NavigationExpired;
use HeadlessChromium\Page;

class MirChrome
{
    private string $socket_file = '/tmp/chrome-php-socket';
    private $browser = null;
    private $page = null;
    private $last_error;

    public function __construct()
    {
    }

    public function init()
    {
        // ブラウザオブジェクトを使い回す。そのほうが高速なため。
        if (is_null($this->browser)) {
            $this->browser = $this->createBrowser();
        }

        // ページオブジェクトも使い回す。そのほうが高速だったため。
        if (is_null($this->page)) {
            $this->page = $this->browser->createPage();
        }
    }

    /**
     * Chromiumを使い、でJavascriptバリバリの動的ページの
     * HTML内容を取得する。
     *
     * @param string $url
     * @param int $time_out_ms
     * @return string
     */
    public function getHtml(string $url, int $time_out_ms = 150000): string
    {
        // 先に、HEADを叩いてページが生きているかを確認する。
        MirUtil::logDebug("<< {$url} ...");

        $this->init();
                
        try {
            $this->page->navigate($url)->waitForNavigation(Page::NETWORK_IDLE, $time_out_ms);
            $html = $this->page->getHtml();

            MirUtil::logDebug("<< {$url} OK");

            return $html;

        } catch (OperationTimedOut $e) {
            // too long to load
            $this->setError($url, "OPERATION_TIMEOUT: {$e}");

        } catch (NavigationExpired $e) {
            // An other page was loaded
            $this->setError($url, "NAVIGATION_EXPIRED: {$e}");
        }

        return '';
    }

    /**
     * エラーを記録
     *
     * @param string $string
     * @return void
     */
    private function setError(string $url, string $error)
    {
        $this->last_error = $error;
        MirUtil::logDebug("<< {$url} {$error}");
    }

    /**
     * エラーを返却
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->last_error;
    }

    /**
     * ページオブジェクトを返す
     *
     * @return mixed
     */
    public function getPage(): mixed
    {
        return $this->page;
    }

    /**
     * Chromeを起動する。
     *
     * @return mixed
     */
    private function createBrowser(): mixed
    {
        $options = [
                'enableImages' => false,
                'headless' => true,
                // 'keepAlive' => true, 子プロセスを起動しすぎてt3.mediumだと1時間後に落ちる
                'startupTimeout' => 60,
        ];

        $browser = $this->createNewBrowser($options);

        // Chromeをバックグラウンド起動する方式は（Chromeが子プロセスを増やしすぎて）サーバが落ちるので、廃止
        // $browser = $this->createBrowserWithSocket();

        // if (is_null($browser)) {
        //     $browser = $this->createNewBrowser($options);
        // }

        return $browser;
    }

    /**
     * Create new Chrome browser (without socket)
     *
     * @param array $options
     * @return mixed
     */
    private function createNewBrowser(array $options): mixed
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser($options);

        // 次に起動するときのため、Chrome情報をSocket情報に残しておく。
        //\file_put_contents($this->socket_file, $browser->getSocketUri(), LOCK_EX);

        return $browser;
    }    
    /**
     * Create chrome browser with socket
     * 
     * 前回起動したChromeを使う。
     * Socket情報を手がかりに前回のChrome情報を取得する
     * 
     * ※ただし重くてサーバが落ちるので現在は使っていない
     *
     * @return mixed
     */
    // private function createBrowserWithSocket(): mixed
    // {
    //     if (! file_exists($this->socket_file)) {
    //         return null;
    //     }

    //     if (! filesize($this->socket_file)) {
    //         return null;
    //     }

    //     // 前回起動したChromeが残っていれば、そのChromeを使う
    //     $socket = \file_get_contents($this->socket_file);

    //     try {
    //         $browser = BrowserFactory::connectToBrowser($socket);
    //         return $browser;
    //     } catch (BrowserConnectionFailed $e) {
    //         return null;
    //     }

    //     return null;
    // }



}
