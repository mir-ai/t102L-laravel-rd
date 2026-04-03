{{-- MessageController.php --}}

@extends('layouts.app')

{{-- ログ情報一覧表示画面 --}}
@section('content')
  {{-- 水平ナビ --}}
  @include('_inc.hnav')
  {{-- SUB_NAVI --}}

  {{-- 件名 --}}
  <h2 class="miraie5">メッセージ</h2>
  <p class="text-secondary">ラズパイ端末に送信するメッセージを作成します。</p>

  <div class="row">
    <div class="col-lg-12 text-start">
      <a href="{{route('murakami.v01.messages.create')}}" class="btn btn-success mb-3">お知らせを新規作成</a>
    </div>
  </div>

  @forelse ($messages as $message)
    @if ($loop->first)
      {{-- テーブル開く --}}
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr class="table-success">
              <th nowrap>ID</th>
              <th nowrap>件名</th>
              <th nowrap>内容</th>
              <th nowrap>更新日時</th>
          </thead>
          <tbody>
    @endif

    {{-- 情報を１件表示 --}}
    <tr>
      <td nowrap>{{ $message->id }}</td>
      <td>{{ $message->message_title }}</td>
      <td>{{ $message->message_body }}</td>
      <td nowrap>{{ $message->updated_at->format('Y/m/d H:i:s') }}</td>
    </tr>

    {{-- テーブル閉じる --}}
    @if ($loop->last)
      </tbody>
      </table>
      </div>
    @endif

  @empty

    {{-- 見つからなかった --}}
    <p>条件に合うお知らせは見つかりません。</p>
  @endforelse
@endsection
