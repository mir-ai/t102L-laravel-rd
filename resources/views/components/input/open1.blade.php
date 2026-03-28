@props([
  'key',
  'label',
  'required' => null,
  'checked',
  'class' => null
])

<div class="row {{$class}}">
  <label for="{{$key}}" class="col-md-12 col-form-label">
    <span class="fw-bold">{{$label}}</span>
    @if ($required == 'Y')
      <small class="text-danger text-nowrap ms-2">必須</small>
    @elseif ($required == 'N')
      <small class="text-secondary text-nowrap ms-2">任意</small>
    @endif
  </label>
</div>
<div class="row mb-3 {{$class}}">
  <div class="col-md-12">
    <div class="row">
