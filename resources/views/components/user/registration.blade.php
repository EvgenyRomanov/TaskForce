<x-layout.layout>
    <x-slot name="title">
        {{ __('registration.title') }}
    </x-slot>

    <x-slot name="subclass">
        container--registration
    </x-slot>

    <div class="center-block">
        <div class="registration-form regular-form">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h3 class="head-main head-task">{{ __('registration.reg_new_user') }}</h3>
                <div class="form-group">
                    <label class="control-label" for="username">{{ __('registration.your_name') }}</label>
                    <input id="username" type="text" name="name" value="{{ old('name') }}">
                    @error('name')
                    <span class="is-invalid">{{ $message }}</span>
                    @else
                    <span class="help-block"></span>
                    @enderror
                </div>
                <div class="half-wrapper">
                    <div class="form-group">
                        <label class="control-label" for="email-user">Email</label>
                        <input id="email-user" type="email" name="email" value="{{ old('email') }}">
                        @error('email')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                        <span class="help-block"></span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="town-user">{{ __('registration.city') }}</label>
                        <select id="town-user" name="town">
                            @foreach ($cities as $city)
                                <option value="{{ $city->name }}" @selected(old('town') == $city->name)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('town')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                        <span class="help-block"></span>
                        @enderror
                    </div>
                </div>
                <div class="half-wrapper">
                    <div class="form-group">
                        <label class="control-label" for="password-user">{{ __('registration.pass') }}</label>
                        <input id="password-user" type="password" name="password">
                        @error('password')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                        <span class="help-block"></span>
                        @enderror
                    </div>
                </div>
                <div class="half-wrapper">

                    <div class="form-group">
                        <label class="control-label" for="password-repeat-user">{{ __('registration.rep_pass') }}</label>
                        <input id="password-repeat-user" type="password" name="password_confirmation">
                        @error('password_confirmation')
                        <span class="is-invalid">{{ $message }}</span>
                        @else
                        <span class="help-block"></span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label checkbox-label" for="response-user">
                        <input id="response-user" type="checkbox" name="role" value="{{ $role }}" @checked(old('role', $role))>
                        {{ __('registration.executor') }}</label>
                </div>
                <input type="submit" class="button button--blue" value="{{ __('registration.cr_acc') }}">
            </form>
        </div>
    </div>
</x-layout.layout>

