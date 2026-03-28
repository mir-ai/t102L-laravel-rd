@props([
  'key', 
  'default', 
  'maxlen' => 0, 
  'class' => '', 
  'div_class' => '', 
  'attribute' => '',
  'ext' => '',
  'col' => 12,
  'placeholder' => '',
  'ext_class' => '', 
  'ext_id' => '', 
])

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <input
      id="{{$key}}"
      name="{{$key}}"
      type="text"
      value="{{old($key, $default)}}"
      placeholder="{{$placeholder}}"
      data-maxlen="{{$maxlen}}"
      data-counter="len_{{$key}}"
      class="form-control len_count col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror" {{$attribute}}
    >
    <span class="input-group-text play_polly" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}" data-yomi_elem_id="#{{$key}}"><i class="bi bi-play-circle"></i></span>
  </div>
</div>

