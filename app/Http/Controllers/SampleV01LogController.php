<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class SampleV01LogController extends Controller
{
    /**
     * ログの一覧表示・検索画面
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // データベースから id の逆順で 100 件取得
        $logs = Log::query()
        ->orderBy('id', 'desc')
        ->limit(100)
        ->get();

        // resources/view/sample/v01/logs/index.blade.php を読む
        return view('sample.v01.logs.index', [
            'logs' => $logs
        ]);
    }

    /**
     * ログを１件表示
     *
     * @param  int $log_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $log_id)
    {
        $log = Log::findOrFail($log_id);

        return view('sample.v01.logs.show', [
            'log' => $log
        ]);
    }
}
