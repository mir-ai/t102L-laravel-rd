@props(['email', 'is_valid', 'is_receive'])

@php
  $email = mb_strimwidth($email, 0, 30, '…');
@endphp

@if ($email)
  @if (! $is_valid)
    <span class="text-danger">{{$email}}</span> <span class="badge rounded-pill bg-danger">アドレス無効</span><br />
  @elseif (! $is_receive)
    {{$email}} <span class="badge rounded-pill bg-dark">受信停止</span><br />
  @else
    <span class="text-success">{{$email}}</span><br />
  @endif
@endif