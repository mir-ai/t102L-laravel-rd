{{-- LogV4Controller.php --}}

@extends('layouts.app')

{{-- ログ１件表示画面 --}}
@section('content')
  {{-- 水平ナビ --}}
  @include('_inc.hnav')
  {{-- SUB_NAVI --}}

  {{-- 件名 --}}
  <h2 class="miraie5">
    <x-headline.back :href="route('kobayashi.v01.logs.index')" />
    {{ $log->id }}番のログ
  </h2>

  {{-- 詳細データ表示 --}}
  {{-- LogV4Controller.php --}}

  {{-- ログ １件詳細表示フォーム --}}
  <div class="card mb-4">
    <div class="card-body">
      <div class="fs-5 mb-3 fw-bold">ログ</div>
      {{-- ID  --}}
      <x-show.open class="ms-5" label="ID" />
      <x-show.show :value="$log->id" />
      <x-show.close />

      {{-- ログ種別  --}}
      <x-show.open class="ms-5" label="ログ種別" />
      <x-show.show :value="$log->log_type" />
      <x-show.close />

      {{-- 本文  --}}
      <x-show.open class="ms-5" label="本文" />
      <x-show.show :value="$log->log_body" />
      <x-show.close />

      {{-- 更新日時  --}}
      <x-show.open class="ms-5" label="更新日時" />
      <x-show.show :value="MirUtil::hilightDt($log->updated_at)" />
      <x-show.close />
    </div>
  </div>
@endsection
