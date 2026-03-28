@props([
  'key',
  'class' => '',
  'accept' => '*',
  'attribute' => '',
  'div_class' => '', 
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
      type="file"
      class="form-control col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
      accept="{{$accept}}"
      {{$attribute}}
    >

    @if ($ext)
    <span class="input-group-text" id="{{ empty($ext_id) ? $key . '_grp' : $ext_id }}" class="{{$ext_class}}">{{$ext}}</span>
    @endif
  </div>
</div>
