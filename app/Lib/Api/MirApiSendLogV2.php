<?php namespace App\Lib\Api;

use App\Enum\ApiSendStatus;
use App\Lib\Mir\MirUtil;
use App\Models\ApiSendRequestLog;

class MirApiSendLogV2
{
    private ApiSendRequestLog $api_send_request_log;
    
    public function __construct(
        private string $full_url, 
        private string $method, 
        private array $headers = [], 
        private array $request_payloads = [], 
        private int $user_id = 0, 
        private int $client_id = 0,
        private string $log_prefix = '',
    )
    {
        $this->api_send_request_log = ApiSendRequestLog::create([
            'full_url' => $full_url,
            'method' => $method,
            'user_id' => $user_id,
            'client_id' => $client_id,
            'headers' => $headers,
            'request_payloads' => $request_payloads,
            'send_request_status' => ApiSendStatus::Init,
            'yymm' => intval(today()->format('ym')),
        ]);

        MirUtil::logDebug("API_SEND {$method} {$full_url}", $request_payloads);
    }

    public function logException(string $e)
    {
        $id = $this->api_send_request_log->id;

        MirUtil::logAlert("API_SEND EXCEPTION {$this->log_prefix} ID={$id}: {$e}");

        $this->api_send_request_log->send_request_status = ApiSendStatus::Exception;
        $this->api_send_request_log->response_reason = $e;
        $this->api_send_request_log->save();
        return;
    }    

    public function logResponse($response)
    {
        $id = $this->api_send_request_log->id;
        $http_status = $response->status();
        $response_body = $response->body();

        if ($response->successful()) {
            MirUtil::logDebug("API_SEND OK ID={$id} {$this->log_prefix}");
            $this->api_send_request_log->send_request_status = ApiSendStatus::Success;

        } else if ($response->clientError()) {
            MirUtil::logDebug("API_SEND CLIENT ERROR ID={$id} {$this->log_prefix}: {$http_status} {$response_body}");
            $this->api_send_request_log->send_request_status = ApiSendStatus::ClientError;
        } else if ($response->serverError()) {
            MirUtil::logDebug("API_SEND ID={$id} SERVER ERROR {$this->log_prefix}: {$http_status} {$response_body}");
            $this->api_send_request_log->send_request_status = ApiSendStatus::ServerError;
        }

        $this->api_send_request_log->http_status = $http_status;
        $this->api_send_request_log->response_body = $response_body;
        $this->api_send_request_log->save();
    }
}
