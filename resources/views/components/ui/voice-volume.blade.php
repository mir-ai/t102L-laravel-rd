@props(['record'])

@php
  $old = old('bosai_volume_db', $record->bosai_volume_db ?? config('_env.BOSAI_VOLUME_DB'));
@endphp

<div class="row mt-2">
  <div class="col-sm-12 btn-group" data-toggle="buttons">
    @for ($dt = -9; $dt <= 0; $dt++)
      <label class="btn btn-outline-secondary btn-sm" style="width:50%"><input type="radio"
          name="bosai_volume_db" value="{{ $dt }}" @if ($old == $dt) checked @endif>
        {{ $dt }}dB</label>
    @endfor
  </div>
</div>