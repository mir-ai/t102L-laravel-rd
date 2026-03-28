@props([
  'key' => '', 
  'label', 
  'class' => 'btn btn-success form-control mt-3', 
  'attribute' => '',
  'col' => 12,
])

{{-- コントローラー側にvalueを渡す必要がある場合に使用。 --}}
<input
  type="submit"
  id="{{$key}}"
  name="{{$key}}"
  class="{{$class}}"
  {{$attribute}}
  value="{{$label}}"
>
