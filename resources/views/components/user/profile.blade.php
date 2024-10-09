<x-layout.layout :user="$layout_user">
    <x-slot name="title">
        {{ __('profile.title') }}
    </x-slot>

    <x-slot name="subclass">
        main-content
    </x-slot>

    <div class="left-column">
        <h3 class="head-main">{{ $user->name }}</h3>
        <div class="user-card">
            <div class="photo-rate">
                <img class="card-photo" src="{{ $user->avatar ? asset("storage/{$user->id}/{$user->avatar}") : Vite::asset('resources/img/avatars/def-avatar.jpg') }}" width="191" height="190" alt="Фото пользователя">
                <div class="card-rate">
                    <div class="stars-rating big">
                        @php
                            $rating = $user->rating();
                        @endphp
                        <span class="{{ $rating >= 1 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $rating >= 2 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $rating >= 3 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $rating >= 4 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $rating >= 5 ? 'fill-star' : '' }}">&nbsp;</span>
                    </div>
                    <span class="current-rate">{{ $user->rating() }}</span>
                </div>
            </div>
            <p class="user-description">
                {{ $user->about }}
            </p>
        </div>
        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">{{ __('profile.spec') }}</p>
                <ul class="special-list">
                    @foreach($user->categories as $category)
                        <li class="special-item">
                            <a href="{{ route('tasks.index', ["category_{$category->id}" => $category->id]) }}" class="link link--regular">{{ __("categories.{$category->name}") }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">{{ __('profile.city_age') }}</p>
                <p class="bio-info">
                    <span class="town-info">{{ $user->city->name }}</span>,
                    @php
                        $age = \Carbon\Carbon::parse($user->birth_date)->age;
                    @endphp
                    @php
                        $locale = \Illuminate\Support\Facades\App::getLocale();
                        $localeRu = 'ru';
                        $oneYear = $locale == $localeRu ? 'год' : 'year';
                        $twoYear = $locale == $localeRu ? 'года' : 'years';
                        $manyYear = $locale == $localeRu ? 'лет' : 'years';
                    @endphp
                    <span class="age-info">{{ $age }}</span> {{ \App\Helpers::getNounPluralForm($age, $oneYear, $twoYear, $manyYear) }}
                </p>
            </div>
        </div>
        <h4 class="head-regular">{{ __('profile.feedbacks') }}</h4>
        @foreach($user->executorFeedbacks as $feedback)
            @php
                $customer = $feedback->customer;
            @endphp
            <div class="response-card">
                <img class="customer-photo" src="{{ $customer->avatar ? asset("storage/{$customer->id}/{$customer->avatar}") : Vite::asset('resources/img/avatars/def-avatar.jpg') }}" width="120" height="127" alt="Фото заказчиков">
                <div class="feedback-wrapper">
                    <p class="feedback">{{ $feedback->feedback }}</p>
                    <p class="task">{{ __('profile.task') }} «<a href="{{ route('tasks.show', $feedback->task_id) }}" class="link link--small">{{ $feedback->task->title }}</a>» {{ $feedback->task->status->name == \App\Models\Status::DONE ? __('profile.done') : __('profile.fail')}}</p>
                </div>
                <div class="feedback-wrapper">
                    <div class="stars-rating small">
                        <span class="{{ $feedback->rating >= 1 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $feedback->rating >= 2 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $feedback->rating >= 3 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $feedback->rating >= 4 ? 'fill-star' : '' }}">&nbsp;</span>
                        <span class="{{ $feedback->rating >= 5 ? 'fill-star' : '' }}">&nbsp;</span>
                    </div>
                    @php
                        list($numberAgo, $pluralForm) = \App\Helpers::getNounPluralDateForm($feedback->created_at);
                    @endphp
                    <p class="info-text"><span class="current-time">{{ $numberAgo }} {{ $pluralForm }} </span>{{ __('profile.ago') }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">{{ __('profile.stat') }}</h4>
            <dl class="black-list">
                <dt>{{ __('profile.all') }}</dt>
                <dd>{{ $user->getCountDoneTasks() }} {{ __('profile.done') }}, {{ $user->getCountFailedTasks() }} {{ __('profile.fail') }}</dd>
                <dt>{{ __('profile.rating') }}</dt>
                <dd>{{ $ratingExecutors }} {{ __('profile.position') }}</dd>
                <dt>{{ __('profile.reg_date') }}</dt>
                @php
                    $locale = config('app.locale');
                @endphp
                <dd>{{ \Carbon\Carbon::parse($user->created_at)->locale($locale)->translatedFormat('d F, H:i') }}</dd>
            </dl>
        </div>
        <div class="right-card white">
            <h4 class="head-card">{{ __('profile.contacts') }}</h4>
            <ul class="enumeration-list">
                @if($user->mobile)
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--phone">{{ $user->mobile }}</a>
                    </li>
                @endif
                @if($user->email)
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--email">{{ $user->email }}</a>
                    </li>
                @endif
                @if($user->telegram)
                    <li class="enumeration-item">
                        <a href="#" class="link link--block link--tg">{{ $user->telegram }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</x-layout.layout>
