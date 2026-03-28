@props(['row_keys', 'col_keys', 'col_names' => null, 'row_col_keys1', 'row_col_keys2'])

@forelse ($row_keys as $row_key)
  {{-- テーブル開く --}}
  @if ($loop->first)
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <tbody>
          @if ($col_names)
            <tr class="table-primary">
              @for ($x = 0; $x < count($col_names); $x++)
                <th>{{ $col_names[$x] }}</th>
              @endfor
            </tr>
            <tr class="table-primary">
              @for ($x = 0; $x < count($col_keys); $x++)
                <th>{{ $col_keys[$x] }}</th>
              @endfor
            </tr>
          @endif
  @endif

  @php
    $deleted = $row_col_keys2[$row_key]['Delete1'] ?? '';
  @endphp

  <tr @class([$deleted => 'table-dark'])>
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
              $disp = "<span class=\"text-danger\"><s>{$v1}</s></span>";
          } elseif ($v1 == '' && $v2 != '') {
              $disp = "<span class=\"text-success\">{$v2}</span>";
          } elseif ($v1 != $v2) {
              $disp = "<span class=\"text-danger\"><s>{$v1}</s></span><br /><span class=\"text-success\">{$v2}</span>";
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
    </div>
  @endif

@empty

  <div class="card">
    <div class="card-body">
      <h4 class="card-title">データはありません</h4>
    </div>
  </div>

@endforelse
