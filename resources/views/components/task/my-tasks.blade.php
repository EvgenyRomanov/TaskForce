<x-layout.layout :user="$user">
    <x-slot name="title">
        {{ __('my_tasks.title') }}
    </x-slot>

    <x-slot name="activeLink">
        my
    </x-slot>

    <x-slot name="subclass">
        main-content
    </x-slot>

    <div class="left-menu">
        <h3 class="head-main head-task">{{ __('my_tasks.my') }}</h3>
        <ul class="side-menu-list">
            @if($user->isCustomer())
                <li style="width: fit-content" class="side-menu-item {{ in_array(Request::get("status"), ['new', null]) ? 'side-menu-item--active' : '' }}">
                    <a href="{{ route('tasks.my_tasks', ['status' => 'new']) }}" class="link link--nav">{{ __('my_tasks.new') }}</a>
                </li>
            @else
                <li style="width: fit-content" class="side-menu-item {{ Request::get("status") == 'expired' ? 'side-menu-item--active' : '' }}">
                    <a href="{{ route('tasks.my_tasks', ['status' => 'expired']) }}" class="link link--nav">{{ __('my_tasks.ex') }}</a>
                </li>
            @endif
            <li style="width: fit-content" class="side-menu-item {{ (Request::get("status") == 'in_progress') || ($user->isExecutor() && !Request::get("status")) ? 'side-menu-item--active' : '' }}">
                <a href="{{ route('tasks.my_tasks', ['status' => 'in_progress']) }}" class="link link--nav">{{ __('my_tasks.in_prog') }}</a>
            </li>
            <li style="width: fit-content" class="side-menu-item {{ Request::get("status") == 'done' ? 'side-menu-item--active' : '' }}">
                <a href="{{ route('tasks.my_tasks', ['status' => 'done']) }}" class="link link--nav">{{ __('my_tasks.cl') }}</a>
            </li>
        </ul>
    </div>
    <div class="left-column left-column--task">
        <h3 class="head-main head-regular">
            @if(in_array(Request::get("status"), ['new', null]))
                {{ __('my_tasks.new') }}
            @elseif(Request::get("status") == 'expired')
                {{ __('my_tasks.ex') }}
            @elseif((Request::get("status") == 'in_progress') || ($user->isExecutor() && !Request::get("status")))
                {{ __('my_tasks.in_prog') }}
            @elseif(Request::get("status") == 'done')
                {{ __('my_tasks.cl') }}
            @endif
        </h3>
        @foreach($tasks as $task)
            <div class="task-card">
                <div class="header-task">
                    <a  href="{{ route('tasks.show', $task->id) }}" class="link link--block link--big">{{ $task->title }}</a>
                    @if($task->budget)
                        <p class="price price--task">{{ $task->budget }} ₽</p>
                    @endif
                </div>
                @php
                    list($numberAgo, $pluralForm) = \App\Helpers::getNounPluralDateForm($task->created_at);
                @endphp
                <p class="info-text"><span class="current-time">{{ $numberAgo }} {{ $pluralForm }} {{ __('my_tasks.ago') }}</span></p>
                <p class="task-text">
                    {{ $task->description }}
                </p>
                <div class="footer-task">
                    @if($task->city)
                        <p class="info-text town-text">{{ $task->city->name }}</p>
                    @endif
                    <p class="info-text category-text">{{ __("categories.{$task->category->name}") }}</p>
                    <a href="{{ route('tasks.show', $task->id) }}" class="button button--black">{{ __('my_tasks.show') }}</a>
                </div>
            </div>
        @endforeach
    </div>
</x-layout.layout>
