@props(['active', 'href', 'label', 'class' => ''])

<li class="nav-item">
  <a class="nav-link {{ $active ? 'active' : '' }} {{$class}}" href="{{ $href }}" target="_top">
    {{ $label }}
  </a>
</li>
