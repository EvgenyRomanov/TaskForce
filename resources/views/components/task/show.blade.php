<x-layout.layout :user="$user">
    <x-slot name="title">
        {{ __('show_task.title') }}
    </x-slot>

    <x-slot name="subclass">
        main-content
    </x-slot>

    <x-slot name="mainJs">
        @vite('resources/js/app.js')
    </x-slot>

    <x-slot name="styleCss">
        <style>
            .rating-area {
                overflow: hidden;
                width: 210px;
            }
            .rating-area:not(:checked) > input {
                display: none;
            }
            .rating-area:not(:checked) > label {
                float: right;
                width: 42px;
                padding: 0;
                cursor: pointer;
                font-size: 32px;
                line-height: 32px;
                color: lightgrey;
                text-shadow: 1px 1px #bbb;
            }
            .rating-area:not(:checked) > label:before {
                content: '★';
            }
            .rating-area > input:checked ~ label {
                color: gold;
                text-shadow: 1px 1px #c60;
            }
            .rating-area:not(:checked) > label:hover,
            .rating-area:not(:checked) > label:hover ~ label {
                color: gold;
            }
            .rating-area > input:checked + label:hover,
            .rating-area > input:checked + label:hover ~ label,
            .rating-area > input:checked ~ label:hover,
            .rating-area > input:checked ~ label:hover ~ label,
            .rating-area > label:hover ~ input:checked ~ label {
                color: gold;
                text-shadow: 1px 1px goldenrod;
            }
            .rate-area > label:active {
                position: relative;
            }
        </style>
    </x-slot>

    <x-slot name="section">
        <section class="pop-up pop-up--cancel pop-up--close">
            <div class="pop-up--wrapper">
                <h4>{{ __('show_task.cancel_task') }}</h4>
                <form method="post" action="{{ route('tasks.cancel', $task->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="cancel">
                    <input type="submit" class="button button--pop-up button--orange" value="{{ __('show_task.cancel_button') }}">
                </form>
                <div class="button-container">
                    <button class="button--close" type="button">{{ __('show_task.close_win') }}</button>
                </div>
            </div>
        </section>
        <section class="pop-up pop-up--refusal pop-up--close">
            <div class="pop-up--wrapper">
                <h4>{{ __('show_task.refusal_task') }}</h4>
                <p class="pop-up-text">
                    {!! __('show_task.refusal_task_det') !!}
                </p>
                <form method="post" action="{{ route('tasks.refuse', $task->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="refuse">
                    <input type="submit" class="button button--pop-up button--orange" value="{{ __('show_task.refuse_button') }}">
                </form>
                <div class="button-container">
                    <button class="button--close" type="button">{{ __('show_task.close_win') }}</button>
                </div>
            </div>
        </section>
        <section class="pop-up pop-up--completion pop-up--close">
            <div class="pop-up--wrapper">
                <h4>{{ __('show_task.completing_task') }}</h4>
                <p class="pop-up-text">
                    {{ __('show_task.completing_task_det') }}
                </p>
                <div class="completion-form pop-up--form regular-form">
                    <form method="post" action="{{ route('tasks.complete', $task->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="complete">

                        <div class="form-group">
                            <label class="control-label" for="completion-comment" >{{ __('show_task.comment') }}</label>
                            <textarea id="completion-comment" name="comment">{{ old('comment') }}</textarea>
                        </div>
                        <p class="completion-head control-label">{{ __('show_task.rating') }}</p>
                        <div class="rating-area stars-rating big active-stars">
                            <input type="radio" id="star-5" name="rating" value="5">
                            <label for="star-5" title="Оценка «5»"></label>
                            <input type="radio" id="star-4" name="rating" value="4">
                            <label for="star-4" title="Оценка «4»"></label>
                            <input type="radio" id="star-3" name="rating" value="3">
                            <label for="star-3" title="Оценка «3»"></label>
                            <input type="radio" id="star-2" name="rating" value="2">
                            <label for="star-2" title="Оценка «2»"></label>
                            <input type="radio" id="star-1" name="rating" value="1">
                            <label for="star-1" title="Оценка «1»"></label>
                        </div>
                        <input type="submit" class="button button--pop-up button--blue" value="{{ __('show_task.complete_button') }}">
                    </form>
                </div>
                <div class="button-container">
                    <button class="button--close" type="button">{{ __('show_task.close_win') }}</button>
                </div>
            </div>
        </section>
        <section class="pop-up pop-up--act_response pop-up--close">
            <div class="pop-up--wrapper">
                <h4>{{ __('show_task.add_response') }}</h4>
                <p class="pop-up-text">
                    {{ __('show_task.add_response_det') }}
                </p>
                <div class="addition-form pop-up--form regular-form">
                    <form method="post" action="{{ route('tasks.respond', $task->id) }}">
                        @csrf
                        @method('PUT')
                        <input id="addition-price" type="hidden" name="action" value="respond">

                        <div class="form-group">
                            <label class="control-label" for="addition-comment">{{ __('show_task.comment') }}</label>
                            <textarea id="addition-comment" name="comment">{{ old('comment') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="addition-price">{{ __('show_task.budget') }}</label>
                            <input id="addition-price" type="text" name="budget" value="{{ old('budget') }}">
                            @error('budget')
                            <span class="is-invalid">{{ $message }}</span>
                            @else
                            <span class="help-block"></span>
                            @enderror
                        </div>
                        <input type="submit" class="button button--pop-up button--blue" value="{{ __('show_task.complete_button') }}">
                    </form>
                </div>
                <div class="button-container">
                    <button class="button--close" type="button">>{{ __('show_task.close_win') }}</button>
                </div>
            </div>
        </section>
        <div class="overlay"></div>
    </x-slot>

    <x-slot name="yandexJs">
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    </x-slot>

    <div class="left-column">
        <div class="head-wrapper">
            <h3 class="head-main">{{ $task->title }}</h3>
            @if($task->budget)
                <p class="price price--big">{{ $task->budget }} ₽</p>
            @endif
        </div>
        <p class="task-description">
            {{ $task->description }}
        </p>
        @if($task->status->name === \App\Models\Status::NEW)
            @if($user->isCustomer())
                <a href="#" class="button button--yellow action-btn" data-action="cancel">{{ __('show_task.cancel_task') }}</a>
            @else
                @if(! $user->wasRespondToTask($task->id))
                    <a href="#" class="button button--blue action-btn" data-action="act_response">{{ __('show_task.res_button') }}</a>
                @endif
            @endif
        @endif
        @if($task->status->name === \App\Models\Status::IN_PROGRESS)
            @if($user->isCustomer())
                <a href="#" class="button button--pink action-btn" data-action="completion">{{ __('show_task.done_button') }}</a>
            @else
                <a href="#" class="button button--orange action-btn" data-action="refusal">{{ __('show_task.ref_button') }}</a>
            @endif
        @endif

        @if($task->lat && $task->long)
            <div class="task-map">
                <div id="map" class="map" style="width: 725px; height: 346px"></div>
                <p class="map-address town">{{ $task->city->name }}</p>
                <p class="map-address">{{ $task->address }}</p>
            </div>
        @endif

        <h4 class="head-regular">{{ __('show_task.responses') }}</h4>
        @foreach($task->responses as $response)
            <div class="response-card">
                <img class="customer-photo" src="{{ $response->executor->avatar ? asset("storage/{$response->executor->id}/{$response->executor->avatar}") : Vite::asset('resources/img/avatars/def-avatar.jpg') }}" width="146" height="156" alt="Фото исполнителя">
                <div class="feedback-wrapper">
                    <a href="{{ route('profile.display', $response->executor->id) }}" class="link link--block link--big">{{ $response->executor->name }}</a>
                    <div class="response-wrapper">
                        <div class="stars-rating small">
                            <span class="{{ $response->executor->rating() >= 1 ? 'fill-star' : '' }}">&nbsp;</span>
                            <span class="{{ $response->executor->rating() >= 2 ? 'fill-star' : '' }}">&nbsp;</span>
                            <span class="{{ $response->executor->rating() >= 3 ? 'fill-star' : '' }}">&nbsp;</span>
                            <span class="{{ $response->executor->rating() >= 4 ? 'fill-star' : '' }}">&nbsp;</span>
                            <span class="{{ $response->executor->rating() >= 5 ? 'fill-star' : '' }}">&nbsp;</span>
                        </div>
                        @php
                            $cntFeedbacks = $response->executor->executorFeedbacks()->count();
                            $locale = Illuminate\Support\Facades\App::getlocale();
                            $localeRu = 'ru';

                            $oneFeedback = $locale == $localeRu ? 'отзыв' : 'feedback';
                            $twoFeedbacks = $locale == $localeRu ? 'отзыва' : 'feedbacks';
                            $manyFeedbacks = $locale == $localeRu ? 'отзывов' : 'feedbacks';
                        @endphp
                        <p class="reviews">{{ $cntFeedbacks }} {{ \App\Helpers::getNounPluralForm($cntFeedbacks, $oneFeedback, $twoFeedbacks, $manyFeedbacks) }}</p>
                    </div>
                    @if($response->comment)
                        <p class="response-message">
                            {{ $response->comment }}
                        </p>
                    @endif
                </div>
                <div class="feedback-wrapper">
                    @php
                        list($numberAgo, $pluralForm) = \App\Helpers::getNounPluralDateForm($response->created_at);
                    @endphp
                    <p class="info-text"><span class="current-time">{{ $numberAgo }} {{ $pluralForm }} {{ __('show_task.ago') }}</span></p>
                    @if($response->budget)
                        <p class="price price--small">{{ $response->budget }} ₽</p>
                    @endif
                </div>
                @if($user->isCustomer() && $response->active && !$task->executor_id)
                    <div class="button-popup">
                        <a href="{{ route('responses.accept', $response->id) }}" class="button button--blue button--small">{{ __('show_task.acc') }}</a>
                        <a href="{{ route('responses.reject', $response->id) }}" class="button button--orange button--small">{{ __('show_task.deny') }}</a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="right-column">
        <div class="right-card black info-card">
            <h4 class="head-card">{{ __('show_task.ab_task') }}</h4>
            <dl class="black-list">
                <dt>{{ __('show_task.cat') }}</dt>
                <dd>{{ __("categories.{$task->category->name}") }}</dd>
                <dt>{{ __('show_task.pub') }}</dt>
                @php
                    list($numberAgo, $pluralForm) = \App\Helpers::getNounPluralDateForm(($task->created_at));
                @endphp
                <dd>{{ $numberAgo }} {{ $pluralForm }} {{ __('show_task.ago') }}</dd>
                <dt>{{ __('show_task.ded') }}</dt>
                @php
                    $locale = config('app.locale');
                @endphp
                @if($task->deadline)
                    <dd>{{ \Carbon\Carbon::parse($task->deadline)->locale($locale)->translatedFormat('d F, H:i') }}</dd>
                @else
                    <dd>{{ __('show_task.without_ded') }}</dd>
                @endif
                <dt>{{ __('show_task.st') }}</dt>
                <dd>
                    @if($task->status->name == \App\Models\Status::NEW)
                        {{ __('show_task.st_new') }}
                    @elseif($task->status->name == \App\Models\Status::IN_PROGRESS)
                        {{ __('show_task.st_pr') }}
                    @elseif($task->status->name == \App\Models\Status::CANCELED)
                        {{ __('show_task.st_cancel') }}
                    @elseif($task->status->name == \App\Models\Status::DONE)
                        {{ __('show_task.st_done') }}
                    @else
                        {{ __('show_task.st_fail') }}
                    @endif
                </dd>
            </dl>
        </div>
        <div class="right-card white file-card">
            <h4 class="head-card">{{ __('show_task.files') }}</h4>
            <ul class="enumeration-list">
                @foreach($task->files as $file)
                    <li class="enumeration-item">
                        <a href="{{ Storage::url("tasks/{$task->id}/{$file->path}") }}" class="link link--block link--clip" download="">{{ $file->path }}</a>
                        @if($file->size)
                            <p class="file-size">{{ round($file->size / 1_000_000, 2) }} Mб</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @if($task->lat && $task->long)
        <script type="text/javascript">
            ymaps.ready(init);

            function init() {
                var myMap = new ymaps.Map("map", {
                    center: [{{ $task->lat }}, {{ $task->long }}],
                    zoom: 12,
                    controls: [
                        'zoomControl',
                        'rulerControl',
                        'routeButtonControl',
                        'trafficControl',
                        'typeSelector',
                        'fullscreenControl',
                        new ymaps.control.SearchControl({
                            options: {
                                size: 'large',
                                provider: 'yandex#search'
                            }
                        })
                    ]
                });
                var myPlacemark = new ymaps.Placemark([{{ $task->lat }}, {{ $task->long }}], {});
                myMap.geoObjects.add(myPlacemark);
            }
        </script>
    @endif
</x-layout.layout>
