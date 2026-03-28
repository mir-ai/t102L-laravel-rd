@props([
  'key', 
  'label', 
  'value' => 'Y', 
  'class' => '', 
  'div_class' => '', 
  'checked', 
  'attribute' => '',
  'col' => 12,
  'prompt' => '',
])

<div class="col-md-{{$col}} mb-1 {{$div_class}}">
  <label class="form-check form-switch">
    <input 
      class="form-check-input col-md-{{$col}} @error($key) is-invalid @enderror  {{$class}}"
      type="checkbox"
      value="{{$value}}"
      id="{{$key}}"
      name="{{$key}}" @checked($checked)
      {{$attribute}}
    >&nbsp;<span class="">
      {{$label}}
    </span>
    @if ($prompt)
      <span class="text-secondary ms-3">{{$prompt}}</span>
    @endif
  </label>
</div>
