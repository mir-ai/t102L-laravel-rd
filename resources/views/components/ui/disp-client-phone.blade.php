@props(['phone', 'is_valid', 'prefix', 'is_receive'])

@php
  $phone = MirUtil::phone_to_human($phone);
@endphp

@if ($phone)
  <span class="font-monospace">{{$prefix}}</span>
  @if (! $is_valid)
    <span class="text-danger">{{$phone}}</span> <span class="badge rounded-pill bg-danger">番号無効</span><br />
  @elseif (! $is_receive)
    {{$phone}} <span class="badge rounded-pill bg-dark">受信停止</span><br />
  @else
    <span class="text-success">{{$phone}}</span><br />
  @endif
@endif
