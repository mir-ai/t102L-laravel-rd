@props([
  'key', 
  'class' => '', 
  'attribute' => '',
  'autocomplete' => '',
  'col' => 12,
])

<div class="col-md-{{$col}} mb-1">
  <input
    id="{{$key}}"
    name="{{$key}}"
    type="password"
    value=""
    class="form-control col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
    @if ($autocomplete)
      autocomplete="{{$autocomplete}}"
    @endif
    {{$attribute}}
  >
</div>
