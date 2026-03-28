<?php

namespace App\Console\Commands;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class subscribe_mqtt_forever extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscribe_mqtt_forever';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $topic = config('_env.MQTT_TOPIC_UPSTREAM');

        $this->output->writeln("exec in Raspberry PI t101-raspi-client ./p07_mqtt_publish.py");

        $mqtt = MQTT::connection();
        $mqtt->subscribe($topic, function (string $topic, string $payload_json_str) {
            $this->info(sprintf('Received QoS level 0 message on topic [%s]: %s', $topic, $payload_json_str));

            $payload = json_decode($payload_json_str, true);

            $reported_at = (empty($payload['unixtime'])) ? 
                now() : 
                Carbon::createFromTimestamp($payload['unixtime']);

            $attributes = [
                'log_type'    => $payload['log_type'] ?? 'UNDEF',
                'log_body' => $payload['log_body'] ?? '-',
                'reported_at' => $reported_at->setTimezone('Asia/Tokyo')->format('Y-m-d H:i:s.v'),
            ];
            logger("attributes", $attributes);

            Log::create($attributes);

        }, 1);

        $mqtt->loop(true);        
    }
}
