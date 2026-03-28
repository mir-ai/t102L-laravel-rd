<?php namespace App\Lib\Mir;

use Carbon\Carbon;
use Monolog\LogRecord;
use App\Lib\Mir\MirUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Monolog\Handler\AbstractProcessingHandler;

class DatabaseMonologHandlerV4 extends AbstractProcessingHandler
{
    protected $table;
    protected $connection;
    
    // \Monolog\Level::DEBUG
    public function __construct($level = \Monolog\Level::Info, $bubble = true)
    {
        $this->table = config('_env.DB_LOG_TABLE', 'mir_laravel_logs');
        $this->connection = config('_env.DB_LOG_CONNECTION', 'mariadb_log');
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        // info, debugは無視
        // emergency, alert, critical, error, warning, noticeは含む
        // DEBUG (100): Detailed debug information.
        // INFO (200): Interesting events. Examples: User logs in, SQL logs.
        // NOTICE (250): Normal but significant events.
        // WARNING (300): Exceptional occurrences that are not errors. Examples: Use of deprecated         // APIs, poor use of an API, undesirable things that are not necessarily wrong.
        // ERROR (400): Runtime errors that do not require immediate action but should typically be        // logged and monitored.
        // CRITICAL (500): Critical conditions. Example: Application component unavailable,        // unexpected exception.
        // ALERT (550): Action must be taken immediately. Example: Entire website down, database       // unavailable, etc. This should trigger the SMS alerts and wake you up.
        // EMERGENCY (600): Emergency: system is unusable.


        $level_value = $record->level->value;
        $level_name = $record->level->name;
        $message = $record->message;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $http_host = $_SERVER['HTTP_HOST'] ?? '';
        $remote_addr = $_SERVER['HTTP_HOST'] ?? '';
        $datetime_utc = $record->datetime;
        $datetime = Carbon::instance($datetime_utc);

        if ($level_value < config('_env.DB_LOG_LEVEL', 250)) {
            return;
        }

        // DB接続エラーは輻輳するので除外
        if (str_contains($message, 'SQLSTATE[HY000] [2002]')) {
            return;
        }
        if (str_contains($message, 'SQLSTATE[HY000] [2006]')) {
            return;
        }
        if (str_contains($message, 'mir_laravel_logs')) {
            return;
        }
        
        $data = [
            'yymm'        => intval(now()->format('ym')),
            'user_id'     => (Auth::id() > 0 ? Auth::id() : 0),
            'instance'    => mb_substr(gethostname(), 0, 16),
            'channel'     => mb_substr($record->channel, 0, 32),
            'level'       => mb_substr($level_value, 0, 16),
            'level_name'  => mb_substr($level_name, 0, 16),
            'message'     => MirUtil::wtrim($message, 16384),
            'context'     => MirUtil::wtrim(json_encode($record->context), 16384),
            'remote_addr' => ($remote_addr) ? intval(ip2long($remote_addr)) : null,
            'user_agent'  => $user_agent,
            'http_host'   => mb_substr($http_host, 0, 64),
            'created_at'  => $datetime->tz('Asia/Tokyo'),
        ];

        try {
            DB::connection($this->connection)->table($this->table)->insert($data);
        } catch (\Exception $e) {
            // Drop logging error.
        }
    }
}
