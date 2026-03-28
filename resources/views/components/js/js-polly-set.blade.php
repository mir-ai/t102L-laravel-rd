<script type="module">
  let voice_id = 'Takumi';

  $(function() {

    // 単語が入力された後、カーソルのフォーカスが外れたら
    // ヨミを更新する。但し、ヨミが既に入っていたら、更新しない。
    $('#src').blur(function() {
      if (is_yomi_filled()) {
        return false;
      }

      yomi_update();
      return false;
    });

    // 単語入力欄の「ヨミ表示」が押されたら読みを更新する。
    $('#yomi_update').click(function() {
      yomi_update();
      return false;
    });

    // ヨミが更新されたら、ヨミ文字ボタンを更新する。
    $('#dst').keyup(function() {
      tone_update();
    });

    // ヨミ文字ボタンが押されたら
    $('body').on('click', '.tone_up', function() {
      let yomi = $(this).data('yomi');
      console.log("tone_up", yomi);
      $('#dst').val(yomi);

      yomi = hiraToKana(yomi);
      play_yomi(yomi);
      return false;
    });

    // 試聴ボタンが押されたら
    $('#read_dst').click(function() {
      let yomi = $('#dst').val();
      yomi = hiraToKana(yomi);
      play_yomi(yomi);

      return false;
    });

    // 単語とヨミを保存する
    $('#yomi_save').click(function() {
      const yomi_set_api_url = "{{ route('api.yomi_set') }}";
      let src_text = $('#src').val();
      let dst_text = $('#dst').val();

      call_yomi_set_api(yomi_set_api_url, src_text, dst_text);
    });

    // 音声合成用のテキストボックスの中の文字がドラッグで選択されたら、それをヨミの調整のヨミのテキストボックスに入れます
    $('.ssml_box').select(function() {
      var selected_text = document.getSelection().toString();
      if (selected_text.length < 16) {
        $('#src').val(selected_text);
      }

      return false;
    });

    // 「ヨミ調整」ボタンを押されたら、単語と読みを更新します。
    $('.yomi_launch').click(function() {
      let src_text = $('#src').val();
      if (src_text.length > 0) {
        $('#dst').val('');
        yomi_update();
      }

      // 現在選択されている声を採用。
      var voice_elem_name = $(this).data('voice_elem_name');

      if (voice_elem_name) {
        let sel = 'input:radio[name="' + voice_elem_name + '"]:checked';

        voice_id = $(sel).val();

        if (voice_id == null) {
          voice_id = $('#' + voice_elem_name).val();
        }
      }

      return true;
    });

    // run tone update after page load
    tone_update();
  });

  // 単語からヨミを取得して表示する
  function yomi_update() {
    let src = $('#src').val();

    if (src == '') {
      return false;
    }

    get_cur_yomi(src);

    return false;
  }

  function is_yomi_filled() {
    return ($('#dst').val() != '');
  }

  function get_cur_yomi(src_text) {
    if (src_text == '') {
      return;
    }

    // 単語に対する現在設定されているヨミを取得する
    const cur_yomi_api_url = "{{ route('api.yomi_get') }}";

    $.ajax({
      url: cur_yomi_api_url,
      type: "POST",
      data: {
        "src_text": src_text,
      },
      dataType: 'json'

    }).done(function(json) {

      console.log(`Success reading data ${cur_yomi_api_url}`, json);

      if (json['success'] == 'N') {
        alert(json['error_msg'])
      } else {
        //if (!is_yomi_filled()) {
        if (json.yomigana) {
          let yomigana = json.yomigana.replace(/[´’‘゜'“´”"❛?？＠@#＃■▲●+]/g, "＋");
          $('#dst').val(yomigana);
          tone_update();
        } else {
          // もし登録がなければ、ヨミをmecab辞書から取得して表示する。
          call_yomi_get_api(src_text);
        }
        //}
      }


    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log(`Failed reading read yomi ${cur_yomi_api_url}`, jqXHR.status, textStatus, errorThrown.message);

    }).always(function() {

    });
  }

  // 現在の登録されているヨミを取得する
  function call_yomi_get_api(src_text) {
    console.log(`call_yomi_get_api src_text='${src_text}'`);

    const mecab_api_url = "{{ config('_env.MECAB_API_URL') }}";
    $.ajax({

      url: mecab_api_url,
      type: "POST",
      data: {
        "src_text": src_text
      },
      dataType: 'json'

    }).done(function(json) {

      console.log(`Success reading mecab yomi ${mecab_api_url}`, json);

      //if (json.yomigana && (!is_yomi_filled())) {
      if (json.yomigana) {
        let yomigana = json.yomigana.replace(/[´’‘゜'“´”"❛?？＠@#＃■▲●+]/g, "＋");
        $('#dst').val(yomigana);
        tone_update();
      }

    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log(`Failed reading mecab yomi ${mecab_api_url}`, jqXHR.status, textStatus, errorThrown.message);

    }).always(function() {

    });
  }

  // 単語に対する読みを保存する
  function call_yomi_set_api(api_url, src_text, dst_text) {
    $.ajax({

      url: api_url,
      type: "POST",
      data: {
        "src_text": src_text,
        "dst_text": dst_text,
      },
      dataType: 'json'

    }).done(function(json) {

      if (json['success'] == 'N') {
        alert(json['error_msg']);
      } else {
        console.log(`Success set yomi ${api_url}`, json);
        $('#src').val('');
        $('#dst').val('');
        $('#tone').html('');
      }

    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log(`Failed reading read yomi ${api_url}`, jqXHR.status, textStatus, errorThrown.message);

    }).always(function() {

    });
  }

  // ヨミを１文字ずつボタンにする。
  function tone_update() {
    let read_text = $('#dst').val();

    let plain = read_text;
    plain = hiraToKana(plain);
    plain = plain.replace(/[´’‘゜'“´”"❛?？＠@#＃■▲●+＋]/g, "");

    // ヨミを1文字ずつ入れる
    let chars = plain.split('');

    let html = [];
    for (var i = 0; i < chars.length; i++) {
      // 自分のキャラを1文字取得
      let char = chars[i];

      // キャラ配列をコピー
      let chars_copy = chars.slice();

      // 自分のキャラの後ろに 強音記号 ' をつける
      chars_copy.splice(i + 1, 0, "＋");
      let tone = chars_copy.join('');

      // 
      let item = `<a class="tone_up btn btn-sm btn-outline-primary me-2 mb-2" data-yomi="${tone}" href="#">${char}</a>`;

      html.push(item)
    }

    if (html.length > 0) {
      let item = `<a class="tone_up btn btn-sm btn-outline-dark me-2 mb-2" data-yomi="${plain}" href="#">無調整</a>`;
      html.push(item)
    }

    let join_html = html.join('');
    $('#tone').html(join_html);
    return;
  }

  // ひらがなをカタカナにする
  function hiraToKana(str) {
    return str.replace(/[\u3041-\u3096]/g, function(match) {
      var chr = match.charCodeAt(0) + 0x60;
      return String.fromCharCode(chr);
    });
  }


  var AUDIO_FORMATS = {
    // 'ogg_vorbis': 'audio/ogg', // iOS support EOL
    'mp3': 'audio/mpeg',
    'pcm': 'audio/wave; codecs=1'
  };

  function getSupportedAudioFormats(player) {
    return Object.keys(AUDIO_FORMATS)
      .filter(function(format) {
        var supported = player.canPlayType(AUDIO_FORMATS[format]);
        return supported === 'probably' || supported === 'maybe';
      });
  }

  // ヨミを読み上げる。
  function play_yomi(yomi) {
    var player = document.getElementById('test_player');
    var read = document.getElementById('read');
    var supportedFormats = getSupportedAudioFormats(player)

    yomi = yomi.replace(/[´’‘゜“´”"❛?？＠@#＃■▲●+＋。、]/g, "'");

    let ssmlContent = `<speak><phoneme alphabet="x-amazon-pron-kana" ph="${yomi}">${yomi}</phoneme></speak>`;

    let polly_server_url = '{{ config('_env.POLLY_SERVER') }}';
    let my_voice_id = encodeURIComponent(voice_id);
    let ssml = encodeURIComponent(ssmlContent);
    let text_type = encodeURIComponent('ssml')
    let output_format = supportedFormats[0];

    player.src =
      `${polly_server_url}/read?voiceId=${my_voice_id}&text=${ssml}&textType=${text_type}&outputFormat=${output_format}`;
    player.play();

    return false;
  }
</script>
