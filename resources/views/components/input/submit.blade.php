@props([
  'key' => '', 
  'label', 
  'class' => 'btn btn-success form-control mt-3', 
  'attribute' => '',
  'col' => 12,
])

{{-- エンターキーでの入力を防ぐため --}}
<button
  type="submit"
  id="{{$key}}"
  name="{{$key}}"
  class="{{$class}} mb-2"
  {{$attribute}}
>{{$label}}</button>
