<x-layout.layout>
    <x-slot name="title">
        {{ __('login.title') }}
    </x-slot>

    <x-slot name="subclass">
        main-content main-content--center
    </x-slot>

    <div class="center-block">
        <div class="registration-form regular-form">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <p>
                    <label class="control-label" for="enter-email">Email</label>
                    <input class="enter-form-email input input-middle" type="email" name="email" id="enter-email" value="{{ old('email') }}">
                    @error('email')
                    <span class="is-invalid">{{ $message }}</span>
                    @enderror
                </p>
                <p>
                    <label class="control-label" for="enter-password">{{ __('login.pass') }}</label>
                    <input class="enter-form-email input input-middle" type="password" name="password" id="enter-password">
                </p>
                <p>
                    <label class="control-label" for="enter-remember">{{ __('login.rem') }}</label>
                    <input class="enter-form-email input input-middle" type="checkbox" name="remember" id="enter-remember">
                </p>
                <input type="submit" class="button button--blue" value="{{ __('login.login') }}">
            </form>
        </div>
    </div>
</x-layout.layout>

