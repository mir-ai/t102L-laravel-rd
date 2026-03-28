
@props(['active', 'href', 'label', 'hrefs'])

<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle {{ $active ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false" target="_top" href="#" target="_top">{{ $label }}</a>
  <ul class="dropdown-menu">
    @foreach ($hrefs as $_label => $_href)
      <li><a class="dropdown-item" href="{{$_href}}">{{$_label}}</a></li> 
    @endforeach
  </ul>
</li>
