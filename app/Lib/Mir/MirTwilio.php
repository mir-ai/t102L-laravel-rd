<?php namespace App\Lib\Mir;

use Twilio\Rest\Client;
use Throwable;

class MirTwilio
{
    public static function call(string $to_number, string $from_number, array $options = []): mixed
    {
        $client = new Client(
            config('_env.TWILIO_ACCOUNT_SID', ''),
            config('_env.TWILIO_AUTH_TOKEN', ''),
        );

        $to_number = MirUtil::to8190($to_number);
        $from_number = MirUtil::to8190($from_number);

        // https://www.twilio.com/docs/voice/api/call-resource
        $callResource = $client->calls->create(
            $to_number,
            $from_number,
            $options,
        );

        return $callResource;
    }
}
