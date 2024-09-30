<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'TaskForce' }}</title>
    @vite('resources/css/style.css')
    {{ $yandexJs ?? '' }}
    {{ $styleCss ?? '' }}
</head>
<body>
<header class="page-header">
    <nav class="main-nav">
        <a href='{{ route('home') }}' class="header-logo">
            <img class="logo-image" src="{{ Vite::asset('resources/img/logotype.png') }}" width=227 height=60 alt="taskforce">
        </a>
        <div class="nav-wrapper">
            <ul class="nav-list">
                <li class="list-item {{ ($activeLink ?? '') == 'new' ? 'list-item--active' : '' }}">
                    <a href="{{ route('tasks.index') }}" class="link link--nav" >{{ __('layout.new') }}</a>
                </li>
                <li class="list-item {{ ($activeLink ?? '') == 'my' ? 'list-item--active' : '' }}">
                    <a href="{{ route('tasks.my_tasks') }}" class="link link--nav" >{{ __('layout.my') }}</a>
                </li>
                @if(Auth::check() && $user->isCustomer())
                    <li class="list-item {{ ($activeLink ?? '') == 'create' ? 'list-item--active' : '' }}">
                        <a href="{{ route('tasks.create') }}" class="link link--nav" >{{ __('layout.create') }}</a>
                    </li>
                @endif
                <li class="list-item {{ ($activeLink ?? '') == 'settings' ? 'list-item--active' : '' }}">
                    <a href="{{ route('profile.edit') }}" class="link link--nav" >{{ __('layout.set') }}</a>
                </li>
            </ul>
        </div>
    </nav>
    @auth
        <div class="user-block">
            <a href="#">
                <img class="user-photo" src="{{ $user->avatar ? asset("storage/{$user->id}/{$user->avatar}") : Vite::asset('resources/img/avatars/def-avatar.jpg') }}" width="55" height="55" alt="Аватар">
            </a>
            <div class="user-menu">
                <p class="user-name">{{ $user->name }}</p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <li class="menu-item">
                            <a href="{{ route('profile.edit') }}" class="link">{{ __('layout.set') }}</a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('logout') }}" class="link">{{ __('layout.logout') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endauth
</header>
<main class="container {{ $subclass }}">
    {{ $slot }}
</main>
    {{ $section ?? '' }}
    {{ $mainJs ?? '' }}
</body>
</html>
