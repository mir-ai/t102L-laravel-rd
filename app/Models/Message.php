<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
  * メッセージモデルのオブジェクト定義
  */
class Message extends Model
{
    // ララベルから操作してもよいカラムをホワイトリストで指定
    // ここに指定がないカラムはララベルから操作されないので注意
    protected $fillable = [
        'id',
        'message_title',
        'message_body',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // 日付型などに自動変換すべきカラムを指定
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

