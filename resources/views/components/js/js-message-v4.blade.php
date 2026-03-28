
@props(['record' => null])

<script type="module">
  const device_names_by_code = @json(MmsUtil::device_codes());
  const dept_names_by_id = @json(MmsCode::getKvs('depts.names_by_id'));
  const dept_tels_by_id = @json(MmsCode::getKvs('depts.tels_by_id'));
  const dept_tel_exts_by_id = @json(MmsCode::getKvs('depts.tel_exts_by_id'));

  $(function() {

    ///////////////////////////////////////////////////
    // カテゴリのチェックをコントロールする

    // 親カテゴリがチェックされたら、配下の小カテゴリをチェックする
    $('.check-groups').click(function() {
        let myctg = $(this).data('myctg');
        let checked = $(this).prop('checked');
        let checks = document.getElementsByClassName("category-of-" + myctg);
        let mark = 'checked';
        if (! checked) {
            mark = '';
        }

        for (let i = 0; i < checks.length; i++) {
            if (checks[i].type == 'checkbox') {
                checks[i].checked = mark;
            }
        }
        return true;
    });

    // 親要素がチェックされたら、子要素をチェックする（チェックが外れたら子要素のチェックも外す）
    $('.check-child').click(function() {
        let myid = $(this).data('myid');
        let checked = $(this).prop('checked');
        let checks = document.getElementsByClassName("child-of-" + myid);
        let mark = 'checked';
        if (! checked) {
            mark = '';
        }

        for (let i = 0; i < checks.length; i++) {
            if (checks[i].type == 'checkbox') {
                checks[i].checked = mark;
            }
        }
        return true;
    });

    // 子要素がチェックされたら、親要素をチェックする
    $('.check-parent').click(function() {
        let myid = $(this).data('myid');
        let parentId = $(this).data('parentid');
        let checked = $(this).prop('checked');

        if (! checked) {
            return true;
        }

        $('#group_' + parentId).prop('checked', true);
        return true;
    });

    // 親カテゴリ・子カテゴリ選択のチェックボックスを全部ONにします
    $('#all_categories_on').click(function(e) {
      $('.all_categories').prop('checked', true);
      return false;
    });

    // 親カテゴリ・子カテゴリ選択のチェックボックスを全部ONにします
    $('#all_categories_off').click(function(e) {
      $('.all_categories').prop('checked', false);
      return false;
    });    

    ///////////////////////////////////////////////////
    // 多言語翻訳

    // 多言語翻訳の言語選択画面を開閉する
    $('#translation_flg').click(function() {
      let checked = $(this).prop("checked");
      if (checked) {
        $('#tralsate-check-box').show('slow');
      } else {
        $('#tralsate-check-box').hide('slow');
      }
      return true;
    });

    ///////////////////////////////////////////////////
    // 部署名

    // 部署名の選択画面を開閉する
    $('#show_dept').click(function() {
      let checked = $(this).prop("checked");
      if (checked) {
        $('#dept-detail-div').show('slow');
      } else {
        $('#dept-detail-div').hide('slow');
      }
      return true;
    });

    // 部署IDが選択されたら、部署名と電話番号を反映する
    $('#dept_id').on('change', function() {
      dept_id = $('#dept_id option:selected').val();

      console.log('dept_name_full', dept_names_by_id[dept_id]);
      console.log('dept_tel', dept_tels_by_id[dept_id]);
      console.log('dept_tel_ext', dept_tel_exts_by_id[dept_id]);

      $('#dept_name_full').val(dept_names_by_id[dept_id]);
      $('#dept_tel').val(dept_tels_by_id[dept_id]);
      $('#dept_tel_ext').val(dept_tel_exts_by_id[dept_id]);

      return true;
    });
   
    ///////////////////////////////////////////////////
    // 子局指定

    // 子局指定の領域を開閉する
    $('#target_speaker_all').on('click', function(e) {
      let checked = $(this).prop("checked");
      if (!checked) {
        $('#speaker-target-div').show('slow');
        $('#speaker_target_mode').val('TAG');
        $('#speaker-target-tag').show();
        $('#speaker-target-each').hide();
      } else {
        $('#speaker-target-div').hide('slow');
        $('#speaker_target_mode').val('ALL');
        $('#speaker-target-tag').hide();
        $('#speaker-target-each').show();
      }

      return true;
    });

    // 子局グループ指定の領域を開閉する
    $('#nav-tag').on('click', function(e) {
      $('#speaker-target-tag').show();
      $('#speaker-target-each').hide();
      $('#speaker_target_mode').val('TAG');
      $('#nav-tag').addClass('active');
      $('#nav-each').removeClass('active');
      return false;
    });

    // 子局個別指定の領域を開閉する
    $('#nav-each').on('click', function(e) {
      $('#speaker-target-tag').hide();
      $('#speaker-target-each').show();
      $('#speaker_target_mode').val('EACH');
      $('#nav-each').addClass('active');
      $('#nav-tag').removeClass('active');
      return false;
    });

    // 子局タグをON/OFFされたら、紐づく子局もON/OFFする。
    $('.speaker-tag-cls').on('click', function(e) {
      let checked = $(this).prop("checked");
      let tag_id = $(this).data('tagig');

      if (checked) {
        $('.tagid-' + tag_id).prop("checked", true);
      } else {
        $('.tagid-' + tag_id).prop("checked", false);
      }
    });

    ///////////////////////////////////////////////////
    // 件名・本文

    // 本文欄の内容をコピー
    $('.copy_others').blur(function() {
      val = $(this).val();

      if ($('#message_disp').val() == '') {
        $('#message_disp').val(val);
      }

      @if (config('_env.TWITTER_OWN_TEXTAREA') == 'Y')
        if ($('#message_twitter').val() == '') {
          $('#message_twitter').val(val.substring(0, 140));
        }
      @endif

      if ($('#message_patlite').val() == '') {
        $('#message_patlite').val(val.substring(0, 50));
      }

      if ($('#message_cbs').val() == '') {
        $('#message_cbs').val(val.substring(0, 200));
      }

      if ($('#message_ssml').val() == '') {
        $('#message_ssml').val(val);
      }

      if ($('#message_ssml_bosai').val() == '') {
        $('#message_ssml_bosai').val(val);
      }

      update_len_count_all();
      return false;
    });

    // 件名欄の内容をコピー
    $('.copy_title').blur(function() {
      val = $(this).val();

      if ($('#message_title_cbs').val() == '') {
        $('#message_title_cbs').val(val.substring(0, 15));
      }
      return false;
    });

    // 画像アップロードされたらプレビューを表示
    $('#message_image').on('change', function(e) {
      var fileset = $(this).val();
      if (fileset === '') {
        $("#img_preview").attr('src', "");
      } else {
        console.log(fileset);
        var reader = new FileReader();
        console.log(reader);
        reader.onload = function(e) {
          console.log(e.target);
          $("#img_preview").attr('src', e.target.result);
        }
        reader.readAsDataURL(e.target.files[0]);
        $('#img_delete').show();
      }
    });

    // 画像削除をクリックされたら画像を削除
    $('#img_delete').on('click', function(e) {
      $("#message_image").val('');
      $("#img_preview").attr('src', "");
      $("#img_url").val('');
      $("#img_url_s").val('');
      $(this).hide();

      return false;
    });

    // 多言語 言語選択チェック

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_languages_on').click(function(e) {
      $('.select_languages').prop('checked', true);
      $('.lang_check').addClass('lang-hilight');

      return false;
    });

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_languages_off').click(function(e) {
      $('.select_languages').prop('checked', false);
      $('.lang_check').removeClass('lang-hilight');

      return false;
    });

    $('.select_languages').change(function() {
      const checked = $(this).prop("checked");
      const parent_id = $(this).data('parent-id');

      if (checked) {
        $('#' + parent_id).addClass('lang-hilight');
      } else {
        $('#' + parent_id).removeClass('lang-hilight');
      }

      return false;
    });

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_devices_on').click(function() {
      $('.select_devices').prop('checked', true)
      $('.table_check').addClass('table-info');

      show_only_necessary_inputs();

      return false;
    });

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_devices_off').click(function() {
      $('.select_devices').prop('checked', false)
      $('.table_check').removeClass('table-info');

      show_only_necessary_inputs();

      return false;
    });

    // メディア選択時にメディアをハイライトします
    $('.send_radios').change(function() {
      const checked = $(this).prop("checked");
      const parent_id = $(this).data('parent-id');

      if (checked) {
        $('#' + parent_id).addClass('table-info');
      } else {
        $('#' + parent_id).removeClass('table-info');
      }

      show_only_necessary_inputs();

      return false;
    });

    // 音源ファイルアップロード
    $('#message_audio').on('change', function (e) {
        var fileset = $(this).val();
        if (fileset === '') {
            $("#audio_preview").attr('src', "");
            $("#audio_preview").hide();
        } else {
            console.log(fileset);
            var reader = new FileReader();
            console.log(reader);
            reader.onload = function (e) {
                console.log(e.target);
                $("#audio_preview").attr('src', e.target.result);
                $("#audio_preview").show();
            }
            reader.readAsDataURL(e.target.files[0]);
            $('#audio_delete').show();
        }
    });
    
    // 音源ファイル削除時
    $('#audio_delete').on('click', function (e) {
        $("#message_audio").val('');
        $("#audio_preview").attr('src', "");
        $("#audio_preview").hide();
        $("#message_wav").val('');
        $(this).hide();

        return false;
    });    

    show_only_necessary_inputs();    
  });

  // 配信対象デバイスON/OFF用のJS

  function build_device_names_by_array(codes, if_empty) {
    if (!codes.length) {
      return if_empty;
    }

    let names = [];
    for (i = 0; i < codes.length; i++) {
      names.push(device_names_by_code[codes[i]]);
    }

    names_str = names.join('・');
    return names_str;
  }

  // 媒体ごとのチェック状態を取得し、該当する入力項目を表示・非表示とする。
  function show_only_necessary_inputs() {

    // 選択されている媒体を取得
    const send_alexa = $('#send_alexa').prop('checked') ? 'Y' : 'N';
    const send_amzn = $('#send_amzn').prop('checked') ? 'Y' : 'N';
    const send_bosai = $('#send_bosai').prop('checked') ? 'Y' : 'N';
    const send_cbs = $('#send_cbs').prop('checked') ? 'Y' : 'N';
    const send_custommail = $('#send_custommail').prop('checked') ? 'Y' : 'N';
    const send_email = $('#send_email').prop('checked') ? 'Y' : 'N';
    const send_facebook = $('#send_facebook').prop('checked') ? 'Y' : 'N';
    const send_fax = $('#send_fax').prop('checked') ? 'Y' : 'N';
    const send_hoam = $('#send_hoam').prop('checked') ? 'Y' : 'N';
    const send_linall = $('#send_linall').prop('checked') ? 'Y' : 'N';
    const send_line = $('#send_line').prop('checked') ? 'Y' : 'N';
    const send_linfam = $('#send_linfam').prop('checked') ? 'Y' : 'N';
    const send_posukuma = $('#send_posukuma').prop('checked') ? 'Y' : 'N';
    const send_radio = $('#send_radio').prop('checked') ? 'Y' : 'N';
    const send_rec = $('#send_rec').prop('checked') ? 'Y' : 'N';
    const send_signage = $('#send_signage').prop('checked') ? 'Y' : 'N';
    const send_siren = $('#send_siren').prop('checked') ? 'Y' : 'N';
    const send_sms = $('#send_sms').prop('checked') ? 'Y' : 'N';
    const send_speaker = $('#send_speaker').prop('checked') ? 'Y' : 'N';
    const send_tel = $('#send_tel').prop('checked') ? 'Y' : 'N';
    const send_twitter = $('#send_twitter').prop('checked') ? 'Y' : 'N';
    const send_web = $('#send_web').prop('checked') ? 'Y' : 'N';
    const send_yahoo = $('#send_yahoo').prop('checked') ? 'Y' : 'N';

    // 要素ごとの表示／非表示

    // 本文（表示用）
    let show_message_disp = false;

    // 本文（音声合成用）
    let show_message_ssml = false;

    // 防災行政無線・IP子局（音声合成用）
    let show_message_ssml_bosai = false;

    // エリアメール・緊急速報メール件名・本文
    let show_message_cbs = false;

    // 放送対象IP子局
    let show_select_speaker = false;

    // 配信先カテゴリ
    let show_group = false;

    // 外部リンク
    let show_link = false;

    // 部署名
    let show_dept = false;

    // 配信文の優先度
    let show_alert_level = false;

    // 多言語翻訳
    let show_translation = false;

    // 音声入力
    let show_audio = false;

    // 画像
    let show_photo = false;

    let show_image_delete = ('{{ request()->session()->get('old_img_url') ?? ($record->img_url ?? '') }}' != '') ? true : false;

    // テキストボックスごとの配信対象

    // 件名のメディア
    let devices_of_title = [];

    // 本文（表示用）のメディア
    let devices_of_message_disp = [];

    // 本文（音声合成用）のメディア
    let devices_of_message_ssml = [];

    // 防災行政無線・IP子局（音声合成用）のメディア
    let devices_of_message_ssml_bosai = [];

    // 音源ファイルアップロードのメディア
    let devices_of_audio = [];

    // グループのメディア
    let devices_of_group = [];

    // 画像アップロードのメディア
    let devices_of_photo = [];

    // 外部リンクのメディア
    let devices_of_homepage = [];

    if (send_bosai == 'Y') {
      show_message_ssml_bosai = true;
      show_alert_level = true;
      show_audio = true;
      show_translation = true;
      devices_of_message_ssml_bosai.push('bosai');
      devices_of_audio.push('bosai');
    }

    if (send_speaker == 'Y') {
      show_message_ssml_bosai = true;
      show_select_speaker = true;
      show_alert_level = true;
      show_translation = true;
      show_audio = true;
      devices_of_message_ssml_bosai.push('speaker');
      devices_of_audio.push('speaker');
    }

    if (send_radio == 'Y') {
      show_message_ssml = true;
      show_group = true;
      show_alert_level = true;
      show_audio = true;
      devices_of_message_ssml.push('radio');
      devices_of_audio.push('radio');
      devices_of_group.push('radio');
    }

    if (send_cbs == 'Y') {
      show_message_cbs = true;
      show_group = true;
      devices_of_group.push('cbs');
    }

    if (send_email == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_link = true;
      show_dept = true;
      show_translation = true;
      devices_of_title.push('email');
      devices_of_message_disp.push('email');
      devices_of_group.push('email');
      devices_of_homepage.push('email');
    }

    if (send_custommail == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_link = true;
      show_dept = true;
      devices_of_title.push('custommail');
      devices_of_message_disp.push('custommail');
      devices_of_homepage.push('custommail');
    }

    if (send_yahoo == 'Y') {
      show_message_disp = true;
      devices_of_title.push('yahoo');
      devices_of_message_disp.push('yahoo');
    }

    if (send_linall == 'Y') {
      show_message_disp = true;
      show_link = true;
      show_dept = true;
      show_photo = true;
      devices_of_title.push('linall');
      devices_of_message_disp.push('linall');
      devices_of_photo.push('linall');
      devices_of_homepage.push('linall');
    }

    if (send_line == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_alert_level = true;
      show_photo = true;
      devices_of_title.push('line');
      devices_of_message_disp.push('line');
      devices_of_photo.push('line');
      devices_of_group.push('email');
    }

    if (send_twitter == 'Y') {
      show_message_disp = true;
      show_link = true;
      show_photo = true;
      devices_of_title.push('twitter');
      devices_of_message_disp.push('twitter');
      devices_of_photo.push('twitter');
    }

    if (send_facebook == 'Y') {
      show_message_disp = true;
      show_link = true;
      show_dept = true;
      show_photo = true;
      devices_of_title.push('facebook');
      devices_of_message_disp.push('facebook');
      devices_of_photo.push('facebook');
      devices_of_homepage.push('facebook');
    }

    if (send_hoam == 'Y') {
      show_message_disp = true;
      show_message_ssml = true;
      show_link = true;
      show_group = true;
      show_dept = true;
      show_alert_level = true;
      show_photo = true;
      show_audio = true;
      devices_of_title.push('hoam');
      devices_of_message_disp.push('hoam');
      devices_of_message_ssml.push('hoam');
      devices_of_audio.push('hoam');
      devices_of_photo.push('hoam');
      devices_of_group.push('hoam');
      devices_of_homepage.push('hoam');
    }

    if (send_amzn == 'Y') {
      show_message_disp = true;
      show_message_ssml = true;
      show_group = true;
      show_dept = true;
      show_alert_level = true;
      show_audio = true;
      devices_of_title.push('hoam');
      devices_of_message_disp.push('hoam');
      devices_of_message_ssml.push('hoam');
      devices_of_audio.push('hoam');
      devices_of_group.push('hoam');
    }
    
    if (send_web == 'Y') {
      show_message_disp = true;
      devices_of_message_disp.push('web');
    }

    if (send_rec == 'Y') {
      show_message_ssml = true;
      devices_of_message_ssml.push('rec');
    }

    if (send_tel == 'Y') {
      show_message_ssml = true;
      show_group = true;
      devices_of_message_ssml.push('tel');
      devices_of_audio.push('tel');
      devices_of_group.push('tel');
    }

    if (send_sms == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_dept = true;
      devices_of_title.push('sms');
      devices_of_message_disp.push('sms');
      devices_of_group.push('sms');
    }

    if (send_fax == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_link = true;
      show_dept = true;
      devices_of_title.push('fax');
      devices_of_message_disp.push('fax');
      devices_of_group.push('fax');
      devices_of_homepage.push('fax');
    }

    if (send_signage == 'Y') {
      show_message_disp = true;
      show_dept = true;
      show_alert_level = true;
      show_translation = true;
      show_photo = true;
      devices_of_title.push('signage');
      devices_of_message_disp.push('signage');
    }

    if (send_alexa == 'Y') {
      show_message_ssml = true;
      show_alert_level = true;
      show_audio = true;
      devices_of_message_ssml.push('alexa');
      devices_of_audio.push('alexa');
    }

    if (send_posukuma == 'Y') {
      show_message_ssml = true;
      show_group = true;
      show_alert_level = true;
      show_audio = true;
      devices_of_message_disp.push('posukuma');
      devices_of_message_ssml.push('posukuma');
      devices_of_audio.push('posukuma');
      devices_of_photo.push('posukuma');
      devices_of_group.push('posukuma');
    }

    // テキストエリアごとの媒体名を設定
    $('.device_names_of_title').text(
      build_device_names_by_array(devices_of_title, '配信文')
    );

    $('.device_names_of_message_disp').text(
      build_device_names_by_array(devices_of_message_disp)
    );

    $('.devices_names_of_message_ssml').text(
      build_device_names_by_array(devices_of_message_ssml)
    );

    $('.devices_names_of_message_ssml_bosai').text(
      build_device_names_by_array(devices_of_message_ssml_bosai)
    );

    $('.devices_names_of_audio').text(
      build_device_names_by_array(devices_of_audio)
    );

    $('.devices_names_of_photo').text(
      build_device_names_by_array(devices_of_photo)
    );

    $('.devices_names_of_group').text(
      build_device_names_by_array(devices_of_group)
    );
    
    $('.devices_names_of_homepage').text(
      build_device_names_by_array(devices_of_homepage)
    );
    
    // 各パーツの表示・非表示を変更

    // 本文（表示用）
    if (show_message_disp) {
      $('#div_message_disp').show('slow');
    } else {
      $('#div_message_disp').hide('slow');
    }

    // 本文（音声合成用）
    if (show_message_ssml) {
      $('#div_message_ssml').show('slow');
    } else {
      $('#div_message_ssml').hide('slow');
    }

    // 防災行政無線・IP子局（音声合成用）
    if (show_message_ssml_bosai) {
      $('#div_message_ssml_bosai').show('slow');
    } else {
      $('#div_message_ssml_bosai').hide('slow');
    }

    // エリアメール・緊急速報メール件名・本文
    if (show_message_cbs) {
      $('#div_message_cbs').show('slow');
    } else {
      $('#div_message_cbs').hide('slow');
    }

    if (show_select_speaker) {
      $('#div_speaker_targets').show('slow');
    } else {
      $('#div_speaker_targets').hide('slow');
    }

    if (show_group) {
      $('#div_group').show('slow');
    } else {
      $('#div_group').hide('slow');
    }

    if (show_photo) {
      $('#div_photo').show('slow');
    } else {
      $('#div_photo').hide('slow');
    }

    if (show_link) {
      $('#div_link').show('slow');
    } else {
      $('#div_link').hide('slow');
    }

    if (show_dept) {
      $('#div_dept').show('slow');
    } else {
      $('#div_dept').hide('slow');
    }

    if (show_alert_level) {
      $('#div_alert_level').show('slow');
    } else {
      $('#div_alert_level').hide('slow');
    }

    if (show_translation) {
      $('#div_translation').show('slow');
    } else {
      $('#div_translation').hide('slow');
    }

    if (show_audio) {
      $('#div_audio').show('slow');
    } else {
      $('#div_audio').hide('slow');
    }

    if (show_image_delete) {
      $('#img_delete').show('slow');
    } else {
      $('#img_delete').hide('slow');
    }
  }

</script>
