@props([
  'key', 
  'class' => '', 
  'attribute' => '',
  'col' => 12,
])

{{--TODO: --}}
<div class="col-md-{{$col}} mb-1">
  <input
    id="{{$key}}"
    name="{{$key}}"
    type="password"
    value=""
    class="form-control col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
    {{$attribute}}
  >
</div>

<div class="col-md-{{$col}} mb-1">
  <input
    id="{{$key}}_confirmation"
    name="{{$key}}_confirmation"
    type="password"
    value=""
    class="form-control col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
    {{$attribute}}
  >
</div>

