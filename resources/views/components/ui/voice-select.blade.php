@props(['record', 'voice_id_column'])

{{-- 男性の声か女性の声かを選択する。 --}}
@php
  $voice_male = config('_env.VOICE_ID_JP_MALE');
  $voice_female = config('_env.VOICE_ID_JP_FEMALE');
  $oldvoice = old($voice_id_column, $record[$voice_id_column] ?? $voice_male);
@endphp

<div class="row">
  <div class="col-sm-12 btn-group" data-toggle="buttons">
    <label class="btn btn-outline-secondary btn-sm"><input type="radio" name="{{ $voice_id_column }}"
        value="{{ $voice_male }}" @checked($oldvoice != $voice_female)> 男性の声</label>
    <label class="btn btn-outline-secondary btn-sm"><input type="radio" name="{{ $voice_id_column }}"
        value="{{ $voice_female }}" @checked($oldvoice == $voice_female)> 女性の声</label>
  </div>
</div>
