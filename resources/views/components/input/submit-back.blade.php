@props([
  'key' => '', 
  'label', 
  'class' => 'mt-3', 
  'class_submit' => '', 
  'attribute' => '',
  'backUrl' => 'javascript:history.back();',
  'backLabel' => '戻る',
])

<div class="row">
  <div class="col-md-9 order-lg-2">
    {{-- エンターキーでの入力を防ぐため --}}
    <button
      type="button"
      id="{{$key}}"
      name="{{$key}}"
      class="btn form-control btn-success {{$class}} {{$class_submit}} mb-3"
      onclick="submit();"
      {{$attribute}}
    >{{$label}}</button>
  </div>
  <div class="col-md-3 order-lg-1">
    <a href="{{$backUrl}}" class="btn btn-warning form-control {{$class}} mb-3">{{$backLabel}}</a>
  </div>
</div>
