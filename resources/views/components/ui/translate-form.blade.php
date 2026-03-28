@props(['submitButton', 'record_type', 'record_id', 'tmp_trans_title_jp', 'tmp_trans_jp', 'translation_langs_csv', 'translation_langs', 'message_translation_kvs'])

<input type="hidden" name="record_type" value="{{ $record_type }}">
<input type="hidden" name="record_id" value="{{ $record_id }}">

@foreach ($translation_langs as $lang_code)
  @if ($loop->first)
  @endif

  <x-ui.translate-item :lang_code="$lang_code" :message_translate="$message_translation_kvs[$lang_code] ?? []" />

@endforeach

<div class="mb-3 row mt-4">
  <div class="col-sm-8 order-sm-2 mt-3">
    <x-input.submit-key label="保存" key="submit_btn" class="btn btn-success form-control btn-lg submit_on_finish" />

  </div>
  <div class="col-sm-4 order-sm-1 mt-3">
    <x-input.submit-key label="戻る" key="back" class="btn btn-warning form-control btn-lg" />

  </div>
</div>
