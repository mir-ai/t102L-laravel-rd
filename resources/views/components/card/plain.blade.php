<div class="card {{$class ?? ''}}">
  <div class="card-body">
    <h5 class="card-title">{{ $title }}</h5>
    {{ $slot }}
  </div>
</div>
