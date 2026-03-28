@props([
  'key',
  'defaultdt' => null,
  'min_dt' => now()->subMonths(36),
  'max_dt' => now()->addMonths(12),
  'class' => '',
  'div_class' => '', 
  'attribute' => '',
  'col' => 12,
  'ext' => '',
  'ext_class' => '', 
  'ext_id' => '', 
])

@php
  if (is_string($defaultdt) && $defaultdt) {
    $defaultdt = MirUtil::parseDt($defaultdt);
  }
  $default_ymd = optional($defaultdt)->format('Y-m-d');
  $min_ymd = $min_dt->format('Y-m-d');
  $max_ymd = $max_dt->format('Y-m-d');
@endphp

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <input
      type="date"
      id="{{$key}}"
      name="{{$key}}"
      value="{{old($key, $default_ymd)}}"
      min="{{$min_ymd}}"
      max="{{$max_ymd}}"
      class="form-control {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
      {{$attribute}}
    >

    @if ($ext)
      <span class="input-group-text" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}">{{$ext}}</span>
    @endif

  </div>
</div>
