@props(['label', 'action'])

<script type="module">
  $(function() {
    $('#exec_destroy').click(function() {
      let ret = confirm("{{ $label }}を{{ $action ?? '削除' }}してよろしいですか？");
      if (!ret) {
        return false;
      }
    });
  });
</script>
