@extends('layouts.app')

@section('content')

<h1 class="miraie5">ララベル サーバ</h1>

<div class="row">
  {{-- 村上 --}}
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">村上</h5>
        <ul class="lg-lg">
          <li>v01 26-04-03 <a href="{{route('murakami.v01.logs.index')}}">ログ表示</a></li>
          <li>v01 26-04-03 <a href="{{route('murakami.v01.messages.index')}}">お知らせ作成</a></li>
        </ul>
      </div>
    </div>
  </div>

  {{-- 小林 --}}
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">小林</h5>
        <ul class="lg-lg">
          <li>v01 26-04-03 <a href="{{route('kobayashi.v01.logs.index')}}">ログ表示</a></li>
          <li>v01 26-04-03 <a href="{{route('kobayashi.v01.messages.index')}}">お知らせ作成</a></li>
        </ul>
      </div>
    </div>
  </div>

  {{-- サンプル --}}
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">サンプル</h5>
        <ul class="lg-lg">
          <li>v01 26-03-28 <a href="{{route('sample.v01.logs.index')}}">ログ表示</a></li>
          <li>v01 26-03-28 <a href="{{route('sample.v01.messages.index')}}">お知らせ作成</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>



@endsection
