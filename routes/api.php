<?php

use App\Http\Controllers\SampleV01LogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SampleV01MessageController;

Route::name('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // 最新のメッセージを１件取得する
    Route::get('message/latest', [SampleV01MessageController::class, 'apiLatest'])->name('api_message_latest');

    // ログに登録する
    Route::post('log/post', [SampleV01LogController::class, 'postLog'])->name('api_log_post');
});
