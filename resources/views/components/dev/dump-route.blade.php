@if (App::environment('local'))
    @php
        $routeName = request()->route()->getName();
        logger("ROUTE {$routeName}");
    @endphp
    <br /><span class="text-muted">{{ $routeName }}</span>
@endif
