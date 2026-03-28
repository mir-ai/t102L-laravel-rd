@props(['row_keys', 'col_keys', 'row_col_keys1', 'row_col_keys2', 'col_names' => []])

@forelse ($row_keys as $row_key)
      {{-- テーブル開く --}}
      @if ($loop->first)
        <table class="table table-responsive table-bordered table-sm">
          <thead>
            <tr>
              @if ($col_names)
                @foreach ($col_names as $col_name)
                  <th>{{$col_name}}</th>
                @endforeach
              @else
                @foreach ($col_keys as $col_key)
                  <th>{{$col_key}}</th>
                @endforeach
              @endif
            </tr>
          </thead>
          <tbody>
      @endif

      <tr>
        @foreach ($col_keys as $col_key)
          {{-- 情報を表示 --}}
          <td>
            @php
              $v1 = $row_col_keys1[$row_key][$col_key] ?? '';
              $v2 = $row_col_keys2[$row_key][$col_key] ?? '';
              
              $v1 = strip_tags($v1);
              $v2 = strip_tags($v2);
              
              $disp = '';
              if ($v1 == $v2) {
                  $disp = $v1;
              } elseif ($v1 != '' && $v2 == '') {
                  $disp = "<span class=\"text-danger\"><STRIKE>{$v1}</STRIKE></span>";
              } elseif ($v1 == '' && $v2 != '') {
                  $disp = "<span class=\"text-success\">{$v2}</span>";
              } elseif ($v1 != $v2) {
                  $disp = "<span class=\"text-danger\">{$v1}</span><br /><span class=\"text-success\">{$v2}</span>";
              }
            @endphp
            {!! $disp !!}
          </td>
        @endforeach
      </tr>

      {{-- テーブル閉じる --}}
      @if ($loop->last)
        </tbody>
        </table>
      @endif

    @empty

      <div class="card">
        <div class="card-body">
          <h4 class="card-title">データはありません</h4>
        </div>
      </div>

    @endforelse
