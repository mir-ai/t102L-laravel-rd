@props(['id'])

{{-- https://css-tricks.com/lets-create-a-custom-audio-player/ --}}
<!-- Simple player id={{$id}} -->

<script type="module">
  $(function(){
    const audioPlayerContainer{{$id}} = $('#audio-player-container-{{$id}}');
    const playIconContainer{{$id}} = $('#play-icon-{{$id}}');
    const currentTimeContainer{{$id}} = $('#current-time-{{$id}}');
    const seekSlider{{$id}} = $('#seek-slider-{{$id}}');
    const audio{{$id}} = $('#audio-{{$id}}');
    const durationContainer{{$id}} = $('#duration-{{$id}}');

    let raf{{$id}} = null;
    let playState{{$id}} = 'play';

    playIconContainer{{$id}}.on('click', () => {
      if (playState{{$id}} === 'play') {
        audio{{$id}}.get(0).play();
        requestAnimationFrame(whilePlaying{{$id}});
        playState{{$id}} = 'pause';
        playIconContainer{{$id}}.html('<i class="bi bi-stop-fill text-danger"></i>');
      } else {
        audio{{$id}}.get(0).pause();
        cancelAnimationFrame(raf{{$id}});
        playState{{$id}} = 'play';
        playIconContainer{{$id}}.html('<i class="bi bi-play-fill"></i>');
      }
    });

    seekSlider{{$id}}.on('input', (e) => {
      showRangeProgress{{$id}}(e.target);
    });

    seekSlider{{$id}}.on('input', () => {
      currentTimeContainer{{$id}}.text(calculateTime{{$id}}(seekSlider{{$id}}.val()));
      if (!audio{{$id}}.get(0).paused) {
        cancelAnimationFrame(raf{{$id}});
      }
    });

    seekSlider{{$id}}.on('change', () => {
      audio{{$id}}.get(0).currentTime = seekSlider{{$id}}.val();
      if (!audio{{$id}}.get(0).paused) {
        requestAnimationFrame(whilePlaying{{$id}});
      }
    });

    const showRangeProgress{{$id}} = (rangeInput) => {
      if (rangeInput === seekSlider{{$id}}) {
        audioPlayerContainer{{$id}}.css(
          '--seek-before-width',
          rangeInput.value / rangeInput.max * 100 + '%'
        );
      } else {
        audioPlayerContainer{{$id}}.css(
          '--volume-before-width',
          rangeInput.value / rangeInput.max * 100 + '%'
        );
      }
    }

    const calculateTime{{$id}} = (secs) => {
      const minutes = Math.floor(secs / 60);
      const seconds = Math.floor(secs % 60);
      const seconds00 = (seconds < 10) ? `0${seconds}` : `${seconds}`;
      const returnedSeconds = `${minutes}:${seconds00}`;
      return returnedSeconds
    }

    const displayDuration{{$id}} = () => {
      durationContainer{{$id}}.html(calculateTime{{$id}}(audio{{$id}}.get(0).duration));
    }

    const setSliderMax{{$id}} = () => {
      seekSlider{{$id}}.attr('max', Math.floor(audio{{$id}}.get(0).duration));
    }

    const displayBufferedAmount{{$id}} = () => {
      const bufferedAmount = Math.floor(audio{{$id}}.get(0).buffered.end(audio{{$id}}.get(0).buffered.length - 1));
      audioPlayerContainer{{$id}}.css(
        '--buffered-width',
        `${(bufferedAmount / seekSlider{{$id}}.attr('max')) * 100}%`
      );
    }

    audio{{$id}}.on('progress', function() { 
      displayBufferedAmount{{$id}};
    });

    audio{{$id}}.on('ended', function() { 
      playIconContainer{{$id}}.html('<i class="bi bi-play-fill"></i>');
    });

    const whilePlaying{{$id}} = () => {
      seekSlider{{$id}}.val(Math.floor(audio{{$id}}.get(0).currentTime));
      currentTimeContainer{{$id}}.text(calculateTime{{$id}}(seekSlider{{$id}}.val()));
      displayBufferedAmount{{$id}};

      audioPlayerContainer{{$id}}.css(
        '--seek-before-width',
        `${seekSlider{{$id}}.val() / seekSlider{{$id}}.attr('max') * 100}%`
      );
      raf{{$id}} = requestAnimationFrame(whilePlaying{{$id}});
    }

    if (audio{{$id}}.get(0).readyState > 0) {
      displayDuration{{$id}}();
      setSliderMax{{$id}}();
      displayBufferedAmount{{$id}}();
    } else {
      audio{{$id}}.on('loadedmetadata', () => {
        displayDuration{{$id}}();
        setSliderMax{{$id}}();
        displayBufferedAmount{{$id}}();
      });
    }

  });

</script>