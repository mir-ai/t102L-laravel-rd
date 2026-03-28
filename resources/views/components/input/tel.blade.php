@props([
  'key', 
  'default', 
  'maxlen' => 0, 
  'class' => '', 
  'div_class' => '', 
  'attribute' => '',
  'placeholder' => '',
  'col' => 12,
  'ext' => '',
])

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <input
      id="{{$key}}"
      name="{{$key}}"
      type="tel"
      value="{{old($key, $default)}}"
      data-maxlen="{{$maxlen}}"
      data-counter="len_{{$key}}"
      placeholder="{{$placeholder}}"
      class="form-control col-md-{{$col}} len_count {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror" 
      {{$attribute}}
    >

    @if ($ext)
      <span class="input-group-text" id="{{$key}}_grp">{{$ext}}</span>
    @endif
  </div>
</div>
