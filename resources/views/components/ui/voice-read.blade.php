@props(['yomi_elem_id', 'voice_elem_id', 'player_elem_id', 'player_id', 'yomi_modal_id', 'data_device' => 'instant'])

{{-- 指定のテキストボックスの内容を読み上げる --}}

  {{-- 試聴（全文） --}}
  <div class="row">
  <div class="col-auto">
    <button @class([
        'btn',
        'btn-warning',
        'play_polly',
        'form-control',
        'mt-2',
      ])
      id="read_{{$yomi_elem_id}}"
      data-yomi_elem_id="#{{$yomi_elem_id}}"
      data-voice_elem_name="{{$voice_elem_id}}"
      data-player_elem_id="#{{$player_id}}"
      data-device="{{$data_device}}"
    >
    <i class="bi bi-caret-right-fill"></i> 試聴（全文）
    </button>
  </div>

  {{-- 試聴（選択範囲） --}}
  <div class="col-auto">
    <button @class([
        'btn',
        'btn-warning',
        'play_polly',
        'form-control',
        'mt-2',
      ])
      id="read_{{$yomi_elem_id}}_selected"
      data-yomi_elem_id="#{{$yomi_elem_id}}"
      data-voice_elem_name="{{$voice_elem_id}}"
      data-player_elem_id="#{{$player_id}}"
      data-device="{{$data_device}}"
    >
      <i class="bi bi-caret-right-fill"></i> 試聴（選択範囲）
    </button>
  </div>

  {{-- ヨミ調整ボタン --}}
  <div class="col-auto">
    <button type="button" @class([
        'btn',
        'btn-primary',
        'yomi_launch',
        'form-control',
        'mt-2'
      ])
      data-bs-toggle="modal"
      data-bs-target="#{{$yomi_modal_id}}">
      <i class="bi bi-music-note-list"></i> ヨミ調整
    </button>
  </div>

  {{-- オーディオプレイヤー --}}
  <div class="col">
    <audio id="{{$player_id}}" controls class="mt-2"></audio>
  </div>
</div>
