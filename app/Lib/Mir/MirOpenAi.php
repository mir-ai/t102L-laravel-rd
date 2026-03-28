<?php namespace App\Lib\Mir;

class MirOpenAi
{
    private $system;

    function __construct($system = 'あなたは賢いAIです。')
    {
        $this->system = $system;
    }

    public function query($question)
    {
        $response = $this->callOpenAIApi($question);

        // 結果をデコード
        $result = json_decode($response, true);

        // logger("MirOpenAI request question= {$question}");

        $result_message = $result["choices"][0]["message"]["content"] ?? "";

        if (! $result_message) {
            $question_nobr = str_replace(["\n", "\r"], '', $question);

            MirUtil::logAlert("MirOpenAI question={$question_nobr} result=", $result ?? []);
        } else {
            MirUtil::logDebug("[OPENAI] question={$question} result=", $result ?? ['NULL']);
        }

        return [
            'result' => 0,
            'message' => $result_message
        ];
    }

    private function callOpenAIApi(
        $question,
        $model = 'gpt-4o-mini'
    )
    {
        $timeout_sec = 10;
        $apikey = config('_env.OPENAI_API_KEY');
        $url = "https://api.openai.com/v1/chat/completions";
        
        // リクエストヘッダー
        $headers = array(
          'Content-Type: application/json',
          'Authorization: Bearer ' . $apikey
        );
        
        // リクエストボディ
        $data = array(
          'model' => $model,
          'messages' => [
              ["role" => "system", "content" => $this->system],
              ['role' => 'user', 'content' => $question],
          ],
          'max_tokens' => (int)config('_env.OPENAI_API_MAX_TOKENS'),
        );

        // cURLを使用してAPIにリクエストを送信 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        
        // 	接続の試行を待ち続ける秒数。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout_sec);

        // 	cURL 関数の実行にかけられる時間の最大値。
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout_sec);
        
        $response = curl_exec($ch); 

        return $response;

    }
}
