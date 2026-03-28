@props(['enabled_languages_csv' => ''])

<div class="row mt-2 mb-3">
  <div class="col text-start">
    翻訳する言語を選択して下さい。
  </div>
  <div class="col text-end">
    <a href="#" id="all_languages_on">全言語を選択</a>&nbsp;｜&nbsp;<a href="#" id="all_languages_off">全解除</a>
  </div>

</div>

@php
  $lang_codes = array_keys(config('_lang.lang_info'));
@endphp

<div class="row">
  @foreach ($lang_codes as $lang_code)
    @if ($lang_code == 'ja')
      @continue
    @endif

    @php
      $enabled_languages = explode(',', $enabled_languages_csv);
      $dbold = (in_array($lang_code, $enabled_languages)) ? 'Y' : 'N';
    @endphp

    <x-ui.translate-check-box-check :$dbold :lang_code="$lang_code" />
  @endforeach
</div>
