@props(['href', 'label', 'target'])

<a class="dropdown-item" target="{{ $target ?? '_top' }}" href="{{ $href }}">{{ $label }}</a>

