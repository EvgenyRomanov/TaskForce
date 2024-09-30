<x-layout.layout :user="$user">
    <x-slot name="title">
        {{ __('edit_profile.title') }}
    </x-slot>

    <x-slot name="subclass">
        main-content main-content--left
    </x-slot>

    <x-slot name="activeLink">
        settings
    </x-slot>

    <x-slot name="mainJs">
        @vite('resources/js/app.js')
    </x-slot>

    <div class="left-menu left-menu--edit">
        <h3 class="head-main head-task">{{ __('edit_profile.set') }}</h3>
        <ul class="side-menu-list">
            <li class="side-menu-item side-menu-item--active">
                <a href="{{ route('profile.edit') }}" class="link link--nav">{{ __('edit_profile.my') }}</a>
            </li>
            <li class="side-menu-item">
                <a class="link link--nav">{{ __('edit_profile.sec') }}</a>
            </li>
        </ul>
    </div>
    <div class="my-profile-form">
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <h3 class="head-main head-regular">{{ __('edit_profile.my') }}</h3>
                <div class="photo-editing">
                    <div>
                        <p class="form-label">{{ __('edit_profile.form_ava') }}</p>
                        <img class="avatar-preview" src="{{ $user->avatar ? asset("storage/{$user->id}/{$user->avatar}") : Vite::asset('resources/img/avatars/def-avatar.jpg') }}" width="83" height="83" alt="Аватар">
                    </div>
                    <input hidden value="{{ old('avatar', $user->avatar) }}" type="file" id="button-input" name="avatar" >
                    <label for="button-input" class="button button--black">{{ __('edit_profile.form_ava_ch') }}</label>
                </div>
                <div class="form-group">
                    <label class="control-label" for="profile-name">{{ __('edit_profile.form_name') }}</label>
                    <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}">
                    @error('name')
                    <span class="is-invalid">{{ $message }}</span>
                    @else
                    <span class="help-block"></span>
                    @enderror
                </div>
                <div class="half-wrapper">
                    <div class="form-group">
                        <label class="control-label" for="profile-email">Email</label>
                        <input readonly id="profile-email" type="email" value="{{ old('email', $user->email) }}">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="profile-date">{{ __('edit_profile.form_bd') }}</label>
                        <input id="profile-date" type="date" name="birth_date" value="{{ $user->birth_date ? old('birth_date', date('Y-m-d', strtotime($user->birth_date))) : '' }}">
                        @error('birth_date')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                            <span class="help-block"></span>
                            @enderror
                    </div>
                </div>
                <div class="half-wrapper">
                    <div class="form-group">
                        <label class="control-label" for="profile-phone">{{ __('edit_profile.form_tel') }}</label>
                        <input id="profile-phone" type="tel" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                        <span class="help-block"></span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="profile-tg">Telegram</label>
                        <input id="profile-tg" type="text" name="telegram" value="{{ old('telegram', $user->telegram) }}">
                        @error('telegram')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                            <span class="help-block"></span>
                            @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="profile-info">{{ __('edit_profile.form_about') }}</label>
                    <textarea id="profile-info" name="about">{{ old('about', $user->about) }}</textarea>
                    @error('about')
                    <span class="is-invalid">{{ $message }}</span>
                    @else
                        <span class="help-block"></span>
                        @enderror
                </div>
                <div class="form-group">
                    @if($user->isExecutor())
                        <p class="form-label">{{ __('edit_profile.form_cat') }}</p>
                        <div class="checkbox-profile">
                            @foreach($categories as $category)
                                <label class="control-label" for="services-{{ $category->id }}">
                                    <input type="checkbox" id="services-{{ $category->id }}" @checked( in_array($category->id, $user->categoryIds())) name="categories[]" value="{{ $category->id }}">
                                    {{ __("categories.{$category->name}") }}</label>
                            @endforeach
                        </div>
                    @endif
                </div>
                <input type="submit" class="button button--blue" value="{{ __('edit_profile.form_sub') }}">
            </form>
    </div>
</x-layout.layout>
