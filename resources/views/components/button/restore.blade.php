@props(['url', 'label', 'class' => ''])

<form role="form" action="{{ $url }}" method="post">
  @method('PATCH')
  @csrf

  <input type="submit" name="{{ $label  ?? '削除取消' }}" class="btn btn-success" dusk="submit-button" id="exec_restore" />

</form>
