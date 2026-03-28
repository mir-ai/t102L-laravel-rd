@props(['trans_source_jp', 'trans'])

@php
  $enable_lang_codes = MmsLang::enabled_all_lang_codes();
@endphp

@foreach ($enable_lang_codes as $lang_code)
  @if ($loop->first)
    <div class="mb-3 row lg-1 mt-5">
      <div class="col-sm-3">
        翻訳用日本語
      </div>
      <div class="col-sm-6">
        <input id="trans_source_jp" name="trans_source_jp" type="text"
          value="{{ old('trans_source_jp', $trans_source_jp ?? '') }}" class="form-control form-control-lg">
        <p class="my-3">
          <a href="#" id="translate_all2" class="btn btn-primary btn-lg form-control translate_lang_box2"
            data-langs="{{ implode(',', $enable_lang_codes) }}">⬇ 一括翻訳 ⬇</a>
        </p>
      </div>
      <div class="col-sm-3">
      </div>
    </div>
  @endif

  @php
    $title_input_name = "title_target_{$lang_code}";
    $player_name = "player_{$lang_code}";
    $translator_name = "translator_{$lang_code}";

    $title_re_name = "title_re_ja_{$lang_code}";
    $tmp_mp3_name = "tmp_mp3_{$lang_code}";
    $translator_type_name = "translator_type_{$lang_code}";
  @endphp

  <div class="mb-3 row lg-1">
    <div class="col-sm-3">
      {{ config("_const.speech_lang_codes.{$lang_code}") }}
      <a href="#" class="translate_lang_box2" data-langs="{{ $lang_code }}"><i class="bi bi-arrow-clockwise"></i></a>
    </div>
    <div class="col-sm-6">
      <input id="{{ $title_input_name }}" name="{{ $title_input_name }}" type="text"
        value="{{ old($title_input_name, $trans[$title_input_name] ?? '') }}"
        class="form-control form-control-lg translate_on_update @error($title_input_name) is-invalid @enderror"
        data-target="title_re_{{ $lang_code }}" data-lang="{{ $lang_code }}" data-job_type="title_re"
        data-gen_audio="N">

      @error('group_name')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div>
    <div class="col-sm-3">
      <div id="title_re_{{ $lang_code }}" class="text-muted m-1">
        {{ $trans[$title_re_name] ?? '' }}
      </div>
    </div>
  </div>
@endforeach
