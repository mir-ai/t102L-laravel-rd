@props(['label'])

<script type="module">
  $(function() {

    $('.len_count').each(function() {
      update_len_count($(this));
    });

    $('.len_count').keyup(function() {
      update_len_count($(this));
      return true;
    });
  });

  function update_len_count(elem) {
    let maxlen = elem.data('maxlen');

    if (maxlen == 0) {
      return false;
    }

    let counter = elem.data('counter');
    // 緊急速報用に、改行は2文字としてカウントしたい。
    let text = elem.val();
    text = text.replace(/\r\n/g, "|");
    text = text.replace(/\r/g, "|");
    text = text.replace(/\n/g, "|");
    text = text.replace(/\|/g, "||");
    let len = text.length;
    //console.log(`len_count ${len}`)

    text = `（${len} / ${maxlen}文字）`
    $(`#${counter}`).text(text);

    if (maxlen < len) {
      $(`#${counter}`).css('color', 'red');
    } else {
      $(`#${counter}`).css('color', 'gray');
    }

    return true;
  }

</script>
