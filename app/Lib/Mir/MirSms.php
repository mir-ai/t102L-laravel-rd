<?php namespace App\Lib\Mir;

use App\Lib\CityUtil;
use App\Lib\Mir\MirUtil;
use App\Lib\Mir\FluentCsv;
use App\Lib\Mir\MirTmpFile;
use Illuminate\Support\Facades\Http;

class MirSms {

    public static function send($args)
    {
        $smsid = preg_replace('/[^0-9a-zA-Z]/', '', $args['smsid']); // 英数字以外削除
        $mobilenumber = preg_replace( '/[^0-9]/', '', $args['mobilenumber']); // 数字以外削除

        $full_api_url = config('_env.SMS_API_SEND');
        $user_name = config('_env.SMS_USERNAME');
        $password = config('_env.SMS_PASSWORD');

        $params = [
            'username'     => $user_name,
            'password'     => $password,
            'mobilenumber' => $mobilenumber,
            'smstext'      => $args['smstext'],
            'smsid'        => $smsid,
            'originalurl'  => $args['originalurl'],
            'originalurl2' => $args['originalurl2'] ?? '',
            'originalurl3' => $args['originalurl3'] ?? '',
            'originalurl4' => $args['originalurl4'] ?? '',
            'status'       => $args['status'],
        ];

        if (! $full_api_url) {
            MirUtil::logError('_env.SMS_API_SEND not set');
        }

        if (! $user_name) {
            MirUtil::logError('_env.SMS_USERNAME not set');
        }

        if (! $password) {
            MirUtil::logError('_env.SMS_PASSWORD not set');
        }

        $response = Http::post($full_api_url, $params);
        $http_status = $response->status();

        $sms_messages = MirSms::sms_messages();
        $result = $response->json();
        $ret_code = $result['result'] ?? 0;
        $ret_msg = $sms_messages[$ret_code] ?? '';

        $ret = [
            'http_status' => $http_status,
            'ret_code' => $ret_code,
            'ret_msg' => $ret_msg,
        ];

        logger("SMS Send [SMSID={$smsid}] http_status={$http_status} ret_code={$ret_code} ret_msg={$ret_msg} result=", $result);

        return $ret;
    }

    /**
     * SMS基盤から過去 $minutes 分以内に送ったSMSの送信結果を取得する
     *
     * @param integer $minutes
     * @return array
     */
    public static function getSmsSendResults(int $minutes = 70): array
    {
        return once(function () use ($minutes) {

            // Get result CSV
            $from = now()->subMinutes($minutes)->format('YmdHis');
            $to = now()->format('YmdHis');
            $username = config('_env.SMS_USERNAME');
            $password = config('_env.SMS_PASSWORD');
            $encode = 'utf8';
            $sms_api_result = config('_env.SMS_API_RESULT');
            $result_csv_url = "{$sms_api_result}?username={$username}&password={$password}&from={$from}&to={$to}&encode={$encode}";
            $content = file_get_contents($result_csv_url);

            // Save result CSV
            $local_abs_path = MirTmpFile::save('sms/result', 'csv', $content);

            // Parse CSV
            $csv_matrix = FluentCsv::parse($local_abs_path, null);

            MirTmpFile::safe_unlink($local_abs_path);

            return $csv_matrix;
        });
    }

    /**
     * 配信結果を取得する
     * sms は delivery_id, city_code, app_code, charge_id が x 区切りで記載されている
     * 
     * この関数は delivery_id を取得する
     */
    public static function getDeliveryResults($min = 70)
    {
        $csv_matrix = self::getSmsSendResults($min);

        $app_code = config('_env.APP_CODE');
        $city_code = CityUtil::city_code();
        $sms_result_kvs = [];
        for ($y = 0; $y <= count($csv_matrix); $y++) {
            $result_txt = $csv_matrix[$y][11] ?? '';
            $smsid = $csv_matrix[$y][17] ?? '';

            if (strpos($smsid, 'x') === false) {
                continue;
            }

            [$delivery_id, $_city_code, $_app_code, $charge_id] = explode('x', "{$smsid}xxx");

            if ($city_code != $_city_code) {
                continue;
            }

            if ($app_code != $_app_code) {
                continue;
            }

            if (! $delivery_id) {
                continue;
            }

            if ($smsid && $result_txt) {
                $sms_result_kvs[$delivery_id] = $result_txt;
            }
        }

        return $sms_result_kvs;
    }

    /**
     * 認証SMSの結果を取得する
     * sms は delivery_id, city_code, app_code, charge_id が x 区切りで記載されている
     * 
     * この関数は charge_id を取得する
     */
    public static function getChargeResults($min = 70)
    {
        $csv_matrix = self::getSmsSendResults($min);

        $app_code = config('_env.APP_CODE');
        $city_code = CityUtil::city_code();
        $sms_result_kvs = [];
        for ($y = 0; $y <= count($csv_matrix); $y++) {
            $result_txt = $csv_matrix[$y][11] ?? '';
            $smsid = $csv_matrix[$y][17] ?? '';

            if (strpos($smsid, 'x') === false) {
                continue;
            }

            [$delivery_id, $_city_code, $_app_code, $charge_id] = explode('x', "{$smsid}xxx");

            if ($city_code != $_city_code) {
                continue;
            }

            if ($app_code != $_app_code) {
                continue;
            }

            if (! $charge_id) {
                continue;
            }

            if ($smsid && $result_txt) {
                $sms_result_kvs[$charge_id] = $result_txt;
            }
        }

        return $sms_result_kvs;        
    }

    private static function sms_messages()
    {
        return [
            '200' => 'Success',
            '401' => 'Authorization Required',
            '402' => 'Failed to send SMS due the Overlimit',
            '414' => 'URL is longer than 8190bytes',
            '503' => 'Reached 80req/sec',
            '550' => 'Failure',
            '555' => 'Your IP address has been blocked',
            '560' => 'Mobile number is invalid',
            '562' => 'Start date is invalid',
            '568' => 'Au SMS title is invalid',
            '569' => 'Softbank SMS title is invalid',
            '570' => 'Sms text id is invalid',
            '571' => 'Sending attempts is invalid',
            '572' => 'Resending interval is invalid.',
            '573' => 'Status is invalid',
            '574' => 'SMS ID is invalid',
            '575' => 'Docomo is invalid',
            '576' => 'au is invalid',
            '577' => 'Soft Bank is invalid',
            '579' => 'Gateway is invalid',
            '580' => 'Sms title is invalid',
            '585' => 'Sms text is invalid',
            '587' => 'SMS ID is not unique',
            '590' => 'Original URL is invalid',
            '598' => 'Docomo SMS title is invalid',
            '599' => 'Resending is disabled',
            '601' => 'SMS title function is disabled',
            '605' => 'Invalid type',
            '606' => 'This API is disabled',
            '608' => 'Invalid Registration date',
            '610' => 'HLR is disabled',
            '612' => 'Original URL 2 is invalid',
            '613' => 'Original URL 3 is invalid',
            '614' => 'Original URL 4 is invalid',
            '615' => 'Incorrect JSON format',
            '616' => 'Memo is disabled',
            '624' => 'Duplicated SMSID',
            '632' => 'Rakuten title is invalid',
            '633' => 'Rakuten text is invalid',
            '634' => 'Main text is too long for Rakuten',
            '635' => 'Reminder text is too long for',
            '636' => 'Rakuten is invalid',
            '666' => 'Prior to block IP address',
        ];
    }

}
