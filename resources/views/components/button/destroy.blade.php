@props(['url', 'label' => '削除', 'class' => '', 'id' => 'exec_destroy'])

{{-- 他の<form></form>と入れ子にすると、効かなくなりますので注意 --}}
<form role="form" action="{{ $url }}" method="POST">
  @method('DELETE')
  @csrf
  <button type="submit" id="{{$id}}" class="btn btn-danger ms-3 mb-2 {{$class}}">{{$label}}</button>
</form>
