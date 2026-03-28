<?php

namespace App\Console\Commands;
use PhpMqtt\Client\Facades\MQTT;

use Illuminate\Console\Command;

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
        $mqtt->subscribe($topic, function (string $topic, string $message) {
            $this->info(sprintf('Received QoS level 0 message on topic [%s]: %s', $topic, $message));
        }, 0);
        $mqtt->loop(true);        

    }
}
