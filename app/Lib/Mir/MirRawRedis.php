<?php namespace App\Lib\Mir;

use \Redis; // 生Redis（LaravelのRedisファサードではない）

/**
 * Redis を 素の状態で使う （保存時のプレフィックスを付けないので、他自治体のクラウドキャストとデータ共有ができる）
 * 
 * 通常の Illuminate\Support\Facades\Redis はキー保存時に、キーの先頭に mms_database_ALERT プレフィックスがついてしまう。
 * 普段は他のプロジェクトと混ざらないのでこのままがよいが、稀に「他プロジェクト含め、全体でデータを共有したい時」がある
 * 例：メール送信時のレート制限に関する情報など
 * 
 * そこで、ここでは「頭にプレフィックスを付けない」でRedisを使える手段を提供する
 * 
 */
class MirRawRedis
{
    private $redis;

    /**
     * Redis に接続する
     *
     * @param integer $expires 有効秒数 (なにか操作するとまた有効期間が延長される)
     */
    public function __construct(
        private int $expires = 600
    )
    {
        $this->redis = new \Redis();

        $this->redis->connect(
            config('database.redis.default.host'),
            config('database.redis.default.port'),
        );
    }

    public function __destruct()
    {
        if ($this->redis->isConnected()) {
            $this->redis->close();
        }
    }

    /**
     * 値をセットする
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function set(string $key, string $value)
    {
        $this->redis->set($key, $value);
        $this->redis->expire($key, $this->expires);
    }

    /**
     * 値を取得する
     *
     * @param string $key
     * @return void
     */
    public function get(string $key): string
    {
        $this->redis->expire($key, $this->expires);
        return $this->redis->get($key);
    }

    /**
     * 値を加算する
     *
     * @param string $key
     * @param integer $by
     * @return int
     */
    public function incr(string $key, int $by = 1): int
    {
        $this->redis->expire($key, $this->expires);
        return $this->redis->incrBy($key, $by);
    }

    /**
     * 値を減算する
     *
     * @param string $key
     * @param integer $by
     * @return int
     */
    public function decr(string $key, int $by = 1): int
    {
        $this->redis->expire($key, $this->expires);
        return $this->redis->decrBy($key, $by);
    }
}
