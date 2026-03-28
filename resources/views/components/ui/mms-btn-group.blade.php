@props(['href', 'active', 'char'])

<a href="{{ $href }}" class="kana1_btn btn btn-outline-primary mb-2 @if ($active) active @endif" data-kana1="{{ $char }}">{{ $char }}</a>
