@props([
  'backUrl',
  'backLabel' => '戻る',
])

<div class="row">
  <div class="col-md-12">
    <a href="{{$backUrl}}" class="btn btn-outline-primary form-control mt-3">{{$backLabel}}</a>
  </div>
</div>
