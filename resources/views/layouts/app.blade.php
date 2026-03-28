<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="{{ config('_env.FAVICON_FORCE_URL') }}">

  {{-- CSRF Token --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Title --}}
  <title>
    {{ $page_title ?? config('_env.APP_NAME_JP') }}
  </title>

  {{-- スクリプト --}}
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  {{-- スクリプト --}}
  @yield('js')
  @stack('scripts')
  @yield('css')
  @yield('meta')
</head>

<body>
  <div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
          <img alt="{{ config('_env.APP_NAME_JP') }}" src="{{ config('_env.HEAD_LOGO_IMG_URL') }}" height="32"
            width="32" class="d-inline-block align-text-top">
          <span class="ms-2">
            {{ config('_env.APP_NAME_JP', '管理画面') }}
          </span>
          @if (request()->route()->getName() != 'index')
            <span class="ms-2 fw-bold">
              <a href="{{ route('index') }}">トップに戻る</a>
            </span>
          @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav me-auto">
          </ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ms-auto">

            @guest
              @if (Route::has('login'))
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
              @endif

              @if (Route::has('register'))
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
              @endif
            @else
              @php
                $route_name = request()?->route()?->getName();
              @endphp

              @if (Auth::user()->IsMasterAdmin)
                {{-- ダッシュボード --}}
                <x-hnav.link label="ダッシュボード" :active="str_starts_with($route_name, 'r4.dashboard')" :href="route('r4.dashboard')" />

                {{-- 着信メール --}}
                <x-hnav.link label="着信メール" :active="str_starts_with($route_name, 'r4.inbound_mails')" :href="route('r4.inbound_mails.index')" />

                {{-- サービス --}}
                <x-hnav.link label="サービス" :active="str_starts_with($route_name, 'r4.services')" :href="route('r4.services.index')" />

                {{-- アカウント --}}
                <x-hnav.link label="アカウント" :active="str_starts_with($route_name, 'r4.users')" :href="route('r4.users.index')" />
              @endif

              <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                  {!! Auth::user()->name !!}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                  <x-dropdown.dropdown />

                  <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                </div>
              </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>

    <main class="py-4">
      <div class="container{{ isset($container_fluid) ? '-fluid' : '' }}">
        {{-- フラッシュメッセージ --}}
        <x-flash.message />
        <x-flash.warning />
        <x-flash.modal />

        {{-- タイトル --}}
        @if (!empty($title))
          <div class="row">
            <div class="col-sm-8">
              <h2>{{ $title }}</h2>
            </div>
            <div class="col-sm-4">
              @yield('rightnav')
            </div>
          </div>
        @endif

        @yield('content')
      </div>

      @if (empty($hide_footer))
        <div>
          <hr />
          <p align="center">
            © {{ now()->format('Y') }} {{ config('_env.FOOTER_CREDIT') }}

            <x-dev.dump-route />
          </p>
        </div>
      @endif
    </main>
  </div>

  <div class="d-none">
    @stack('hidden-div')
  </div>

  @livewireScripts

  @stack('js-bottom')
</body>

</html>
