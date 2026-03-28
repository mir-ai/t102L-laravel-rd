<?php namespace App\Lib\Mir;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use App\Lib\CityUtil;

class MirMutex {

    public static function is_first($key, $expire_sec = 60): bool
    {
        $redis_key = "{$key}_counter";

        $val = Redis::incrby($redis_key, 1);

        if ($val == 1) {
            Redis::expire($redis_key, $expire_sec);
            return true;
        }

        return false;
    }

    public static function clear($key): bool
    {
        $redis_key = "{$key}_counter";

        Redis::delete($redis_key);

        return true;
    }


    // キャッチャーが、MP機器から10分以上離れて音が届くことが在るので、録音開始時刻の近さで2台めを判定するようにする。
    public static function has_previous_record($key, $catched_at, $delta_sec = 60, $expire_sec = 60 * 60)
    {
        $redis_key = "{$key}_recorded_timestamp";

        $new_recorded_timestamp = $catched_at->timestamp;
        $old_recorded_timestamp = Redis::get($redis_key);
        if ($old_recorded_timestamp) {

            $delta = abs($new_recorded_timestamp - $old_recorded_timestamp);

            logger("new_recorded_timestamp={$new_recorded_timestamp} old_recorded_timestamp={$old_recorded_timestamp}, delta=({)");
        
            if ($delta < $delta_sec) {
                return true;
            }
        }

        Redis::set($redis_key, $new_recorded_timestamp);
        Redis::expire($redis_key, $expire_sec);

        return false;
    }

}
