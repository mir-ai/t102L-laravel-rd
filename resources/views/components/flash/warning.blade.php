{{-- エラー発生時などに表示する画面上部のワーニング（赤） --}}
@if (Session::has('flash_warning'))
  <div class="row">
    <div class="col-sm-12">
      <div class="alert alert-danger">{!! nl2br(e(Session::get('flash_warning'))) !!}</div>
    </div>
  </div>
@endif
