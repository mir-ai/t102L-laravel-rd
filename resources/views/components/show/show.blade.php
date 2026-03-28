@props(['value'])

@if ($value instanceof \UnitEnum)
  {{-- enumは値を表示 --}}
  {!! $value->value ?? '' !!}
@elseif (is_array($value))
  {{-- 配列はJSONにして表示 --}}
  <pre style="line-height: 1;">{!! nl2br(e(json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))) !!}</pre>
@else
  {{-- 通常変数 --}}
  {!! $value ?? '' !!}
@endif
