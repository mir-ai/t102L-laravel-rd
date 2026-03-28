{{--
  翻訳領域を手動で編集する

  翻訳領域は、一旦機械翻訳で作成するが、あとから手動で書き換えて、編集ロックを掛けることができるようにする。

  「編集」ボタンを押すと、モーダルウィンドウが起動

  モーダルウィンドウには、「日本語原文」「翻訳文」「日本語への逆翻訳文」のフォームがある。それぞれの欄には、現在の翻訳内容の値が（あれば）入っている。
  
  「日本語原文」欄を手動で書き換えると、「翻訳文」「日本語への逆翻訳文」が更新される。

  「翻訳文」欄を手動で書き換えると、「日本語への逆翻訳文」が更新される。

  保存すると、モーダルウィンドウが閉じて、もとの翻訳領域に反映されている。

  翻訳者は「手動」となる。機械翻訳は上書き禁止となる。
  試聴、編集ボタンは表示される。

--}}
<script type="module">

  let editing_lang_code;

  $(function() {

    // 機械翻訳結果表示部分で、「編集」ボタンが押された
    $('.edit_translate_open').click(function() {

      editing_lang_code = $(this).data('lang');

      console.log('edit_translate_open', 'editing_lang_code', editing_lang_code);

      // 機械翻訳された「日本語原文」「翻訳文」「日本語への逆翻訳文」の内容を、モーダルウィンドウ側のコントロールにコピーする
      get_values(editing_lang_code);

      $('#editTranslateModalCenter').modal('show')

      return false;
    });

    // 手動編集モーダル画面で、「保存」ボタンが押された
    $('#edit_translate_save').click(function() {
      // 手動変更されたモーダルウィンドウ側のコントロールの内容を、もとの「日本語原文」「翻訳文」「日本語への逆翻訳文」にコピーする
      editing_lang_code = $('#editing_lang_code').val();

      set_values(editing_lang_code);

      $('#editTranslateModalCenter').modal('hide')

      return false;
    });

  });

  // 機械翻訳された「日本語原文」「翻訳文」「日本語への逆翻訳文」の内容を、モーダルウィンドウ側のコントロールにコピーする
  function get_values(lang)
  {
    // 1. 翻訳元日本語原文コントロール(hidden)
    $('#text_ja_org_edit').val(
      $('#text_ja_org_' + lang).val()
    );

    // 2. 翻訳元日本語原文表示(disp)
    // "text_ja_org_disp_edit";

    // 3. 翻訳後テキストコントロール(hidden)
    $('#text_intl_edit').val(
      $('#text_intl_' + lang).val()
    );

    // 4. 翻訳後テキスト表示(disp)
    // "text_intl_disp_edit";

    // 5. 翻訳後翻訳エンジン種別コントロール(hidden)
    $('#translator_type_edit').val(
      $('#translator_type_' + lang).val()
    );

    // 6. 翻訳後翻訳エンジン種別表示(disp)
    // "translator_type_disp_edit";

    // 7. 日本語逆翻訳テキストコントロール(hidden)
    $('#text_ja_re_edit').val(
      $('#text_ja_re_' + lang).val()
    );

    // 8. 日本語逆翻訳テキスト表示(disp)
    $('#text_ja_re_disp_edit').text(
      $('#text_ja_re_' + lang).val()
    );

    // 9. 日本語逆翻訳エンジン種別コントロール(hidden)
    $('#translator_type_re_edit').val(
      $('#translator_type_re_' + lang).val()
    );

    // 10. 日本語逆翻訳エンジン種別表示(disp)
    // "translator_type_re_disp_edit";

    // 11. 上書きロックコントロール(hidden)
    $('#overwrite_locked_edit').val(
      $('#overwrite_locked_' + lang).val()
    );

    // 12. 上書きロック表示(disp)
    // "overwrite_locked_disp_edit";

    // 13. 音声再生ボタンのID
    // "intl_button_edit";

    // 14. 言語コード一時保存 
    $('#editing_lang_code').val(
      lang
    );
  }

  // 手動変更されたモーダルウィンドウ側のコントロールの内容を、もとの「日本語原文」「翻訳文」「日本語への逆翻訳文」にコピーする
  function set_values(lang)
  {
    // 1. 翻訳元日本語原文コントロール(hidden)
    $('#text_ja_org_' + lang).val(
      $('#text_ja_org_edit').val()
    );

    // 2. 翻訳元日本語原文表示(disp)
    $('#text_ja_org_disp_' + lang).text(
      $('#text_ja_org_edit').val()
    );

    // 3. 翻訳後テキストコントロール(hidden)
    $('#text_intl_' + lang).val(
      $('#text_intl_edit').val()
    );

    // 4. 翻訳後テキスト表示(disp)
    $('#text_intl_disp_' + lang).text(
      $('#text_intl_edit').val()
    );

    // 5. 翻訳後翻訳エンジン種別コントロール(hidden)
    $('#translator_type_' + lang).val(80); // 修正

    // 6. 翻訳後翻訳エンジン種別表示(disp)
      $('#translator_type_disp_' + lang).text('編集済');

    // 7. 日本語逆翻訳テキストコントロール(hidden)
    $('#text_ja_re_' + lang).val(
      $('#text_ja_re_edit').val()
    );

    // 8. 日本語逆翻訳テキスト表示(disp)
    $('#text_ja_re_disp_' + lang).text(
      $('#text_ja_re_edit').val()
    );
      
    // 9. 日本語逆翻訳エンジン種別コントロール(hidden)
    $('#translator_type_re_' + lang).val(80);

    // 10. 日本語逆翻訳エンジン種別表示(disp)
    $('#translator_type_re_disp_' + lang).text('編集済');

    // 11. 上書きロックコントロール(hidden)
    $('#overwrite_locked_' + lang).val('Y');

    // 12. 上書きロック表示(disp)
    $('#overwrite_locked_disp_' + lang).val('ロック');

    // 13. 音声再生ボタンのID
    $('.intl_button_edit' + lang).show();

    // 14. 言語コード一時保存 
    $('#editing_lang_code').val('');
  }

</script>
