@php
  $route_name = request()->route()->getName();
@endphp

@if (!empty($service_code))
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a href="{{ route('r4.inbound_service_mails.index', ['service_code' => $service_code]) }}"
        @class([
            'nav-link',
            'active' => str_starts_with($route_name, 'r4.inbound_service_mails.'),
        ])>着信メール</a>
    </li>
    <li class="nav-item">
      <a href="{{ route('r4.notifications.index', ['service_code' => $service_code]) }}"
        @class([
            'nav-link',
            'active' => str_starts_with($route_name, 'r4.notifications.'),
        ])>アラート</a>
    </li>
    <li class="nav-item">
      <a href="{{ route('r4.deliveries.index', ['service_code' => $service_code]) }}" @class([
          'nav-link',
          'active' => str_starts_with($route_name, 'r4.deliveries.'),
      ])>通知状況</a>
    </li>
    <li class="nav-item">
      <a href="{{ route('r4.supporters.index', ['service_code' => $service_code]) }}"
        @class([
            'nav-link',
            'active' => str_starts_with($route_name, 'r4.supporters.'),
        ])>みまもり管理者</a>
    </li>
    <li class="nav-item">
      <a href="{{ route('r4.hoam_clients.index', ['service_code' => $service_code]) }}"
        @class([
            'nav-link',
            'active' => str_starts_with($route_name, 'r4.hoam_clients.'),
        ])>みまもり端末</a>
    </li>
    <li class="nav-item">
      <a href="{{ route('r4.water_clients.index', ['service_code' => $service_code]) }}"
        @class([
            'nav-link',
            'active' => str_starts_with($route_name, 'r4.water_clients.'),
        ])>水道利用者</a>
    </li>
  </ul>
@endif
