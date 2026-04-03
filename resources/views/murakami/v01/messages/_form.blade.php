{{-- MessageV4Controller.php --}}

{{-- メッセージ 登録・編集フォーム --}}

{{-- 検索条件を次に引き継ぐhiddenタグ --}}

<div class="mt-4 lg-1">
  {{-- 件名 --}}
  <x-input.open key="message_title" label="件名" required="N" />
  <x-input.text key="message_title" :default="old('message_title', $message->message_title ?? '')" maxlen="255" class="form-control-lg" />
  <x-input.close key="message_title" desc="件名を入力します。" maxlen="255" />

  {{-- 本文 --}}
  <x-input.open key="message_body" label="内容" required="N" />
  <x-input.textarea key="message_body" :default="old('message_body', $message->message_body ?? '')" maxlen="16000" class="form-control-lg" />
  <x-input.close key="message_body" desc="内容を入力します。" maxlen="16000" />

  <div class="mt-4 mb-2">
    <x-input.submit-back :label="$submitButton" :backUrl="$back_url ?? ''" class="btn-lg" />
  </div>

</div>
