@props(['a_message'])

<script type="module">
  let show_dept3 = ('{{ old('show_dept', $a_message->show_dept) }}' == 'Y') ? true : false;
  let show_img_delete = ('{{ $a_message->img_url }}' != '') ? true : false;
  @if (config('_env.ENABLE_TRANSLATE', 'N') == 'Y')
    let show_translation2 = ('{{ old('translation_flg', $a_message->translation_flg) }}' == 'Y') ? true : false;
  @else
    let show_translation2 = false;
  @endif

  function update_textareas() {
    const send_tel = $('#send_tel').prop('checked') ? 'Y' : 'N';
    const send_sms = $('#send_sms').prop('checked') ? 'Y' : 'N';
    const send_rec = $('#send_rec').prop('checked') ? 'Y' : 'N';
    const send_fax = $('#send_fax').prop('checked') ? 'Y' : 'N';
    const send_email = $('#send_email').prop('checked') ? 'Y' : 'N';
    const send_custommail = $('#send_custommail').prop('checked') ? 'Y' : 'N';
    const send_speaker = $('#send_speaker').prop('checked') ? 'Y' : 'N';
    const send_hoam = $('#send_hoam').prop('checked') ? 'Y' : 'N';
    const send_line = $('#send_line').prop('checked') ? 'Y' : 'N';
    const send_alexa = $('#send_alexa').prop('checked') ? 'Y' : 'N';
    const send_amzn = $('#send_amzn').prop('checked') ? 'Y' : 'N';
    const send_posukuma = $('#send_posukuma').prop('checked') ? 'Y' : 'N';
    const send_radio = $('#send_radio').prop('checked') ? 'Y' : 'N';
    const send_web = $('#send_web').prop('checked') ? 'Y' : 'N';
    const send_bosai = $('#send_bosai').prop('checked') ? 'Y' : 'N';
    const send_twitter = $('#send_twitter').prop('checked') ? 'Y' : 'N';
    const send_patlite = $('#send_patlite').prop('checked') ? 'Y' : 'N';
    const send_cbs = $('#send_cbs').prop('checked') ? 'Y' : 'N';
    const send_linall = $('#send_linall').prop('checked') ? 'Y' : 'N';
    const send_linfam = $('#send_linfam').prop('checked') ? 'Y' : 'N';
    const send_yahoo = $('#send_yahoo').prop('checked') ? 'Y' : 'N';
    const send_facebook = $('#send_facebook').prop('checked') ? 'Y' : 'N';
    const send_lalert = $('#send_lalert').prop('checked') ? 'Y' : 'N';
    const send_siren = $('#send_siren').prop('checked') ? 'Y' : 'N';
    const send_signage = $('#send_signage').prop('checked') ? 'Y' : 'N';

    let show_message_disp = false;
    let show_message_alt_disp = false;
    let show_message_twitter = false;
    let show_message_patlite = false;
    let show_message_cbs = false;
    let show_message_ssml = false;
    let show_message_ssml_bosai = false;

    let show_group = false;
    let show_photo = false;
    let show_map = false;
    let show_homepage = false;
    let show_dept = false;
    let show_close_date = false;
    let show_answer = false;
    let show_translation = false;
    let show_tel_ask = false;
    let show_audio = false;

    if (send_tel == 'Y') {
      show_message_ssml = true;
      show_group = true;
      show_answer = true;
      show_tel_ask = true;
      show_audio = true;
      show_translation = true;
    }

    if (send_sms == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_answer = true;
      show_translation = true;
    }

    if (send_rec == 'Y') {
      show_message_ssml = true;
      show_close_date = true;
      show_audio = true;
    }

    if (send_fax == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
      show_dept = true;
      show_translation = true;
    }

    if (send_email == 'Y') {
      @if (config('_env.TEXT_COLUMN_EMAIL', '') != 'message_alt_disp')
        show_message_disp = true;
      @else
        show_message_alt_disp = true;
      @endif
      show_group = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
      show_dept = true;
      show_translation = true;
    }

    if (send_custommail == 'Y') {
      @if (config('_env.TEXT_COLUMN_EMAIL', '') != 'message_alt_disp')
        show_message_disp = true;
      @else
        show_message_alt_disp = true;
      @endif
      show_group = true;
      show_dept = true;
    }

    if (send_line == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
    }

    if (send_alexa == 'Y') {
      show_message_disp = true;
      show_message_ssml = true;
      show_close_date = true;
      show_audio = true;
    }

    if (send_amzn == 'Y') {
      show_message_disp = true;
      show_message_ssml = true;
      show_close_date = true;
      show_audio = true;
    }

    if (send_posukuma == 'Y') {
      show_message_disp = true;
      show_audio = true;
    }

    if (send_radio == 'Y') {
      show_group = true;
      show_message_ssml = true;
      show_close_date = true;
    }

    if (send_web == 'Y') {
      show_message_disp = true;
      show_close_date = true;
      show_translation = true;
    }

    if (send_bosai == 'Y') {
      show_message_ssml_bosai = true;
      show_translation = true;
      show_audio = true;
    }

    if (send_speaker == 'Y') {
      show_message_ssml_bosai = true;
      show_translation = true;
      show_audio = true;
    }

    if (send_twitter == 'Y') {
      @if (config('_env.TWITTER_OWN_TEXTAREA') == 'Y')
        show_message_twitter = true;
      @else
        show_message_disp = true;
      @endif
      show_photo = true;
      show_homepage = true;
    }

    if (send_patlite == 'Y') {
      show_message_patlite = true;
      // show_photo = true;
      // show_map = true;
    }

    if (send_cbs == 'Y') {
      show_message_cbs = true;
      show_group = true;
    }

    if (send_linall == 'Y') {
      show_message_disp = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
    }

    if (send_linfam == 'Y') {
      show_message_disp = true;
      show_group = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
    }

    if (send_yahoo == 'Y') {
      show_message_disp = true;
    }

    if (send_facebook == 'Y') {
      show_message_disp = true;
      show_photo = true;
      show_map = true;
      show_homepage = true;
    }

    if (send_hoam == 'Y') {
      show_message_disp = true;
      show_message_ssml = true;
      show_photo = true;
      show_homepage = true;
      show_dept = true;
      show_audio = true;
    }

    if (send_lalert == 'Y') {
      show_message_disp = true;
      show_dept = true;
    }

    if (send_signage == 'Y') {
      show_message_disp = true;
      show_photo = true;
      show_translation = true;
    }

    // テキスト入力欄の表示・非表示を設定
    if (show_message_disp) {
      $('#div_message_disp').show('slow');
    } else {
      $('#div_message_disp').hide('slow');
    }

    if (show_message_alt_disp) {
      $('#div_message_alt_disp').show('slow');
    } else {
      $('#div_message_alt_disp').hide('slow');
    }

    @if (config('_env.TWITTER_OWN_TEXTAREA') == 'Y')
      if (show_message_twitter) {
        $('#div_message_twitter').show('slow');
      } else {
        $('#div_message_twitter').hide('slow');
      }
    @endif

    if (show_message_patlite) {
      $('#div_message_patlite').show('slow');
    } else {
      $('#div_message_patlite').hide('slow');
    }

    if (show_message_cbs) {
      $('#div_message_cbs').show('slow');
    } else {
      $('#div_message_cbs').hide('slow');
    }

    if (show_message_ssml_bosai) {
      $('#div_message_ssml_bosai').show('slow');
    } else {
      $('#div_message_ssml_bosai').hide('slow');
    }

    if (show_message_ssml) {
      $('#div_message_ssml').show('slow');
    } else {
      $('#div_message_ssml').hide('slow');
    }

    // 設定項目の表示・非表示を設定
    if (show_group) {
      $('#div_group').show('slow');
    } else {
      $('#div_group').hide('slow');
    }

    if (show_audio) {
      $('#div_audio').show('slow');
    } else {
      $('#div_audio').hide('slow');
    }

    if (show_photo) {
      $('#div_photo').show('slow');
    } else {
      $('#div_photo').hide('slow');
    }

    if (show_map) {
      $('#div_map').show('slow');
    } else {
      $('#div_map').hide('slow');
    }

    if (show_homepage) {
      $('#div_homepage').show('slow');
    } else {
      $('#div_homepage').hide('slow');
    }

    if (show_dept) {
      $('#div_dept').show('slow');
    } else {
      $('#div_dept').hide('slow');
    }

    if (show_img_delete) {
      $('#img_delete').show('slow');
    } else {
      $('#img_delete').hide('slow');
    }

    if (show_close_date) {
      $('#div_close_date').show('slow');
    } else {
      $('#div_close_date').hide('slow');
    }

    if (show_answer) {
      $('#div_answer').show('slow');
    } else {
      $('#div_answer').hide('slow');
    }

    if (show_translation) {
      $('#div_translation').show('slow');
      $('#translation_flg').val('Y');

    } else {
      $('#div_translation').hide('slow');
      $('#translation_flg').val('N');
    }

    if (show_tel_ask) {
      $('#div_tel_ask').show('slow');
    } else {
      $('#div_tel_ask').hide('slow');
    }

    update_len_count_all();
  }

  function update_len_count_all() {
    @if (MmsUtil::device_enabled('message_disp'))
      update_len_count($('#message_disp'))
    @endif

    @if (MmsUtil::device_enabled('message_twitter'))
      @if (config('_env.TWITTER_OWN_TEXTAREA') == 'Y')
        update_len_count($('#message_twitter'))
      @endif
    @endif

    @if (MmsUtil::device_enabled('message_patlite'))
      update_len_count($('#message_patlite'))
    @endif

    @if (MmsUtil::device_enabled('message_cbs'))
      update_len_count($('#message_cbs'))
    @endif

    @if (MmsUtil::device_enabled('message_ssml'))
      update_len_count($('#message_ssml'))
    @endif

    @if (MmsUtil::device_enabled('message_ssml_bosai'))
      update_len_count($('#message_ssml_bosai'))
    @endif
  }

  function update_len_count(elem) {

    let maxlen = elem.data('maxlen');
    let len_elem_id = elem.data('len_elem_id');
    let text = elem.val();
    text = text.replace(/\r\n/g, "|");
    text = text.replace(/\r/g, "|");
    text = text.replace(/\n/g, "|");
    text = text.replace(/\|/g, "||");
    let len = text.length;
    // console.log(`len_count ${len}`)

    $(`#${len_elem_id}`).text(len);

    if (maxlen <= len) {
      $(`#${len_elem_id}`).css('color', 'red');
    } else {
      $(`#${len_elem_id}`).css('color', 'gray');
    }

    return true;
  }

  $(document).ready(function() {
    update_textareas();
    update_len_count_all();
  });

  $(function() {

    $('#accordionDept').on('hidden.bs.collapse', function() {
      $('#show_dept').val('N');
      console.log("show dept off");
    });

    $('#accordionDept').on('shown.bs.collapse', function() {
      $('#show_dept').val('Y');
      console.log("show dept on");
    });

    $('#accordionMap').on('hidden.bs.collapse', function() {
      $('#attach_map').val('N');
      console.log("attach map off");
    });

    $('#accordionMap').on('shown.bs.collapse', function() {
      $('#attach_map').val('Y');
      console.log("attach map on");
    });

    $('#accordionTrans').on('hidden.bs.collapse', function() {
      $('#translation_flg').val('N');
      console.log("translation_flg off");
    });

    $('#accordionTrans').on('shown.bs.collapse', function() {
      $('#translation_flg').val('Y');
      console.log("translation_flg on");
    });

    $('.send_radios').change(function() {
      const checked = $(this).prop("checked");
      const parent_id = $(this).data('parent-id');

      if (checked) {
        $('#' + parent_id).addClass('table-info');
      } else {
        $('#' + parent_id).removeClass('table-info');
      }
      update_textareas();

      return false;
    });

    $('.alert_level').change(function() {


    });

    // 削除ボタン押下時のコンファーム
    $('#exec_destroy').click(function() {

      ret = confirm("配信文を削除してよろしいですか？");
      if (ret == false) {
        return false;
      }

      return true;
    });

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

    $('.copy_title').blur(function() {
      val = $(this).val();

      if ($('#message_title_cbs').val() == '') {
        $('#message_title_cbs').val(val.substring(0, 15));
      }
      return false;
    });

    $('.save_selected').blur(function() {

      disp = $('#message_disp').val();
      ssml = $('#message_ssml').val();
      ssml_bosai = $('#message_ssml_bosai').val();

      return true;
    });

    $('#recipients_all_on').click(function() {

      let check = document.getElementsByClassName('recipients_check');
      for (let i = 0; i < check.length; i++) {
        if (check[i].type == 'checkbox') {
          check[i].checked = 'checked';
        }
      }
      return false;
    });

    // 削除ボタン押下時のコンファーム
    $('#recipients_all_off').click(function() {

      let check = document.getElementsByClassName('recipients_check');
      for (let i = 0; i < check.length; i++) {
        if (check[i].type == 'checkbox') {
          check[i].checked = '';
        }
      }
      return false;
    });

    // グループ選択チェックボックスON
    $('#groups_all_on').click(function() {

      let check = document.getElementsByClassName('groups_check');
      for (let i = 0; i < check.length; i++) {
        if (check[i].type == 'checkbox') {
          check[i].checked = 'checked';
        }
      }
      return false;
    });

    // グループ選択チェックボックスOFF
    $('#groups_all_off').click(function() {

        let check = document.getElementsByClassName('groups_check');
        for (let i = 0; i < check.length; i++) {
        if (check[i].type == 'checkbox') {
            check[i].checked = '';
        }
        }
        return false;
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

    // 文字数カウントとオーバー警告
    $('.len_count').keyup(function() {
      update_len_count($(this));

      return true;
    });

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_devices_on').click(function(e) {
      $('.select_devices').prop('checked', true);
      $('.table_check').addClass('table-info');
      update_textareas();
      update_len_count_all();

      return false;
    });

    // デバイス選択のチェックボックスを全部ONにします
    $('#all_devices_off').click(function(e) {
      $('.select_devices').prop('checked', false);
      $('.table_check').removeClass('table-info');
      update_textareas();
      update_len_count_all();

      return false;
    });

    // 言語選択のチェックボックスを全部ONにします
    $('#all_langs_on').click(function() {
      $('.select_langs').prop('checked', true)
      return false;
    });

    // 言語選択のチェックボックスを全部ONにします
    $('#all_langs_off').click(function() {
      $('.select_langs').prop('checked', false)
      return false;
    });

    $('#message_image').change(function() {
      var file = $(this).prop('files')[0];
      filename = file.name;
      filename = filename.replace('.png', '');
      filename = filename.replace('.jpg', '');
      filename = filename.replace('.jpeg', '');
      $('#img_desc').val(filename);
    });

    $('#next').click(function() {
      let check = '';

      // 入力欄が表示されている要素に対応するテキストボックスの内容を取得する。
      if ($("#div_message_disp").is(":visible")) {
        check = check + ($('#message_disp').val() ?? '');
      }

      if ($("#div_message_alt_disp").is(":visible")) {
        check = check + ($('#message_alt_disp').val() ?? '');
      }

      if ($("#div_message_twitter").is(":visible")) {
        check = check + ($('#message_twitter').val() ?? '');
      }

      if ($("#div_message_patlite").is(":visible")) {
        check = check + ($('#message_patlite').val() ?? '');
      }

      if ($("#div_message_cbs").is(":visible")) {
        check = check + ($('#message_cbs').val() ?? '');
      }

      if ($("#div_message_ssml_bosai").is(":visible")) {
        check = check + ($('#message_ssml_bosai').val() ?? '');
      }

      if ($("#div_message_ssml").is(":visible")) {
        check = check + ($('#message_ssml').val() ?? '');
      }

      if (check.indexOf('●') != -1) {
        alert('本文中に●が残っています。');
        return false;
      }
      if (check.indexOf('△') != -1) {
        alert('本文中に△が残っています。');
        return false;
      }

      let title = ($('#message_title').val() ?? '');
      if (title.indexOf('●') != -1) {
        alert('件名に●が残っています。');
        return false;
      }

      return true;
    });

  });
</script>
