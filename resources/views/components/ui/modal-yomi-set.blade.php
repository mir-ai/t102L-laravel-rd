<!-- Modal -->
<div class="modal fade" id="yomiModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">ヨミ調整</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3 row">
          <div class="col-sm-2">
            <label for="src">単語</label>
          </div>
          <div class="col-sm-7">
            <input id="src" name="src" type="text" value="" class="form-control @error('src') is-invalid @enderror">
            <small class="text-muted">ヨミガナを割り当てたい単語を入力します。</small>
          </div>

          <div class="col-sm-3">
            <a href="#" class="btn btn-primary" id="yomi_update">更新</a>
          </div>
        </div>

        <div class="mb-3 row">
          <div class="col-sm-2">
            <label for="dst"></label>
          </div>
          <div class="col-sm-10">
            <h4 id="tone"></h4>
          </div>
        </div>

        <div class="mb-3 row">
          <div class="col-sm-2">
            <label for="dst">ヨミ</label>
          </div>
          <div class="col-sm-7">
            <input id="dst" name="dst" type="text"
              value="{{ old('dst') }}"
              class="form-control @error('dst') is-invalid @enderror">
            <small class="text-muted">ヨミガナを<span
                class="text-danger">全角カタカナで入力</span>します。アクセントを置きたいカタカナの直後に＋をつけると、強く発音します。(マイニチシ＋ンブン)。＋を複数置きたいときは、単語をスペースで区切って下さい（マ＋イニチ　シンブン＋）</small>
            <audio id="test_player" class="d-none" controls></audio>
          </div>
          <div class="col-sm-3">
            <a href="#" class="btn btn-warning" id="read_dst"><i class="bi bi-play-fill"></i> 試聴</a>
          </div>
        </div>

        <div class="mb-3 row">
          <div class="col-sm-12">
            <button type="button" class="btn btn-success form-control" id="yomi_save" data-bs-dismiss="modal">保存</button>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >閉じる</button>
      </div>
    </div>
  </div>
</div>
