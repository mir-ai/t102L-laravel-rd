@props(['chartid', 'times', 'label', 'cpukvs', 'key', 'values', 'colors', 'cpuids', 'options'])

{{-- BOX の監視グラフを書くための1グラフのJS --}}

let {{$chartid}}_ctx = document.getElementById('{{ $chartid }}');
let {{$chartid}}_cht = new Chart({{$chartid}}_ctx, {
  type: 'line',
  data: {
    labels: @json($times ?? []),
    datasets: [
      @foreach ($cpukvs as $cpuid => $cpulabel)
        @if (in_array($cpulabel, $cpuids))
          {
            label: '{{$cpulabel}}',
            data: @json($values[$cpuid] ?? []),
            borderColor: '{{$colors[$cpulabel]}}',
            backgroundColor: '{{$colors[$cpulabel]}}',
          },
        @endif
      @endforeach
    ]
  },
  options: {{$options}},
});
