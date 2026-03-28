@props([
  'key',
  'options', 
  'default' => '',
  'prompt',
  'class' => '',
  'errorkey' => '',
  'attribute' => '',
  'div_class' => '', 
  'col' => 12,
])

@php($errorkey = ($errorkey) ?: $key)

<div class="col-md-{{$col}} mb-1 {{$div_class}}">
  <select 
    name="{{$key}}[]"
    id="{{$key}}"
    class="{{$class}} col-md-{{$col}} @error($errorkey) is-invalid @enderror form-select {{$select_large}}"
    multiple
    {{$attribute}}
  >

    @if (isset($prompt))
      <option value="">{{$prompt}}</option>
    @endif

    @foreach ($options as $k => $v)
      <option value="{{$k}}" @selected(in_array($k, explode('|', $default)))>{{$v}}</option>
    @endforeach
  </select>
</div>
