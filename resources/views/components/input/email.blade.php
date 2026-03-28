@props([
  'key', 
  'default', 
  'maxlen', 
  'class' => '', 
  'div_class' => '', 
  'attribute' => '',
  'ext' => '',
  'col' => 12,
  'ext_class' => '', 
  'ext_id' => '', 
])

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <input
      id="{{$key}}"
      name="{{$key}}"
      type="email"
      value="{{old($key, $default)}}"
      data-maxlen="{{$maxlen ?? 0}}"
      data-counter="len_{{$key}}"
      class="form-control col-md-{{$col}} len_count {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
      {{$attribute}} 
    >
    @if ($ext)
      <span class="input-group-text" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}">{{$ext}}</span>
    @endif
  </div>
</div>
