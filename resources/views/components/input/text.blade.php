@props([
  'key', 
  'default', 
  'maxlen' => 0, 
  'class' => '', 
  'div_class' => '', 
  'attribute' => '',
  'pre' => '',
  'ext' => '',
  'ext_class' => '', 
  'ext_id' => '', 
  'col' => 12,
  'placeholder' => '',
  'pattern' => '',
  'list' => '',
  'autocomplete' => '',
])

@php
  if ($default instanceof DateTime) {
    $default = $default->format('Y-m-d H:i:s');
  }
  $value = old($key, $default);
  if (is_array($value)) {
    $value = json_encode($value, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  }
  
@endphp

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    @if ($pre)
      <span class="input-group-text" id="{{$key}}_pre">{{$pre}}</span>
    @endif

    <input
      id="{{$key}}"
      name="{{$key}}"
      type="text"
      value="{{$value}}"
      @if ($pattern)
      pattern="{{$pattern}}"
      @endif      
      placeholder="{{$placeholder}}"
      data-maxlen="{{$maxlen}}"
      data-counter="len_{{$key}}"
      class="form-control len_count col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror" {{$attribute}}
      @if ($list)
      list="{{$list}}"
      @endif
      @if ($autocomplete)
      autocomplete="{{$autocomplete}}"
      @endif
    >

    @if ($ext)
      <span class="input-group-text" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}">{!! $ext !!}</span>
    @endif
  </div>
</div>
