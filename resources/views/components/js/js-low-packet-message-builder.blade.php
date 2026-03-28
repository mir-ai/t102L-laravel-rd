@props(['category_code'])

{{-- 低パケット型同報 --}}
<!-- js-low-packet-message-builder -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>

<script type="module">
  $(function() {
    $('.select2-ajax').select2({
      ajax: {
        url: '{{route('api_stored_audio_file_picker', ['category_code' => $category_code])}}',
        dataType: 'json',
        processResults(response) {
           // データをselect2向けに加工
          let options = [];

          response.data.forEach((item) => {

            options.push({
              id: item.id,
              text: item.text_body
            });

          });

          return {
            results: options,
            pagination: {
              more: (response.next_page_url !== null) // 次ページがあるかどうか
            }
          };

        }
      },
      language: 'ja', // 日本語化
      // placeholder: '放送文を選択します。', // For single selects only. (表示不可)
      allowClear: true,
      sorter: data => data.sort((a, b) => a.id < b.id),
    });
  });
</script>
