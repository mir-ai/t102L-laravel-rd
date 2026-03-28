@props([
    'items',
    'prefix',
    'is_enabled',
    'class' => '',
    'col' => 12,
])

@foreach ($items as $id => $label)
  @php
    $key = "{$prefix}:{$id}";
    $_db = $is_enabled[$id] ?? '';
  @endphp

  <div class="form-check">
    <input type="checkbox" name="{{ $key }}" id="{{ $key }}" value="Y" 
      class="form-check-input {{$class}}"
      @checked(old($key, $_db) == 'Y')
    >
    <label for="{{ $key }}" class="form-check-label">
      {!! $label !!}
    </label>
  </div>
@endforeach
