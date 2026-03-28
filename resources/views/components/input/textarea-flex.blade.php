@props([
    'key',
    'default',
    'class' => '',
    'div_class' => '',
    'maxlen' => 0,
    'attribute' => '',
    'col' => 12,
    'ext' => '',
    'ext_class' => '', 
    'ext_id' => '', 
])

@pushOnce('scripts', 'x-js.js-textarea-flex')
  <x-js.js-textarea-flex />
@endPushOnce

<div class="col-md-{{ $col }} mb-1">
  <div class="FlexTextarea">
    <div class="FlexTextarea__dummy" aria-hidden="true">{{ old($key, $default) }}</div>
    <textarea 
      id="{{ $key }}"
      name="{{ $key }}"
      class="FlexTextarea__textarea form-control len_count {{ $class }} @error($key) is-invalid @else border-secondary-subtle @enderror "
      data-counter="len_{{$key}}"
      data-maxlen="{{ $maxlen }}"
      {{ $attribute }}
    >{{ old($key, $default) }}</textarea>
    @if ($ext)
      <span class="input-group-text" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}">{{ $ext }}</span>
    @endif
  </div>
</div>
