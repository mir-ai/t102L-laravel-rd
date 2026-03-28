@props(['label'])

<script type="module">
  $(function() {
    $('#exec_restore').click(function() {
      let ret = confirm("{{ $label }}の削除を取り消しますか？");
      if (!ret) {
        return false;
      }
    });
  });
</script>
