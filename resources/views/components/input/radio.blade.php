@props([
  'key',
  'value',
  'label',
  'class' => '', 
  'checked',
  'div_class' => '', 
  'pre',
  'attribute' => '',
  'col' => 12,
])

<div class="col-md-{{$col}} mb-1">
  <div class="form-check {{$div_class}}">
    <input
      class="form-check-input col-md-{{$col}} @error($key) is-invalid @else border-secondary-subtle @enderror {{$class}}"
      type="radio"
      name="{{$key}}"
      value="{{$value}}"
      id="{{$key}}_{{$value}}"
      autocomplete="off"
      @checked($checked)
      {{$attribute}}
    >
    <label class="form-check-label" for="{{$key}}_{{$value}}">
      {{$label}}
    </label>
  </div>
</div>
