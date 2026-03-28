@props([
  'key', 
  'value', 
  'class' => '', 
  'attribute' => '',
  'div_class' => '', 
  'col' => 12,
])

<p class="{!! e(nl2br($class)) !!}">{{$value}}</p>
