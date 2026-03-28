{{-- 記事を表示するモーダルウィンドウ --}}
<div class="modal fade modal-lg" id="postViewModal" tabindex="-1" aria-labelledby="postViewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="postViewModalTitle"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-end mb-3">
          <span id="postViewModalDate"></span>
          <span class="ms-2"><a href="" target="_print" id="postViewModalPdfHref"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</a></span>
          <span class="ms-2"><a href="" target="_pdf" id="postViewModalPrintHref"><i class="bi bi-printer-fill"></i> 印刷</a></span>
        </div>
        <div id="postViewModalBody"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</div>
