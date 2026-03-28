@props(['conds'])

@foreach ($conds as $k => $v)
  @if (filled($v))
    <input type="hidden" name="{{$k}}" value="{{$v}}">
  @endif
@endforeach
