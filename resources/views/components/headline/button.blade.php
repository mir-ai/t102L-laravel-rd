@props(['title', 'href', 'class' => 'btn-success', 'btn_label' => '編集', 'href2' => '', 'class2' => 'btn-success', 'btn_label2' => '編集', 'href3' => '', 'class3' => 'btn-success', 'btn_label3' => '編集'])

<div class="row mt-4 mb-3">
  <div class="col-sm-6">
    <h3 class="miraie2 my-0">{{$title}}</h3>
  </div>
  <div class="col-sm-6 text-end text-nowrap">
    <a href="{{$href}}" class="btn {{$class}}">{{$btn_label}}</a>
    @if ($href2)
      <a href="{{$href2}}" class="btn {{$class2}} ms-2">{{$btn_label2}}</a>
    @endif
    @if ($href3)
      <a href="{{$href3}}" class="btn {{$class3}} ms-2">{{$btn_label3}}</a>
    @endif    
  </div>
</div>
