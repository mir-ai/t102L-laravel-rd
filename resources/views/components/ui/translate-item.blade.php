@props(['lang_code', 'message_translate'])

@php
  $title_org_name = "title_org_{$lang_code}";
  $body_org_name = "body_org_{$lang_code}";
  $title_input_name = "title_target_{$lang_code}";
  $body_input_name = "body_target_{$lang_code}";
  $player_name = "player_{$lang_code}";
  $translator_name = "translator_{$lang_code}";

  $title_re_name = "title_re_ja_{$lang_code}";
  $body_re_name = "body_re_ja_{$lang_code}";
  $tmp_mp3_name = "tmp_mp3_{$lang_code}";
  $translator_type_name = "translator_type_{$lang_code}";

  $translator_type = $message_translate->translator_type ?? '';
@endphp

<h2 class="ml" id="lang_{{ $lang_code }}">
  {{ config("_const.speech_lang_codes.{$lang_code}") }}
</h2>

<input type="hidden" name="lang_codes[]" value="{{ $lang_code }}">

<div class="mb-3 row">
  <div class="col-sm-4 lg-1">
    {{-- 件名日本語原文 --}}
    <input id="{{ $title_org_name }}" name="{{ $title_org_name }}" type="text"
      value="{{ old($title_org_name, $message_translate->title_ja ?? '') }}"
      class="form-control form-control-lg translate_on_update2 @error($title_org_name) is-invalid @enderror"
      data-target="title_target_{{ $lang_code }}" data-re="title_re_{{ $lang_code }}"
      data-lang="{{ $lang_code }}" data-job_type="title_target" data-job_re="title_re" data-gen_audio="N">

  </div>
  <div class="col-sm-4">
    {{-- 件名翻訳 --}}
    <input id="{{ $title_input_name }}" name="{{ $title_input_name }}" type="text"
      value="{{ old($title_input_name, $message_translate->title_target ?? '') }}"
      class="form-control form-control-lg translate_on_update @error($title_input_name) is-invalid @enderror"
      data-target="title_re_{{ $lang_code }}" data-lang="{{ $lang_code }}" data-job_type="title_re"
      data-gen_audio="N">

    @error($title_input_name)
      <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
      </span>
    @else
    @enderror
  </div>
  <div class="col-sm-4">
    {{-- 件名日本語再翻訳 --}}
    <div id="title_re_{{ $lang_code }}" class="">
      {{ $message_translate->title_re_ja ?? '' }}<br />&nbsp;
    </div>
    <input type="hidden" id="{{ $title_re_name }}" name="{{ $title_re_name }}"
      value="{{ $message_translate->title_re_ja ?? '' }}">
  </div>
</div>

<div class="mb-3 row">
  <div class="col-sm-4 lg-1">
    {{-- 本文日本語原文 --}}
    <textarea id="{{ $body_org_name }}" name="{{ $body_org_name }}" rows="5"
      class="form-control form-control-lg translate_on_update2 @error($body_org_name) is-invalid @enderror"
      data-target="body_target_{{ $lang_code }}" data-re="body_re_{{ $lang_code }}" data-lang="{{ $lang_code }}"
      data-job_type="body_target" data-job_re="body_re" data-gen_audio="R">{{ old($body_org_name, $message_translate->body_ja ?? '') }}</textarea>

    <a class="btn btn-outline-primary form-control mt-2 void_button" href="#">更新</a>

  </div>
  <div class="col-sm-4">
    {{-- 本文翻訳 --}}
    <textarea id="{{ $body_input_name }}" name="{{ $body_input_name }}" rows="5"
      class="form-control form-control-lg translate_on_update @error($body_input_name) is-invalid @enderror"
      data-target="body_re_{{ $lang_code }}" data-lang="{{ $lang_code }}" data-job_type="body_re"
      data-gen_audio="R">{{ old($body_input_name, $message_translate->body_target ?? '') }}</textarea>

    @error($body_input_name)
      <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
      </span>
    @else
    @enderror

    <a class="btn btn-outline-primary form-control mt-2 void_button" href="#">更新</a>

  </div>
  <div class="col-sm-4">
    {{-- 本文日本語再翻訳 --}}
    <div id="body_re_{{ $lang_code }}" class="">
      {{ $message_translate->body_re_ja ?? '' }}<br />&nbsp;
    </div>
    <input type="hidden" id="{{ $body_re_name }}" name="{{ $body_re_name }}"
      value="{{ $message_translate->body_re_ja ?? '' }}">

  </div>
</div>

<div class="mb-3 row">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-8">
    {{-- audio here --}}
    {{-- INTL_VOICE_EXIST_CHECK --}}
    <table width="100%">
      <tr>
        <td width="70%">
          @if (config("_const.lang_code_speech2voice.{$lang_code}"))
            <div id="{{ $player_name }}" class="">
              <audio controls preload="none" src="{{ $message_translate->intl_mp3_url ?? '' }}">No Player</audio>
            </div>
          @endif

        </td>
        <td class="text-end" width="30%">
          <p class="text-muted" id="{{ $translator_name }}">
            {{ config("_const.translator_type.{$translator_type}", '') }}</p>
        </td>
      </tr>
    </table>

    <input type="hidden" id="{{ $tmp_mp3_name }}" name="{{ $tmp_mp3_name }}"
      value="{{ $message_translate->intl_mp3_url ?? '' }}">

    <input type="hidden" id="{{ $translator_type_name }}" name="{{ $translator_type_name }}"
      value="{{ $message_translate->translator_type ?? '' }}">
  </div>
</div>
