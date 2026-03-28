{{--
  テキスト領域に対して、順次多言語翻訳を行うJS。

  ・「翻訳実行」ボタンを押すと、英語、中国語、韓国語に翻訳する。
  ・各言語ごとに、その言語用の「日本語原文」「翻訳した外国語」「翻訳した外国語を日本語に逆翻訳したもの」に翻訳する。
  ・

--}}
<script type="module">
  let job_queue = [];
  let ajax_translate_running = false;
  let abort_flg = false;

  $(function() {

    // 翻訳ジョブを定期的に実行します。
    setInterval(function() {
      job_run_next();
    }, 200);

    // 一括翻訳ボタンが押された。日本語読み上げ文を各国語に翻訳する。
    $('#exec_translate_button1').click(function() {
      return exec_translate_button1_clicked();
    });

    // モーダルウィンドウで日本語テキストを編集した
    $('#edit_text_ja_org').click(function() {
      lang = $('#editing_lang_code').val();
      edit_translate_save_busy();
      job_append(lang, 'edit');

    });
    
    // モーダルウィンドウで外国語テキストを編集した
    $('#edit_text_intl').click(function() {
      lang = $('#editing_lang_code').val();
      edit_translate_save_busy();
      job_append(lang, 'edit', false);

    });

    $('.submit_on_finish').click(function() {
      if (job_queue.length > 0) {
        alert("まだ翻訳中です。少し待ってから、もう一度お試し下さい。");
        console.log("job_queue", job_queue);
        return false;
      }

       return true;
    });

  });

  // 「⬇ 翻訳実行 ⬇」ボタンが押された。日本語読み上げ文を各国語に翻訳する。
  function exec_translate_button1_clicked()
  {
      // 編集ロックされていない言語を対象とする

      
      
      // 翻訳として選択された言語のみを表示する。
      lang_list = [];
      if ($('#lang_en').prop('checked')) {
        if ($('#overwrite_locked_en').val() != 'Y') {
          lang_list.push('en');
        }
      }

      if ($('#lang_ko').prop('checked')) {
        if ($('#overwrite_locked_ko').val() != 'Y') {
          lang_list.push('ko');
        }
      }

      if ($('#lang_zh').prop('checked')) {
        if ($('#overwrite_locked_zh').val() != 'Y') {
          lang_list.push('zh');
        }
      }

      // 「⬇ 翻訳実行 ⬇」ボタンの色を、処理状況に応じて変更する
      if (job_queue.length > 0) {
        // すでに翻訳処理を実行中の場合、停止してボタンの色を待機中に
        job_clear();
        exec_translate_button1_ok();
        return false;
      } else {
        // 未実行の場合、ボタンの色をビジーにする
        exec_translate_button1_busy();
      }

      // 日本語原文を各言語の日本語欄にコピーする
      for (var i = 0; i < lang_list.length; i++) {
        lang = lang_list[i];

        text_ja = $('#text_ja').val();
        overwrite_locked = $('#overwrite_locked_' + lang).val();

        if (overwrite_locked != 'Y') {
          $('#text_ja_org_' + lang).val(text_ja);
          $('#text_ja_org_disp_' + lang).text(text_ja);

          job_append(lang, lang);
        } else {
          // 上書き禁止の場合は上書きしない
          console.log(`Overwrite locked lang ${lang}.`, 'overwrite_locked', overwrite_locked);
        }
      }

      return false;
  }

  // 翻訳ジョブをキューに追加する。
  function job_append(lang, ext, exec_first = true) {
    // 1. 日本語→外国語翻訳ジョブをキューに追加する。
    let text_ja_org = '#text_ja_org_' + ext;
    let text_intl = '#text_intl_' + ext;
    let text_intl_disp = '#text_intl_disp_' + ext;
    let text_ja_re = '#text_ja_re_' + ext;
    let text_ja_re_disp = '#text_ja_re_disp_' + ext;
    let translator = '#translator_type_' + ext;
    let translator_disp = '#translator_type_disp_' + ext;
    let translator_re = '#translator_type_re_' + ext;
    let translator_re_disp = '#translator_type_re_disp_' + ext;
    let intl_button = '.intl_button_' + ext;
    
    if (exec_first) {
      push_queue_if_new({
        'source_lang': 'ja',
        'source_elem_id': text_ja_org,
        'target_lang': lang,
        'target_elem_ctrl_id': text_intl,
        'target_elem_disp_id': text_intl_disp,
        'translator_elem_ctrl_id': translator,
        'translator_elem_disp_id': translator_disp,
        'intl_button_class' : intl_button,
        'job_type': 'body',
      });

      $(text_intl).addClass('loading');
      $(text_intl_disp).addClass('loading');
    }

    // 2. 外国語翻訳→日本語逆翻訳をキューに追加する。

    push_queue_if_new({
      'source_lang': lang,
      'source_elem_id': text_intl,
      'target_lang': 'ja',
      'target_elem_ctrl_id': text_ja_re,
      'target_elem_disp_id': text_ja_re_disp,
      'translator_elem_ctrl_id': translator_re,
      'translator_elem_disp_id': translator_re_disp,
      'intl_button_class' : intl_button,
      'job_type': 'body_re',
    });

    $(text_ja_re).addClass('loading');
    $(text_ja_re_disp).addClass('loading');
  }

  function exec_translate_button1_busy() {
    $('#exec_translate_button1').removeClass('btn-primary');
    $('#exec_translate_button1').addClass('btn-outline-dark');
    $('#exec_translate_button1').text('翻訳を中止する');
  }

  // 翻訳準備完了です。ボタンを有効にします。
  function exec_translate_button1_ok() {
    $('#exec_translate_button1').removeClass('btn-outline-dark');
    $('#exec_translate_button1').addClass('btn-primary');
    $('#exec_translate_button1').text('⬇ 翻訳実行 ⬇');
  }

  function edit_translate_save_busy() {
    $('#edit_translate_save').addClass('disabled');
    $('#edit_translate_save').text('翻訳中...');
  }

  function edit_translate_save_ok() {
    $('#edit_translate_save').removeClass('disabled');
    $('#edit_translate_save').text('保存');
  }

  function job_clear() {
    job_queue = [];
  }

  // ジョブの内容要素を保存用に結合する
  function implode_queue_item(item) {
    ret = '';
    ret += item['source_lang'] + '|';
    ret += item['target_lang'] + '|';
    ret += item['job_type'] + '|';
    ret += item['source_elem_id'] + '|';
    ret += item['target_elem_ctrl_id'] + '|';
    ret += item['target_elem_disp_id'] + '|';
    ret += item['translator_elem_ctrl_id'] + '|';
    ret += item['translator_elem_disp_id'] + '|';
    ret += item['intl_button_class'];

    return ret;
  }

  // ジョブの内容要素を取り出し用に分解する
  function explode_queue_item(item_str) {
    if (!item_str) {
      return {}
    }

    items = item_str.split('|');

    return {
      'source_lang': items[0],
      'target_lang': items[1],
      'job_type': items[2],
      'source_elem_id': items[3],
      'target_elem_ctrl_id': items[4],
      'target_elem_disp_id': items[5],
      'translator_elem_ctrl_id': items[6],
      'translator_elem_disp_id': items[7],
      'intl_button_class' : items[8],
    };
  }

  // ジョブがまだ登録されていなければ、登録する。
  function push_queue_if_new(item) {

    item_str = implode_queue_item(item);
    if (job_queue.indexOf(item_str) >= 0) {
      // すでにある
      return false;
    }

    job_queue.push(item_str);
    return true;
  }

  // キューにある翻訳ジョブを定期的に実行します。
  function job_run_next() {

    // 既にajaxが走っていたら、次のインターバルでの実行を待ちます。
    if (ajax_translate_running) {
      return false;
    }

    // 残りのジョブがなかったら、ローディングマークは外します。
    if (job_queue.length == 0) {
      exec_translate_button1_ok();
      edit_translate_save_ok();
      $('.loading').removeClass('loading');
      return false;
    }

    // 次のキューを取得します。
    item_str = job_queue.shift();
    item = explode_queue_item(item_str);

    // 次の翻訳ジョブを実行します。
    if (item) {
      translate(item);
    }

    return true;
  }

  // 翻訳ジョブを実行します。
  function translate(params) {
    console.log("translate() params=", params);

    source_lang = params['source_lang'];
    target_lang = params['target_lang'];
    job_type = params['job_type'];
    source_elem_id = params['source_elem_id'];
    target_elem_ctrl_id = params['target_elem_ctrl_id'];
    target_elem_disp_id = params['target_elem_disp_id'];
    translator_elem_ctrl_id = params['translator_elem_ctrl_id'];
    translator_elem_disp_id = params['translator_elem_disp_id'];
    intl_button_class = params['intl_button_class'];

    lang = target_lang;
    if (lang == 'ja') {
      lang = source_lang;
    }

    const text = $(source_elem_id).val();

    if (!text) {
      return;
    }

    let post_data = {
      "text": text,
      "source_lang": source_lang,
      "target_lang": target_lang,
      "job_type": job_type,
    };

    console.log("[REQUEST] ", source_lang, target_lang, job_type, post_data);

    ajax_translate_running = true;

    $.ajax({
      url: '{{ route('api_translate') }}',
      type: "POST",
      data: post_data,
      dataType: 'json'

    }).done(function(json) {

      console.log("[RESPONSE] ", source_lang, target_lang, job_type, json);

      if (json.translated) {
        $(target_elem_ctrl_id).val(json.translated);
        $(target_elem_disp_id).text(json.translated);
        $(intl_button_class).removeClass('d-none');
      }

      if (json.translator_label) {
        console.log('TRANSLATOR ', translator_elem_ctrl_id, json.translator_label);

        $(translator_elem_disp_id).text(json.translator_label);
      }

      if (json.translator_type) {
        console.log('TRANSLATOR_TYPE ', translator_elem_disp_id, json.translator_type);

        $(translator_elem_ctrl_id).val(json.translator_type);
      }


    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.log("Failed translate", post_data, jqXHR.status, textStatus, errorThrown.message);

    }).always(function() {
      console.log("ajax_translate_running off");
      $(target_elem_ctrl_id).removeClass('loading');
      $(target_elem_disp_id).removeClass('loading');
      ajax_translate_running = false;
    });

    return;
  }

</script>
