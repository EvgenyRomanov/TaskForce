<x-layout.layout :user="$user">
    <x-slot name="title">
        {{ __('new_tasks.title') }}
    </x-slot>

    <x-slot name="activeLink">
        new
    </x-slot>

    <x-slot name="subclass">
        main-content
    </x-slot>

    <div class="left-column">
        <h3 class="head-main head-task">{{ __('new_tasks.new') }}</h3>
        @foreach($tasks as $task)
            <div class="task-card">
                <div class="header-task">
                    <a href="{{ route('tasks.show', $task->id) }}" class="link link--block link--big">{{ $task->title }}</a>
                    @if($task->budget)
                        <p class="price price--task">{{ $task->budget }} ₽</p>
                    @endif
                </div>
                @php
                    list($numberAgo, $pluralForm) = \App\Helpers::getNounPluralDateForm($task->created_at);
                @endphp
                <p class="info-text"><span class="current-time">{{ $numberAgo }} {{ $pluralForm }} </span>{{ __('new_tasks.ago') }}</p>
                <p class="task-text">
                    {{ $task->description }}
                </p>
                <div class="footer-task">
                    @if($task->city)
                        <p class="info-text town-text">{{ $task->city->name }}</p>
                    @endif
                    <p class="info-text category-text">{{ __("categories.{$task->category->name}") }}</p>
                    <a href="{{ route('tasks.show', $task->id) }}" class="button button--black">{{ __('new_tasks.show') }}</a>
                </div>
            </div>
        @endforeach
        {{ $tasks->links('vendor.pagination.custom-simple-bootstrap-5', compact('tasks')) }}
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <form method="get" action="{{ route('tasks.index') }}">
                    <h4 class="head-card">{{ __('new_tasks.cat') }}</h4>
                    <div class="form-group">
                        @foreach($categories as $category)
                            <label class="control-label" for="services-{{ $category->id }}">
                                <input type="checkbox" id="services-{{ $category->id }}" name="category_{{ $category->id }}" value="{{ $category->id }}" @checked( Request::get("category_{$category->id}") )>
                                {{ __("categories.{$category->name}") }}</label>
                        @endforeach
                    </div>
                    <h4 class="head-card">{{ __('new_tasks.add') }}</h4>
                    <div class="form-group">
                        <label class="control-label" for="without-response">
                            <input id="without-response" type="checkbox" @checked( Request::get("without_response") ) name="without_response">
                            {{ __('new_tasks.w_res') }}</label>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="remote_work">
                            <input id="remote_work" type="checkbox" @checked( Request::get("remote_work") ) name="remote_work">
                            {{ __('new_tasks.r_work') }}</label>
                    </div>
                    <h4 class="head-card">{{ __('new_tasks.time') }}</h4>
                    <div class="form-group">
                        <label for="period-value"></label>
                        <select id="period-value" name="period">
                            <option value="1" @selected(Request::get("period") == 1)>1 {{ __('new_tasks.1_hour') }}</option>
                            <option value="12" @selected(Request::get("period") == 12)>12 {{ __('new_tasks.12_hours') }}</option>
                            <option value="24" @selected(Request::get("period") == 24)>24 {{ __('new_tasks.24_hours') }}</option>
                        </select>
                    </div>
                    <input type="submit" class="button button--blue" value="{{ __('new_tasks.sh') }}">
                </form>
            </div>
        </div>
    </div>
</x-layout.layout>
