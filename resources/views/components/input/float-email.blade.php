@props([
  'key', 
  'label', 
  'default', 
  'placeholder', 
  'maxlen', 
  'class' => '', 
  'attribute' => '',
  'col' => 12,
])

<div class="col-md-{{$col}} mb-1">
  <div class="form-floating">
    <input
      type="email"
      id="{{$key}}"
      name="{{$key}}"
      value="{{old($key, $default)}}"
      class="form-control col-md-{{$col}} @error($key) is-invalid @else border-secondary-subtle @enderror" placeholder="{{$placeholder}}"
      {{$attribute}}
    >
    <label for="{{$key}}">{{$label}}</label>
  </div>
</div>
