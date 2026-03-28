@props([
  'key', 
  'desc' => '', 
  'maxlen',
  'class' => '',
])
    </div>
    <div class="form-text mt-0">
      <span class="{{$class}}">
      <x-input.error-message :key="$key" />
      <span class="mt-0">{!! $desc !!}</span>

      @if ($maxlen ?? 0)
        <span id="len_{{$key}}">（{{$maxlen}}文字）</span>
      @endif
      </span>
    </div>
    
  </div>
</div>
