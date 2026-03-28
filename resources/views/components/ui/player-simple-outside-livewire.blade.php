@props(['id', 'mp3_url'])

<audio id="audio-{{$id}}" src="{{$mp3_url}}" preload="metadata" class="d-none"></audio>
