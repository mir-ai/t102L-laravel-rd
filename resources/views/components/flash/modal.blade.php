{{-- 登録や編集などの完了後に表示するモーダルダイアログ (v4) --}}
@if (Session::has('flash_modal'))
  <div class="modal" id="flash_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-check-circle-fill text-success"></i> 処理成功</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="lg-1">{!! nl2br(e(Session::get('flash_modal'))) !!}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
@endif
