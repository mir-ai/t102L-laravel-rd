<?php

use App\Http\Controllers\SampleV01LogController;
use App\Http\Controllers\SampleV01MessageController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ルート
Route::get('/', [App\Http\Controllers\GuestController::class, 'index'])->name('index');

// 基本認証領域
if (Route::group(['middleware' => 'auth.very_basic'], function () {

    //-----------------------------------------------------------
    // 村上くんの場所

    //-----------------------------------------------------------
    // 小林くんの場所
    
    //-----------------------------------------------------------
    // サンプルの場所
    Route::prefix('sample')->name('sample.')->group(function () {

        // バージョン0１
        Route::prefix('v01')->name('v01.')->group(function () {

        // ログ一覧表示
            Route::get('log/index', [SampleV01LogController::class, 'index'])->name('logs.index');

            // ログ詳細表示
            Route::get('log/show/{log_id}', [SampleV01LogController::class, 'show'])->name('logs.show');

            // お知らせ一覧表示
            Route::get('message/index', [SampleV01MessageController::class, 'index'])->name('messages.index');

            // お知らせ作成画面
            Route::get('message/create', [SampleV01MessageController::class, 'create'])->name('messages.create');

            // お知らせ作成実行
            Route::post('message/store', [SampleV01MessageController::class, 'store'])->name('messages.store');
        });
    });

    Auth::routes(['register' => false]);
}));
