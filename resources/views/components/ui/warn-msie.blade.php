@php
$user_agent = request()->header('User-Agent');
$is_msie = strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident/') !== false;
@endphp

@if ($is_msie)
  <div class="alert alert-danger" role="alert">
    ご利用のブラウザは、非推奨となっている「インターネットエクスプローラー」です。一部の機能が正しく動作しません。Google Chrome や Microsoft Edgeなど、他のブラウザをご利用下さい。
  </div>
@endif
