<?php namespace App\Lib\Mir;

use Carbon\Carbon;
use Aws\CommandPool;
use App\Lib\CityUtil;
use Aws\Ses\SesClient;
use App\Models\Message;
use App\Lib\Mir\MirUtil;
use Aws\ResultInterface;
use Aws\CommandInterface;
use Illuminate\Support\Str;
use App\Lib\Mir\MirRawRedis;
use App\Lib\Mms\MmsRedisKey;
use App\DataAccess\DeliveryDa;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

// Amazon SES でメールをAPIで送る
class MirSesMailApi
{
    // 同時実行1でだいたい秒間4送信
    // 同時実行2でだいたい秒間9送信
    // 同時実行3でだいたい秒間13送信
    // 同時実行4でだいたい秒間16送信
    private int $concurrency = 3;

    // 連続成功回数（スロットルがかからない回数）
    private int $consecutive_success_counter = 0;

    // 現在の同時実行数を他の団体のコードと共有するためのRawRedis
    private $raw_redis_service = null;    
    private $key_concurrent = '';

    // 連続この回数スロットルがかからなかったら、同時実行数を１つ加算する
    private $speedup_on_success_count = 6;

    // ピークの同時実行数
    private $max_concurrency = 10;

    // 送信時のスロットルの割合を算出したい
    private $send_count = 0;
    private $throttling_count = 0;

    // 一度に処理するメール件数（速度制限がかかったらすぐに速度を落とせるように）
    private $send_at_once = 20;

    // スロットルがかかったとみなす割合
    private $throttling_detect_ratio = 0.1;

    public function __construct(
        // TODO: private EmailDeliveryRecorder $emailRecorder,
        private bool $publish,
        private Carbon $job_terminate_at,
        private array $send_items,
    )
    {

    }

    public function send(): bool
    {
        if (! $this->send_items) {
            return true;
        }

        $this->raw_redis_service = new MirRawRedis(expires: 600);
        $this->key_concurrent = MmsRedisKey::redis_shared_email_ses_api_concurrent();

        $prev_throttling_detected = false;

        // 一定件数ずつ小分けに実行する。
        // スロットル制限がかかったらすぐに速度を落とせるように。
        // 終了時間が来たらすぐに終了できるように
        foreach (array_chunk($this->send_items, $this->send_at_once) as $send_items) {

            $concurrency = $this->getConcurrency();

            $this->runSendMailSesApi($send_items, $concurrency);

            $throttling_ratio = ($this->throttling_count / $this->send_count);
            $throttling_detected = ($throttling_ratio > $this->throttling_detect_ratio);

            if ($throttling_detected) {
                if (! $prev_throttling_detected) {
                    // 前回スロットルがかかっていなくて、今回スロットルがかかったら、それをピークとして記憶する
                    $this->max_concurrency = $concurrency - 1;
                }

                // スロットルが検知されたら、スピードダウン
                $concurrency = $this->speeddown();
                $this->resetSuccessCounter();

            } else {
                // 一定回数以上成功したら、スピードアップ
                if ($this->isEnounghSuccess($this->speedup_on_success_count)) {
                    $concurrency = $this->speedup();
                    $this->resetSuccessCounter();
                }
            }

            $prev_throttling_detected = $throttling_detected;

            // 終了時間が来たら終わり
            if ($this->shouldTerminate()) {
                break;
            }
        }

        return true;
    }

    private function runSendMailSesApi(array $send_items, int $concurrency = 3)
    {
        logger("EmailSender: concurrency={$concurrency}");
        $ses_client = $this->getSesClient();
        $count = count($send_items);

        $commands = $this->getSesCommand($ses_client, $send_items);

        try {
            $timeStart = microtime(true);

            $pool = $this->execCommand($ses_client, $commands, $concurrency);

            // Initiate the pool transfers
            $promise = $pool->promise();

            // Force the pool to complete synchronously
            $promise->wait();

            $timeEnd = microtime(true);
            $stats = sprintf("%d in %1.1fs (%1.1f/s)", 
                $count,
                $timeEnd - $timeStart,
                $count / ($timeEnd - $timeStart)
            );

            logger("Send SES API completed: {$stats}");
            
        } catch(\Exception $e) {
            // echo sprintf('Error: %s' . PHP_EOL, $e->getMessage());
            MirUtil::logError('Catch Block: Amazon SES Exception : ' . $e->getMessage());
        }

        return true;
    }

    /**
     * SES client を取得
     *
     * @return
     */
    private function getSesClient()
    {
        return new SesClient([
            'version' => 'latest', 
            'region' => config('_env.SES_REGION'),
            'credentials' => [
                'key' => config('_env.SES_KEY'),
                'secret' => config('_env.SES_SECRET'),
            ], 
        ]);
    }

    private function getSesCommand($ses_client, array $send_items): array
    {
        $commands = [];
        foreach ($send_items as $send_item)
        {
            $delivery_email_id = $send_item['id'];
            $email = $send_item['email'];
            $client_id = $send_item['client_id'];
            $title = $send_item['title'];
            $plaintext_body = $send_item['body'];

            $mail_from = config('_env.MAIL_FROM_ADDRESS');
            $mail_name = config('_env.MAIL_FROM_NAME');

            // SendEmail
            // https://docs.aws.amazon.com/ses/latest/APIReference/API_SendEmail.html

            // https://docs.aws.amazon.com/ja_jp/ses/latest/dg/send-an-email-using-sdk-programmatically.html
            $commands[] = $ses_client->getCommand('SendEmail', [
                // Pass the message id so it can be updated after it is processed (it's ignored by SES)
                'delivery_email_id' => $delivery_email_id,
                'Source' => "{$mail_name} <{$mail_from}>",
                'Destination' => [
                    'ToAddresses' => [$email]
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => $title, 
                        'Charset' => 'UTF-8', 
                    ], 
                    'Body' => [
                        'Text' => [
                            'Charset' => 'UTF-8',
                            'Data' => $plaintext_body,
                        ],                        
                    ],
                ],
            ]);
        }

        return $commands;
    }

    private function execCommand($client, $commands, $concurrency = 3)
    {
        $this->send_count = 0;
        $this->throttling_count = 0;

        $pool = new CommandPool($client, $commands, [
            'concurrency' => $concurrency, 
            'before' => function (CommandInterface $cmd, $iteratorId) {
                $req = $cmd->toArray();
                $delivery_email_id = $req['delivery_email_id'];

                // logger("[M={$this->message->id}] email sending DE={$delivery_email_id}");
            },
            'fulfilled' => function (ResultInterface $result, $iteratorId) use ($commands) {
                // SESから返却される MessageId
                // 例: "010101976ce0eaa3-69d1c69f-3ce3-4ca3-bae3-1742fc0531c5-000000"
                $message_id = $result['MessageId'];

                $delivery_email_id = $commands[$iteratorId]['delivery_email_id'];

                DeliveryDa::recordSuccess($delivery_email_id);

                $this->send_count++;
            }, 
            'rejected' => function (AwsException $reason, $iteratorId) use ($commands) {
                $delivery_email_id = $commands[$iteratorId]['delivery_email_id'];

                $desc = $this->removeStackTrace((string)$reason);
                $desc = $this->simplifyDescription($desc);

                if ($this->isRetryableApiError($desc)) {
                    DeliveryDa::recordFailedRetry($delivery_email_id, $desc);
                } else {
                    DeliveryDa::recordFailedEnd($delivery_email_id, $desc);
                }

                if ($this->isThrottlingError($desc)) {
                    $this->send_count++;
                    $this->throttling_count++;
                }
            }, 
        ]);

        return $pool;
    }

    /**
     * 連続成功回数をリセット
     * 同時実行数増加タイミング管理用
     *
     * @return void
     */
    private function resetSuccessCounter()
    {
        $this->consecutive_success_counter = 0;
    }

    /**
     * 連続成功回数が一定を超えたら、trueを返す
     * 同時実行数増加タイミング管理用
     *
     * @param integer $over
     * @return boolean
     */
    private function isEnounghSuccess(int $over): bool
    {
        $this->consecutive_success_counter++;

        if ($this->consecutive_success_counter >= $over) {
            return true;
        }

        return false;
    }

    /**
     * 同時実行数を増やす
     * 配信リミットがしばらくかからなかったら増やす
     *
     * @return integer
     */
    private function speedup(): int
    {
        $concurrency = $this->raw_redis_service->incr($this->key_concurrent);
        logger("EmailSender4 Speed up concurrency={$concurrency}");
        return $concurrency;
    }

    /**
     * 同時実行数を減らす
     * 配信リミットがかかり始めたら減らす
     *
     * @return integer
     */
    private function speeddown(): int
    {
        $concurrency = $this->raw_redis_service->decr($this->key_concurrent);
        logger("EmailSender4 Speed down concurrency={$concurrency}");
        return $concurrency;
    }

    /**
     * 同時実行回数を取得する
     * 
     * 初期値はクラス変数から規定値を取るが、
     * 他の自治体のクラウドキャストと共有する同時実行回数で板があれば（RawRedis で他の団体のCloudCastか、自分自身で同時実行回数が設定されていたら、）
     * その値を採用する。
     *
     * @return integer
     */
    private function getConcurrency(): int
    {
        $concurrency = intval($this->concurrency);

        // RawRedis から同時接続数を取得する
        $shared_concurrency = $this->raw_redis_service->get($this->key_concurrent);

        // 他団体と共有している同時接続数があれば、その値を採用する
        if ($shared_concurrency) {
            $concurrency = intval($shared_concurrency);
        } 

        // 同時接続数が最大値を超えないように
        if ($concurrency > $this->max_concurrency) {
            $concurrency = $this->max_concurrency;
        }

        // 同時接続数が最小値を下回らないように
        if ($concurrency < 1) {
            $concurrency = 1;
        }

        // 他団体と共有している同時接続数を更新する
        if (! $shared_concurrency) {
            $this->raw_redis_service->set($this->key_concurrent, $concurrency);
        } 

        return $concurrency;
    }


    /**
     * 再試行可能なAPIエラー
     *
     * @param string $e
     * @return boolean
     */
    private function isRetryableApiError(string $e): bool
    {
        $retryable_errors = [
            'ServiceUnavailable',
            'Throttling',
            'RequestTimeout',
            'Expired',
            'RequestAborted',
        ];

        $test = str_replace($retryable_errors, '', $e);

        if ($test != $e) {
            // 上記エラーを含んでいたということは、再試行可能なエラー
            return true;
        }

        // 一致していたということは、 $retryable_errors が含まれていないので、再試行可能エラーではない。
        return false;
    }

    /**
     * 送信数制限がかかったかを確認
     *
     * @param string $e
     * @return boolean
     */
    private function isThrottlingError(string $e): bool
    {
        return (bool)(str_contains($e, 'Throttling'));
    }

    /**
     * エラーメッセージを単純化する
     * 確実の原因とエラーメッセージがわかったものだけを単純化する
     * （未知のエラーを隠してしまわないように）
     *
     * @param string $desc
     * @return string
     */
    private function simplifyDescription(string $desc): string
    {
        // AWS SES Throttling
        if (str_contains($desc, 'Throttling') && str_contains($desc, 'Maximum sending rate exceeded.')) {
            return 'Throttling';
        }

        return $desc;
    }

    /**
     * 終了時間になったか
     *
     * @return boolean
     */
    protected function shouldTerminate(): bool
    {
        return (now() > $this->job_terminate_at);
    }     

    /**
     * エラーメッセージからスタックトレース情報を取り除きます
     *
     * @param string $e
     * @return string
     */
    protected function removeStackTrace(string $e): string
    {
        return Str::before($e, 'Stack trace:');
    }


}
