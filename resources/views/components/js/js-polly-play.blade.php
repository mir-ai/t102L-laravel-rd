<script type="module">
  var AUDIO_FORMATS = {
    // 'ogg_vorbis': 'audio/ogg', // iOS support EOL
    'mp3': 'audio/mpeg',
    'pcm': 'audio/wave; codecs=1'
  };

  var supportedFormats;

  let playing_id;

  // 最後に再生したプレイヤーのオブジェクト
  let last_played_player_obj = null;

  function getSupportedAudioFormats(player) {
    return Object.keys(AUDIO_FORMATS)
      .filter(function(format) {
        var supported = player.canPlayType(AUDIO_FORMATS[format]);
        return supported === 'probably' || supported === 'maybe';
      });
  }

  function used_replaces_text(used_replaces) {
    let text = [];
    for (let key in used_replaces) {
      tmp = key + '（' + used_replaces[key] + '）';
      tmp = tmp.replace(/'/g, "＋");

      console.log(tmp);
      text.push(tmp);
    }
    if (text.length == 0) {
      return '';
    } else {
      return '' + text.join('、');
    }
  }

  $(function() {

    // テキストを読み上げる。多言語対応。声も選べる。

    // class に play_polly と書かれたボタンやリンクを押すと起動する。
    // 
    //    <button 
    //      id="read_ja"
    //      class="btn btn-warning play_polly form-control"
    //      data-yomi_read="本日は晴天なり"
    //      data-yomi_elem_id="#text_ja" → 読み上げるテキスト領域のID
    //      data-player_elem_id="#yomi_player"
    //      data-device="instant_bosai"
    //      data-voice_elem_name="voice_id"
    //      data-rate_elem_name="voice_rate"
    //      data-volume_elem_name="voice_volume_ja"
    //      data-lang_code="ja"
    //      data-play_id=1
    //      >▶試聴</button>     
    //    

    $('.play_polly').click(function() {

      // 読み上げるテキストを取得します。
      // 読むテキストデータを直接指定された場合
      var yomi_read = $(this).data('yomi_read');

      // 読むテキストデータのエレメントをIDで指定された場合
      var yomi_elem_id = $(this).data('yomi_elem_id');

      if (yomi_elem_id) {
        yomi_read = $(yomi_elem_id).val();
      }

      // 選択範囲をされていた場合
      var id = $(this).attr('id');

      if (id) {
        if (id.indexOf('_selected') >= 0) {
          // 選択範囲を読み上げる
          var selected_text = document.getSelection().toString();
          if (selected_text) {
            yomi_read = selected_text;
          }
        }
      }

      if (! yomi_read) {
        yomi_read = '試聴したい文章を入力して下さい。';
      }

      // 読み上げる言語を取得します。
      var lang_code = $(this).data('lang_code');

      if (! lang_code) {
        lang_code = 'ja';
      }

      // １つのフォームで複数言語の入力を切り替えて使う編集モーダルのために、
      // 声と速度を直接指定コントロールから取得するためのトリック。
      // lang_elem_name, lang_elem_lang があると 
      // 声のIDは voice_id_{lang}, 声の速度は voice_rate_{lang}
      // のコントロールの値から取得する。
      var lang_elem_lang = '';
      var lang_elem_name = $(this).data('lang_elem_name');
      if (lang_elem_name) {
        lang_elem_lang = $('#' + lang_elem_name).val();
        lang_code = lang_elem_lang;
      }

      // 読み上げる声を取得します。
      // Amazon Polly の音声
      let voice_id = '{{config('_env.VOICE_HIGH_JA_JP')}}';

      let voice_elem_name = $(this).data('voice_elem_name');

      if (lang_elem_lang) {
        voice_elem_name = 'voice_id_' + lang_elem_lang;
      }

      if (voice_elem_name) {
        let sel = 'input:radio[name="' + voice_elem_name + '"]:checked';

        voice_id = $(sel).val();

        if (voice_id == null) {
          voice_id = $('#' + voice_elem_name).val();
        }
      }

      // 読み上げる速度を取得します。
      let speech_rate = '{{config('_env.VOICE_RATE_JA_JP')}}';

      var rate_elem_name = $(this).data('rate_elem_name');
      if (lang_elem_lang) {
        rate_elem_name = 'voice_rate_' + lang_elem_lang;
      }

      if (rate_elem_name) {
        let sel = 'input:radio[name="' + rate_elem_name + '"]:checked';

        speech_rate = $(sel).val();

        if (speech_rate == null) {
          speech_rate = $('#' + rate_elem_name).val();
        }
      }

      // 読み上げる音量を取得します。
      let voice_volume = 0;

      var volume_elem_name = $(this).data('volume_elem_name');

      if (volume_elem_name) {
        let sel = 'input:radio[name="' + volume_elem_name + '"]:checked';

        voice_volume = $(sel).val();

        if (voice_volume == null) {
          voice_volume = $('#' + volume_elem_name).val();
        }
      }

      // 音声データのIDを取得します。
      // ２回ボタンが押されてたとき、同じ音声データについてであれば停止。
      // 異なる音声データであれば、再生します。
      var play_id = $(this).data('play_id');

      // 再生するモードを選択します。
      var device = $(this).data('device') ?? 'instant';

      console.log('play_polly', 'yomi_read', yomi_read, 'lang_code', 'lang');

      // 再生するプレイヤーを取得します。
      var player_elem_id = $(this).data('player_elem_id') ?? '#yomi_player';

      // Prepare player
      var player = $(player_elem_id).get(0); // Get raw object

      // Stop player if playing.
      if (playing_id && playing_id == play_id) {
        if (! player.paused) {
          player.pause();
          set_button_ready(this);
          return false;
        }
      }

      // 試聴ボタンを、停止ボタンにする。
      set_button_playing(this);

      let supportedFormats = getSupportedAudioFormats(player);

      if (supportedFormats.length === 0) {
        for (let read in reads) {
          read.disabled = true;
        }
        alert('このブラウザは音声読み上げに対応していません。最新のブラウザをご利用下さい。');
        return false;
      }

      yomi_read = yomi_read.replace(/<say-as interpret-as=\"telephone\">/g, "SAY_START");
      yomi_read = yomi_read.replace(/<\/say-as>/g, "SAY_END");

      yomi_read = yomi_read.replace(/[\<\>\＜\＞\"\'\&]/g, "");

      yomi_read = yomi_read.replace(/SAY_START/g, "<say-as interpret-as=\"telephone\">");
      yomi_read = yomi_read.replace(/SAY_END/g, "</say-as>");

      $.ajax({
        url: '{{ route('api.yomi_ssml') }}',
        type: "POST",
        data: {
          "yomi_read": yomi_read,
          "device": device,
          "lang_code": lang_code,
          "speech_rate": parseInt(speech_rate, 10),
          "volume": voice_volume,
        },
        dataType: 'json'

      }).done(function(json) {
        console.log("Success reading play data", json);

        if (json.yomi_ssml) {

          let used_replaces = used_replaces_text(
            json.used_replaces
          );

          $('#used_replaces').text(
            used_replaces
          );

          let src_text = '{{ config('_env.POLLY_SERVER') }}/read?voiceId=' +
            encodeURIComponent(voice_id) +
            '&text=' + encodeURIComponent(json.yomi_ssml) +
            '&textType=' + encodeURIComponent('ssml') +
            '&outputFormat=' + supportedFormats[0];

          player.src = src_text;

          player.play();

          // 現在再生中の音声IDを記録
          playing_id = play_id

          // 現在再生中のプレイヤーを保持（あとで終了したかを判定するため）
          last_played_player_obj = player;
        }

      }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Failed reading read json.", jqXHR.status, textStatus, errorThrown.message);

      }).always(function() {

      });

      return false;
    });

    // プレイヤー再生完了後に放送開始ボタンの表示を戻すタイマー
    setInterval(function() {
      restore_button_label();
    }, 1000);
  });

  function set_button_ready(elem)
  {
    $(elem).removeClass('btn-outline-danger');
    $(elem).addClass('btn-warning');
    $(elem).html('<i class="bi bi-play-fill"></i> 試聴');
  }

  function set_button_playing(elem)
  {
    $(elem).removeClass('btn-warning');
    $(elem).addClass('btn-outline-danger');
    $(elem).html('<i class="bi bi-stop-fill"></i> 停止');
  }

  function restore_button_label()
  {
    if (last_played_player_obj) {
      if (last_played_player_obj.paused) {
        $('.play_polly').removeClass('btn-outline-danger');
        $('.play_polly').addClass('btn-warning');
        $('.play_polly').html('<i class="bi bi-play-fill"></i> 試聴');
        
        last_played_player_obj = null;
      }
    }
  }
</script>
