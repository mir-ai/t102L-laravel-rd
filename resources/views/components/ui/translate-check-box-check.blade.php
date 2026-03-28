@props(['lang_code', 'dbold'])

@php
  $flag_file_name = config("_lang.lang_info.{$lang_code}.nationalFlag");
  $lang_name = config("_lang.lang_info.{$lang_code}.langNameJp");
  $checked = (old("lang_codes.{$lang_code}", $dbold) == 'Y') ? 'checked' : '';
  $hilight = ($checked == 'checked') ? 'lang-hilight' : '';
@endphp

<div class="col-sm-4 g-2">
  <div class="lang_check {{$hilight}}" id="check_{{ $lang_code }}">
    <label class="" for="{{ $lang_code }}">
      <table>
        <td>
          <input type="checkbox" class="form-control-input send_radios select_languages m-1" id="{{ $lang_code }}"
            name="lang_codes[{{ $lang_code }}]" value="Y" {{$checked}} 
            data-parent-id="check_{{ $lang_code }}" />
        </td>
        <td>
          <img src="{{ asset("img/national_flags/{$flag_file_name}") }}" width="60" />
        </td>
        <td>
          {{ $lang_name }}
        </td>
      </table>
    </label>
  </div>
</div>
