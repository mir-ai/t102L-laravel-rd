{{-- 登録や編集などの完了後に画面上部に表示するメッセージ（緑） --}}
@if (Session::has('flash_message'))
  <div class="row">
    <div class="col-sm-12">
      <div class="alert alert-success">{!! nl2br(e(Session::get('flash_message'))) !!}</div>
    </div>
  </div>
@endif
