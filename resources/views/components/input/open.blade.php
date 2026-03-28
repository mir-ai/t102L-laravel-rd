@props([
  'key',
  'label',
  'required' => null,
  'checked',
  'class' => null,
  'col_label' => 3,
])

@php($col_body = (12 - $col_label))

<div class="row mb-3 {{$class}}">
  <label for="{{$key}}" class="col-md-{{$col_label}} col-form-label">
    <span class="fw-bold font-weight-bold @error($key) text-danger @endif">{{$label}}</span>
    <span class="lg-s1">
    @if ($required == 'Y')
      <small class="text-danger text-nowrap ms-2"><i class="bi bi-asterisk"></i></small>
    @elseif ($required == 'N')
      <small class="text-secondary text-nowrap ms-2">任意</small>
    @endif
    </span>
  </label>
  <div class="col-md-{{$col_body}}">
    <div class="row">
