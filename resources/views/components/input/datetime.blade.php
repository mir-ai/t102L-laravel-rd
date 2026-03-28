@props([
  'key',
  'defaultdt' => now(),
  'mindt' => now()->subMonths(36),
  'maxdt' => now()->addMonths(12),
  'class' => '',
  'attribute' => '',
  'ext' => '',
  'col' => 12,
])

@php
  $date_ymd = optional($defaultdt)->format('Y-m-d');
  $hour = optional($defaultdt)->format('H');
  $minute = optional($defaultdt)->format('i');
  
  $min_ymd     = $mindt->format('Y-m-d');
  $max_ymd     = $maxdt->format('Y-m-d');

  $key_date = "{$key}_date";
  $key_hour = "{$key}_hour";
  $key_minute = "{$key}_minute";
@endphp

@error($key)
  @php($border = 'is-invalid')
@else
  @php($border = 'border-secondary-subtle')
@enderror

<div class="col-md-5">
  <input 
    type="date"
    id="{{$key_date}}"
    name="{{$key_date}}"
    value="{{old($key_date, $date_ymd)}}"
    min="{{$min_ymd}}"
    max="{{$max_ymd}}" 
    class="form-control mb-2 col-md-{{$col}} {{$class}} {{$border}}"
    {{$attribute}}
  />
</div>
<div class="col-md-4">
  <x-input.select 
    :key="$key_hour"
    :options="MirUtil::jpHoursKv()"
    :default="old($key_hour, $hour)"
    class="form-control mb-2 {{$class}} {{$border}}"  
  />
</div>
<div class="col-md-3">
  <x-input.select 
    :key="$key_minute"
    :options="MirUtil::range(0, 59, 1, '%02d', '%02d分')"
    :default="old($key_minute, $minute)"
    :errorkey="$key"
    class="form-control mb-2 {{$class}} {{$border}}" 
  />
</div>
