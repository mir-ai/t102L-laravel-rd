<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// みまもり端末にアラートを送信する
Schedule::command('app:send_notifications_to_hoam')->everyMinute()->withoutOverlapping()->runInBackground();

// みまもり管理者にメールを送信する
Schedule::command('app:send_notifications_to_supporter')->everyMinute()->withoutOverlapping()->runInBackground();

// ロケーションごとの端末IDを洗替する
Schedule::command('app:sync_locations')->dailyAt('00:00')->withoutOverlapping()->runInBackground();
