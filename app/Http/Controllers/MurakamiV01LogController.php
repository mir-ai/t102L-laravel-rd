<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MurakamiV01LogController extends Controller
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
        ->orderBy('reported_at', 'desc')
        ->limit(100)
        ->get();

        // resources/view/murakami/v01/logs/index.blade.php を読む
        return view('murakami.v01.logs.index', [
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

        return view('murakami.v01.logs.show', [
            'log' => $log
        ]);
    }

    /**
     * APIからのログ情報を記録する
     *
     * @param Request $request
     * @return void
     */
    public function postLog(Request $request)
    {
        // 値のエラーを確認する
        $validator = Validator::make($request->all(), [
            'log_type' => 'required|string|max:255',
            'log_body' => 'required|string|max:16000',
            'unixtime' => 'required|numeric|max:2147483647',
        ]);

        // エラーがあったら
        if ($validator->fails()) {
            return [
                'is_success' => 'N',
                'errors' => $validator->errors()
            ];
        }

        $reported_at = Carbon::createFromTimestamp($request->unixtime);

        $attributes = [
            'log_type'    => $request->log_type ?? 'UNDEF',
            'log_body' => $request->log_body ?? '-',
            'reported_at' => $reported_at->setTimezone('Asia/Tokyo')->format('Y-m-d H:i:s.v'),
        ];

        Log::create($attributes);

        return [
            'is_success' => 'Y',
        ];

    }
}
