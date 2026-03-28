@props([
  'key', 
  'value', 
  'class' => '', 
  'attribute' => '',
  'div_class' => '', 
  'col' => 12,
])

<div class="col-md-{{$col}} mb-1 {{$div_class}}">
  <input
    class="form-control-plaintext col-md-{{$col}} "
    type="text"
    value="{{$value}}"
    {{$attribute}}
    readonly
  >
</div>
