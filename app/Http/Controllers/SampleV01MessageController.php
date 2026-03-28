<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use PhpMqtt\Client\Facades\MQTT;

class SampleV01MessageController extends Controller
{
    /**
     * メッセージの一覧表示・検索画面
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // データベースから id の逆順で 100 件取得
        $messages = Message::query()
        ->orderBy('id', 'desc')
        ->limit(100)
        ->get();

        // resources/view/sample/v01/logs/index.blade.php を読む
        return view('sample.v01.messages.index', [
            'messages' => $messages
        ]);
    }

    /**
     * メッセージの登録フォーム
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // メッセージの登録画面を表示する
        return view('sample.v01.messages.create');
    }

    /**
     * メッセージの登録処理
     *
     * @param  \App\Http\Requests\MessageStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message_title' => 'required|string|max:255',
            'message_body' => 'required|string|max:16000',
        ]);

        $message = Message::create([
            'message_title' => $validated['message_title'],
            'message_body' => $validated['message_body'],
        ]);

        // MQTTを用いて、クライアントに更新があったことを伝達する
        $mqtt_payload = json_encode([
            'message_body' => $validated['message_body'],
            'timestamp' => time(),
        ]);

        $topic = $this->publishMqtt($mqtt_payload);

        // 一覧表示ページにリダイレクトする
        return redirect()
            ->route('sample.v01.messages.index')
            ->with('flash_message',  "メッセージ「{$message->message_title}」を作成しました。MQTTトピック'{$topic}'に通知を発行しました。");
    }

    /**
     * APIを通じて、最新のメッセージを１件返す
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function apiLatest(Request $request)
    {
        $message = Message::query()
        ->orderBy('id', 'desc')
        ->first();

        if (! $message) {
            $data = [
                'is_success' => 'N',
                'reason' => 'NOT FOUND',
            ];
        } else {
            $data = [
                'is_success' => 'Y',
                'id' => $message->id ?? 0,
                'message_title' => $message?->message_id,
                'message_body' => $message?->message_body,
                'created_at' => $message?->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * MQTTを用いて、クライアントに更新があったことを伝達する
     *
     * @param string $payload
     * @return string $topic
     */
    private function publishMqtt(string $mqtt_payload): string
    {
        $topic = config('_env.MQTT_TOPIC_DOWNSTREAM');
        MQTT::publish($topic, $mqtt_payload);
        logger()->info("MQTT publish topic='{$topic}', payload={$mqtt_payload}");

        return $topic;
    }

}
