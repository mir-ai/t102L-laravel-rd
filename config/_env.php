<?php

return [

    // 市区町村コード
    'CITY_CODE' => env('CITY_CODE', ''),

    // 管理者のメール
    'MAIL_ADMIN_TO' => env('MAIL_ADMIN_TO', 'ss@mir-ai.co.jp'),

    // フッタークレジット
    'FOOTER_CREDIT' => env('FOOTER_CREDIT'),

    // favicon
    'FAVICON_FORCE_URL' => env('FAVICON_FORCE_URL'),

    // アプリコード
    'APP_CODE' => env('APP_CODE', ''),

    // アプリ名称(内部用)
    'APP_NAME' => env('APP_NAME', ''),

    // サイト名(外部公開用)
    'APP_NAME_JP' => env('APP_NAME_JP'),

    // ヘッダー画像URL
    'HEAD_LOGO_IMG_URL' => env('HEAD_LOGO_IMG_URL'),

    // Laravelのログを保存するDBテーブル
    'DB_LOG_TABLE' => env('DB_LOG_TABLE'),

    // Laravelのログを保存するコネクション
    'DB_LOG_CONNECTION' => env('DB_LOG_CONNECTION'),

    // Laravelのログを保存するコネクション
    'DB_LOG_CONNECTION' => env('DB_LOG_CONNECTION'),

    // CloudFront版のS3プレフィックス
    'CLOUD_FRONT_PREFIX' => env('CLOUD_FRONT_PREFIX'),

    // S3版のURLプレフィックス
    'S3_PREFIX' => env('S3_PREFIX'),
    
    // Laravelのログを保存するレベル
    //   DEBUG (100): Detailed debug information.
    //   INFO (200): Interesting events. Examples: User logs in, SQL logs.
    //   NOTICE (250): Normal but significant events.
    //   WARNING (300): Exceptional occurrences that are not errors. Examples: Use of deprecated
    //   APIs, poor use of an API, undesirable things that are not necessarily wrong.
    //   ERROR (400): Runtime errors that do not require immediate action but should typically be
    //   logged and monitored.
    //   CRITICAL (500): Critical conditions. Example: Application component unavailable,
    //   unexpected exception.
    //   ALERT (550): Action must be taken immediately. Example: Entire website down, database
    //   unavailable, etc. This should trigger the SMS alerts and wake you up.
    //   EMERGENCY (600): Emergency: system is unusable.
    'DB_LOG_LEVEL' => env('DB_LOG_LEVEL', 250),

    // メール送信設定
    'SES_REGION' => env('SES_REGION'),
    'SES_KEY' => env('SES_KEY'),
    'SES_SECRET' => env('SES_SECRET'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),

    // MQTT トピック

    // サーバからクライアントへ
    'MQTT_TOPIC_DOWNSTREAM' => env('MQTT_TOPIC_DOWNSTREAM'),

    // クライアントからサーバへ
    'MQTT_TOPIC_UPSTREAM' => env('MQTT_TOPIC_UPSTREAM'),
];

