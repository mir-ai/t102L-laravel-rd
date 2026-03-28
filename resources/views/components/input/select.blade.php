@props([
  'key',
  'options', 
  'default',
  'prompt' => '',
  'class' => '',
  'div_class' => '', 
  'errorkey' => '',
  'attribute' => '',
  'col' => 12,
])

@php($errorkey = ($errorkey) ? $errorkey : $key)

<div class="col-md-{{$col}} mb-1 {{$div_class}}">
  <select 
    name="{{$key}}"
    id="{{$key}}"
    class="{{$class}} col-md-{{$col}} @error($errorkey) is-invalid @else border-secondary-subtle @enderror form-select"
    {{$attribute}}
  >

    @if (! empty($prompt))
      <option value="">{{$prompt}}</option>
    @endif

    @foreach ($options as $k => $v)
      <option value="{{$k}}" @selected(old($key, $default) == $k)>{{$v}}</option>
    @endforeach
  </select>
</div>
