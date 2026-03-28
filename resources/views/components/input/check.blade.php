@props([
    'key',
    'name' => '',
    'value',
    'prompt' => '',
    'label' => '',
    'checked',
    'pre',
    'class' => '',
    'labelclass' => '',
    'attribute' => '',
    'col' => 12,
])

@php
  // エルビス演算子 ?:
  // $name に値がなければ右辺を代入
  $name = $name ?: $key;
@endphp

<div class="col-md-{{ $col }} mb-1">
  <label class="form-control">
    <input type="checkbox" name="{{ $name }}" id="{{ $key }}" value="{{ $value }}"
      class="form-check-input col-md-{{ $col }} {{ $class }} @error($key) is-invalid @else border-secondary-subtle @enderror form-control-input "
      @checked($checked) {{ $attribute }}>
    &nbsp;{!! $pre ?? '' !!}<span class="ms-2 {{ $labelclass }}">{{ $prompt }}{{ $label }}</span>
  </label>
</div>
