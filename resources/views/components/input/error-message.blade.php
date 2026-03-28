@props([
  'key',
  'class' => 'text-danger'
])

@error($key)
  @foreach($errors->get($key) as $message)
    <span class="text-danger">
      {{$message}}
    </span>
  @endforeach  
@enderror
