<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
  * ログモデルのオブジェクト定義
  */
class Log extends Model
{
    // ララベルから操作してもよいカラムをホワイトリストで指定
    // ここに指定がないカラムはララベルから操作されないので注意
    protected $fillable = [
        'id',
        'log_type',
        'log_body',
        'reported_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // 日付型などに自動変換すべきカラムを指定
     protected $casts = [
        'reported_at' => 'datetime:Y-m-d H:i:s.v',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

