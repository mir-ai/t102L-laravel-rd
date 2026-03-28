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
])

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <input
      id="{{$key}}"
      name="{{$key}}"
      type="search"
      value="{{old($key, $default)}}"
      placeholder="{{$placeholder}}"
      data-maxlen="{{$maxlen}}"
      data-counter="len_{{$key}}"
      class="form-control len_count col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror" {{$attribute}}
    >
    @if ($ext)
      <span class="input-group-text" id="{{$key}}_grp">{{$ext}}</span>
    @endif
  </div>
</div>
