@props([
  'key', 
  'default', 
  'class' => '', 
  'div_class' => '', 
  'maxlen' => 0,
  'attribute' => '',
  'col' => 12,
  'cols' => 80,
  'rows' => 4,
])

<div class="col-md-{{$col}} mb-1">
  <div class="input-group {{$div_class}}">
    <textarea
      id="{{$key}}"
      name="{{$key}}"
      :cols="$cols""
      :rows="$rows""
      class="form-control len_count col-md-{{$col}} {{$class}} @error($key) is-invalid @else border-secondary-subtle @enderror"
      data-maxlen="{{$maxlen}}"
      data-counter="len_{{$key}}"
      {{$attribute}}
    >{{old($key, $default)}}</textarea>
    <span class="input-group-text play_polly" id="{{$key}}_grp" data-yomi_elem_id="#{{$key}}"><i class="bi bi-play-circle"></i></span>
  </div>
</div>

