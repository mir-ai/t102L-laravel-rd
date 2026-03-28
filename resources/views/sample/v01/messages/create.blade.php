{{-- MessageController.php --}}

@extends('layouts.app')

@section('js')
  <x-js.js-input-len-count />
@endsection

{{-- メッセージ情報作成用フォーム表示画面 --}}
@section('content')

  {{-- 水平ナビ --}}
  @include('_inc.hnav')
  {{-- SUB_NAVI --}}

  {{-- 件名 --}}
  <h2 class="miraie5">
    <x-headline.back :href="route('sample.v01.messages.index')" />
    新しいメッセージを登録
  </h2>

  {{-- フォーム本体 --}}
  <x-form.open id="form_message" method="POST" :action="route('sample.v01.messages.store')" enctype="" class="" />
  @relativeInclude('_form', ['submitButton' => '保存', 'back_url' => route('sample.v01.messages.index')])
  <x-form.close />
@endsection
