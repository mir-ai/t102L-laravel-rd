@props(['action', 'method' => 'GET', 'class', 'enctype', 'accept', 'id' => '', 'target', 'hide_error' => 0])

@if (! $hide_error)
  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      <p class="fs-6 fw-bold mb-2">エラーがありました。</p>
      <ul class="my-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
@endif

<form 
  id="{{$id}}"
  role="form" @if ($id) id="{{ $id }}" @endif 
  action="{!! $action !!}"
  method="{{ strcasecmp($method, 'get') ? 'POST' : 'GET' }}" 
  enctype="{{ $enctype ?? '' }}" 
  accept="{{ $accept ?? '' }}"
  class="{{ $class ?? '' }}" 
  target="{{ $target ?? '' }}"
>
  @method($method)
  @csrf
