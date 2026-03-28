{{-- LogV4Controller.php --}}

@extends('layouts.app')

{{-- ログ情報一覧表示画面 --}}
@section('content')
  {{-- 水平ナビ --}}
  @include('_inc.hnav')
  {{-- SUB_NAVI --}}

  {{-- 件名 --}}
  <h2 class="miraie5">ログ</h2>
  <p class="text-secondary">ラズパイ端末から得られたログを表示します。</p>

  <div class="row">
    <div class="col-lg-12 text-end">
      <button onclick="location.reload();" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-clockwise"></i> 更新</button>
    </div>
  </div>

  @forelse ($logs as $log)
    @if ($loop->first)
      {{-- テーブル開く --}}
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr class="table-success">
              <th nowrap>ID</th>
              <th nowrap>日時</th>
              <th nowrap>ログ種別</th>
              <th nowrap>本文</th>
          </thead>
          <tbody>
    @endif

    {{-- 情報を１件表示 --}}
    @php
      $href = route('sample.v01.logs.show', ['log_id' => $log->id]);
    @endphp
    <tr>
      <td nowrap><a href="{{ $href }}">{{ $log->id }}</a></td>
      <td nowrap>{{ $log->updated_at->format('Y/m/d H:i:s') }}</td>
      <td>{{ $log->log_type }}</td>
      <td>{{ $log->log_body }}</td>
    </tr>

    {{-- テーブル閉じる --}}
    @if ($loop->last)
      </tbody>
      </table>
      </div>
    @endif

  @empty

    {{-- 見つからなかった --}}
    <p>条件に合うログは見つかりません。</p>
  @endforelse
@endsection
