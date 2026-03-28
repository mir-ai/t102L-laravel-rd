<?php namespace App\Lib\Mir;

use App\DataTransfer\DeliveryResult;
use App\Enum\DeliveryStatus;
use App\Lib\Api\MirApiSendLogV2;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MirHoamV2
{
    private $timestamp;
    private $passString;

    private string $get_attribute_groups_api_url;
    private string $create_notice_api_url;
    private string $create_emergency_alert_url;
    private string $get_location_api_url;

    public function __construct(
        private string $ext_link_id,
        private string $secret,
        private string $ila_api_base_url_overwrite = '',
    )
    {
        $ila_api_base_url = config('_env.ILA_API_BASE_URL');

        if ($ila_api_base_url_overwrite) {
            $ila_api_base_url = $ila_api_base_url_overwrite;
            logger("MirHoamV2 ILA API URL overwrited: {$ila_api_base_url}");
        }

        $this->get_attribute_groups_api_url = "{$ila_api_base_url}/api/public/v1/getAttributeGroups";
        $this->create_notice_api_url = "{$ila_api_base_url}/api/public/v1/createNotice";
        $this->create_emergency_alert_url = "{$ila_api_base_url}/api/public/v1/createEmergencyAlert";
        $this->get_location_api_url = "{$ila_api_base_url}/api/public/v1/getLocations";
    }

    /**
     * APIコールのクレデンシャル作成
     *
     * @param string $extLinkId
     * @return array<timestamp, passString>
     */
    private function initCredentials(): array
    {
        $timestamp = (string)time();

        $x = "{$this->ext_link_id}|{$timestamp}|{$this->secret}";
        $passString = md5($x);            
        //「クライアント ID + “|” + 現在日時のタイムスタンプ情報 + “|” + クライアントシークレートキー」を文字列連結し、MD5

        return [$timestamp, $passString];
    }

    public function getAttributeGroups(): DeliveryResult
    {
        $apiUrl = $this->get_attribute_groups_api_url;

        [$timestamp, $passString] = $this->initCredentials();

        $postdata = [
            'ext_link_id' => $this->ext_link_id,
            'timestamp' => $timestamp,
            'pass_string' => $passString,
        ];

        //MirUtil::logDebug("ILA getAttributeGroups=", $postdata);

        $headers = [
                'Content-Type' => 'application/json',
        ];

        $mir_api_send_log = new MirApiSendLogV2(
            full_url: $apiUrl,
            method: 'POST',
            headers: $headers,
            request_payloads: $postdata,
            log_prefix: 'ILA_GET_ATTR',
        );

        $detail = '';
        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($apiUrl, $postdata);

            $mir_api_send_log->logResponse($response);

            if ($response->successful()) {
                $ret = $response->json();

                if (($ret['result'] ?? '') == 'success') {
                    return new DeliveryResult(
                        delivery_status: DeliveryStatus::Success,
                    );
                } else {
                    $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
                }
            } else {
                $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
            }

        } catch (\Exception $e) {
            $mir_api_send_log->logException($e);
            $detail = "エラー: {$e}";
        }

        return new DeliveryResult(
            delivery_status: DeliveryStatus::FailedEnd,
            detail: $detail
        );
    }

    // 【ささえiコミュニティ】お知らせ投稿パートナーAPI仕様書_v2.0.pdf
    // https://app.box.com/s/qdn0vhkvh05ava45887bkgmwlynsc35s
    // お知らせ投稿API
    public function callCreateNotice(
        string $immediate_period_end_date_time,
        string $notice_title,
        string $notice_body,
        string|null $notice_website_name1 = null,
        string|null $notice_website_url1 = null,
        string|null $notice_website_name2 = null,
        string|null $notice_website_url2 = null,
        string|null $audio_file = null,
        string|null $image_file = null,
        string|null $notice_creator_name = null,
        string|null $device_ids = '',
        string|null $attribute_group_ids = '',    
    ): DeliveryResult
    {
        $apiUrl = $this->create_notice_api_url;
        
        [$timestamp, $passString] = $this->initCredentials();

        $param = [
            'ext_link_id' => $this->ext_link_id,
            'timestamp' => $timestamp,
            'pass_string' => $passString,
            'notice_type' => 'important_notice',
            'delivery_type' => 'important_notice',
            'delivery_plan_type' => 1, // 即時配信
            'immediate_peroid_end_date_time' => $immediate_period_end_date_time,
            'notice_title' => $notice_title,
            'notice_body' => $notice_body,
        ];

        // 配信者名
        if ($notice_creator_name) {
            $param['notice_creator_name'] = $notice_creator_name;
        }

        // Webサイト情報
        $items = [];

        if ($notice_website_name1 && $notice_website_url1) {
            $items[] = str_replace('"', '\"', $notice_website_name1);
            $items[] = $notice_website_url1;
        }

        if ($notice_website_name2 && $notice_website_url2) {
            $items[] = str_replace('"', '\"', $notice_website_name2);
            $items[] = $notice_website_url2;
        }
        
        if ($items) {
            $param['notice_website_name_urls'] = '"' . implode('","', $items) . '"';
        }

        // 配信先種別
        $delivery_to_type = 0;
        if ($attribute_group_ids) {
            $param['attribute_group_ids'] = $attribute_group_ids;
            $delivery_to_type = 1;
        }

        if ($device_ids) {
            $param['device_ids'] = $device_ids;
            $delivery_to_type = 2;
        }

        $param['delivery_to_type'] = $delivery_to_type;

        $headers = [
            'Content-Type' => 'multipart/form-data',
        ];

        $mir_api_send_log = new MirApiSendLogV2(
            full_url: $apiUrl,
            method: 'POST_FORM',
            headers: $headers,
            request_payloads: $param,
            log_prefix: 'ILA_SEND_NOTICE',
        );

        $detail = null;
        try {
            /** @var Response $response */
            $response = Http::withHeaders($headers)
            ->asForm()->post($apiUrl, $param);

            $mir_api_send_log->logResponse($response);

            if ($response->successful()) {
                $ret = $response->json();
                $result = $ret['result'] ?? '';

                if ($result == 'success') {
                    return new DeliveryResult(
                        delivery_status: DeliveryStatus::Success,
                    );
                } else {
                    $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
                }
            } else {
                $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            $mir_api_send_log->logException($e);
            $detail = "エラー: {$e}";
        }

        return new DeliveryResult(
            delivery_status: DeliveryStatus::FailedEnd,
            detail: $detail
        );
    }

    // 【ささえiコミュニティ】お知らせ投稿パートナーAPI仕様書_v2.0.pdf
    // https://app.box.com/s/qdn0vhkvh05ava45887bkgmwlynsc35s
    public function callEmergencyAlert(
        string $notice_title,
        string $notice_body,
        string|null $notice_creator_name,
        int $delivery_to_type = 1, // 個別配信
        string|null $attribute_group_ids = '',   
        string|null $device_ids = '',
        string $category = '',
    )
    {
        $apiUrl = $this->create_emergency_alert_url;
        
        [$timestamp, $passString] = $this->initCredentials();

        $param = [
            'ext_link_id' => $this->ext_link_id,
            'timestamp' => $timestamp,
            'pass_string' => $passString,
            'notice_title' => $notice_title,
            'notice_body' => $notice_body,
        ];

        $delivery_to_type = 0;
        if ($attribute_group_ids) {
            $param['attribute_group_ids'] = $attribute_group_ids;
            $delivery_to_type = 1;
        }

        if ($device_ids) {
            $param['device_ids'] = $device_ids;
            $delivery_to_type = 2;
        }

        $param['delivery_to_type'] = $delivery_to_type;

        if ($notice_creator_name) {
            $param['notice_creator_name'] = $notice_creator_name;
        }

        if ($category) {
            $param['category'] = $category;
        }

        $headers = [
            'Content-Type' => 'multipart/form-data',
        ];

        $mir_api_send_log = new MirApiSendLogV2(
            full_url: $apiUrl,
            method: 'POST_FORM',
            headers: $headers,
            request_payloads: $param,
            log_prefix: 'ILA_SEND_EMERGENCY',
        );

        $detail = null;
        try {
            /** @var Response $response */
            $response = Http::withHeaders($headers)
            ->asForm()->post($apiUrl, $param);

            $mir_api_send_log->logResponse($response);

            if ($response->successful()) {
                $ret = $response->json();
                $result = $ret['result'] ?? '';

                if ($result == 'success') {
                    return new DeliveryResult(
                        delivery_status: DeliveryStatus::Success,
                    );
                } else {
                    $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
                }
            } else {
                $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            $mir_api_send_log->logException($e);
            $detail = "エラー: {$e}";
        }

        return new DeliveryResult(
            delivery_status: DeliveryStatus::FailedEnd,
            detail: $detail
        );
    }

    /**
     * ロケーションごとのHoamデバイスIDを取得する
     *
     * @param array $location_ids
     * @return array $locationKvs array<$location_ids, $hoam_devlice_ids>
     */
    public function getLocations(
        array $location_ids,
    ): array
    {
        $apiUrl = $this->get_location_api_url;
        $chunk_size = 20;

        //MirUtil::logDebug("ILA getAttributeGroups=", $postdata);

        $headers = [
                'Content-Type' => 'application/json',
        ];

        $locationKvs = [];
        $location_ids_chunks = array_chunk($location_ids, $chunk_size);
        foreach ($location_ids_chunks as $location_ids_chunk) {
            [$timestamp, $passString] = $this->initCredentials();

            $postdata = [
                'ext_link_id' => $this->ext_link_id,
                'timestamp' => $timestamp,
                'pass_string' => $passString,
                'location_ids' => implode(',', $location_ids_chunk)
            ];

            $mir_api_send_log = new MirApiSendLogV2(
                full_url: $apiUrl,
                method: 'POST',
                headers: $headers,
                request_payloads: $postdata,
                log_prefix: 'ILA_GET_LOCATIONS',
            );

            try {
                /** @var Response $response */
                $response = Http::withHeaders($headers)
                ->post($apiUrl, $postdata);

                $mir_api_send_log->logResponse($response);

                if ($response->successful()) {
                    $ret = $response->json();
                    $result = $ret['result'] ?? '';

                    if ($result == 'success') {
                        foreach (($ret['locations'] ?? []) as $location) {
                            $location_id = $location['location_id'];
                            $device_id = $location['device_id'];

                            $locationKvs[$location_id] = $device_id;
                        }
                    }
                } else {
                    $detail = "エラー: " . json_encode($response->json(), JSON_UNESCAPED_UNICODE);
                }
            } catch (\Exception $e) {
                $mir_api_send_log->logException($e);
                $detail = "エラー: {$e}";
            }

            sleep(1);
        }

        return $locationKvs;
    }

}
