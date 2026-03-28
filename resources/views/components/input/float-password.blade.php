@props([
  'key', 
  'label', 
  'placeholder', 
  'class' => '', 
  'attribute' => '',
  'col' => 12,
])

<div class="col-md-{{$col}} mb-1">
  <div class="form-floating">
    <input
      type="password"
      id="{{$key}}"
      name="{{$key}}"
      value=""
      placeholder="{{$placeholder}}" 
      class="form-control col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
      {{$attribute}}
    >
  <label for="{{$key}}">{{$label}}</label>
  </div>
</div>
