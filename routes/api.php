<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V4\Api\ReplaceApiV4Controller;
use App\Http\Controllers\V4\Api\InboundMailApiV4Controller;

Route::name('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // 日本語テキストを読み上げるSSMLを生成する
    Route::post('replace/ssml', [ReplaceApiV4Controller::class, 'ssml'])->name('yomi_ssml');

    // 日本語の単語を入れると、登録されているヨミを返す
    Route::post('replace/get', [ReplaceApiV4Controller::class, 'yomi_get'])->name('yomi_get');

    // 日本語の単語に対してヨミを登録する
    Route::post('replace/set', [ReplaceApiV4Controller::class, 'yomi_set'])->name('yomi_set');

    // 届いたメールを登録する
    Route::post('inbound/mail', [InboundMailApiV4Controller::class, 'add'])->name('inbound_mail_add');
});
