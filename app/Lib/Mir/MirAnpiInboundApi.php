<?php namespace App\Lib\Mir;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ApiCallHttpErrorException;
use App\Exceptions\ApiCallArgumentErrorException;

class MirAnpiInboundApi {

    /**
     * ミライエの職員参集v3に対して発報する。
     *
     * @param string $api_url
     * @param array $args
     * @return void
     * 
     */
    public static function send(string $api_url, array $args)
    {
        $payload = self::buildRequestPayload($args);

        logger("MirAnpiInboundApi url={$api_url} payload=", $payload);

        $response = Http::post($api_url, $payload);

        $status = $response->status();
        $body = $response->body();
        $json = $response->json();

        if ($response->failed()) {
            // HTTPエラー発生
            $msg = "MirAnpiInboundApi Failed status={$status} body={$body}";
            MirUtil::logAlert($msg, $json);
            throw new ApiCallHttpErrorException($msg);
        }

        $is_success = $json['success'] ?? '';

        if ($is_success != 'Y') {
            // 受け取りエラー発生
            $msg = "MirAnpiInboundApi Failed status={$status} success={$is_success}";
            MirUtil::logAlert($msg, $json);
            throw new ApiCallArgumentErrorException($msg);
        }

        // Success
        info("MirAnpiInboundApi Success {$api_url}", $args);
        return;
    }

    private static function buildRequestPayload(array $args): array
    {
        $template_name = $args['template_name'];
        $unixtime = time();
        $iv = substr(config('_env.DIRECT_SEND_SALT'), 0, 16);
        $hash = openssl_encrypt($unixtime, 'AES-256-CBC', $template_name, 0, $iv);
       
        $payload = [
            // ux   - UNIXTIME
            'unixtime'       => $unixtime,

            // hs   - encrypt(UNIXTIME)
            'hash'           => $hash ?? '',

            'city_code'         => $args['city_code_6'],
            'trigger_code'      => $args['trigger_code'],
            'report_date_dt'    => $args['report_date_dt'],
            'publish_office'    => $args['publish_office'],
            'subject'           => $args['subject'],
            'body'              => $args['body'],
            'body_disp'         => $args['body_disp'],
            'body_voice'        => $args['body_voice'],
            'url'               => $args['url'],
            'trigger_name'      => $args['trigger_name'],
            'trigger_app'       => $args['trigger_app'],
            'priority'          => $args['priority'],
            'stage'             => $args['stage'],
            'dryrun'            => $args['dryrun'],
            'template_name'     => $args['template_name'],
            'alert_kinds'       => $args['alert_kinds'],
        ];

        return $payload;
    }

}
