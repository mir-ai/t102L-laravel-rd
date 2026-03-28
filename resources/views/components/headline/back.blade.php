@props(['href', 'label' => ''])

{{-- 詳細ページや編集ページで一覧画面に戻るためのリンク --}}
<a href="{{ $href }}" class="link-dark lg-s1"><i class="bi bi-caret-left text-secondary"></i>{{$label}}</a>
