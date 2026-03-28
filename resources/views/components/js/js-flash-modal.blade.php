@if (Session::has('flash_modal'))
  {{-- フラッシュセッション flash_modal が定義されていたら、 flash_modal ダイアログを起動する。 --}}
  <script type="module">
    $(function() {
      $('#flash_modal').modal('show');
    });
  </script>
@endif

