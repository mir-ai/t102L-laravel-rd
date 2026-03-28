@props(['mp3_url', 'id', 'size' => 8, 'with_player' => true])
<!-- player-simple -->

<div class="d-flex" id="audio-player-container-{{$id}}">
  <div class="col-auto align-self-center">
    <button class="play-button btn btn-warning btn-sm" id="play-icon-{{$id}}"><i class="bi bi-play-fill"></i></button>
  </div>
  <div class="col-auto align-self-center">
    <span id="current-time-{{$id}}" class="time">0:00</span>
  </div>
  <div class="col-auto align-self-center">
    <input type="range" id="seek-slider-{{$id}}" max="100" value="0" style="width: {{$size}}em;">
  </div>
  <div class="col-auto align-self-center">
    <span id="duration-{{$id}}" class="time d-none">0:00</span>
  </div>
  <div class="col align-self-center">
  </div>
  <div class="col-auto">
    @if ($with_player)
      <audio id="audio-{{$id}}" src="{{$mp3_url}}" preload="metadata"></audio>
    @endif
  </div>
</div>

<!-- / player-simple -->
