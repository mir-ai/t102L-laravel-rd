@props(['column', 'conds', 'route', 'desc' => false])

{{-- ソートボタンを作る --}}
@php
    $is_sort_this_column = (($conds['o'] == $column) || ($conds['o'] == "{$column}-"));

    $is_desc = str_contains($conds['o'], '-');
    if (! $is_sort_this_column) {
        // ソートがこのカラムではなかったら
        if ($desc) {
            // 逆順でこのカラムのソートリンクを作る
            $new_conds = array_merge($conds, ['o' => "{$column}-"]);
        } else {
            // 正順でこのカラムのソートリンクを作る
            $new_conds = array_merge($conds, ['o' => "{$column}"]);
        }
        $mark = '△';

    } else {
        if ($is_desc) {
            // ソートがこのカラムで、逆順だったら
            $new_conds = array_merge($conds, ['o' => $column]);
            $mark = '▼';

        } else {
            // ソートがこのカラムで、正順だったら
            $new_conds = array_merge($conds, ['o' => "{$column}-"]);
            $mark = '▲';
        }
    }
@endphp

<span><a href="{{ route($route, $new_conds)}}" class="text-decoration-none">{{$mark}}</a></span>    
