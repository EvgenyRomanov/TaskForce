<x-layout.layout :user="$user">
    <x-slot name="title">
        {{ __('create_task.title') }}
    </x-slot>

    <x-slot name="activeLink">
        create
    </x-slot>

    <x-slot name="subclass">
        main-content main-content--center
    </x-slot>

    <div class="add-task-form regular-form">
        <form method="post" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
            @csrf

            <h3 class="head-main head-main">{{ __('create_task.publish_task') }}</h3>
            <div class="form-group">
                <label class="control-label" for="essence-work">{{ __('create_task.form_title') }}</label>
                <input id="essence-work" type="text" name="title" value="{{ old('title') }}">
                @error('title')
                <span class="is-invalid">{{ $message }}</span>
                @else
                <span class="help-block"></span>
                @enderror
            </div>
            <div class="form-group">
                <label class="control-label" for="username">{{ __('create_task.form_desc') }}</label>
                <textarea id="username" name="description">{{ old('description') }}</textarea>
                @error('description')
                <span class="is-invalid">{{ $message }}</span>
                @else
                <span class="help-block"></span>
                @enderror
            </div>
            <div class="form-group">
                <label class="control-label" for="town-user">{{ __('create_task.form_cat') }}</label>
                <select id="town-user" name="category">
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" @selected(old('category') == $category->name)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category')
                <span class="is-invalid">{{ $message }}</span>
                @else
                <span class="help-block"></span>
                @enderror
            </div>
            <div class="form-group">
                <label class="control-label" for="location">{{ __('create_task.form_loc') }}</label>
                <input class="location-icon" id="location" type="text" name="location" value="{{ old('location') }}">
                @error('location')
                <span class="is-invalid">{{ $message }}</span>
                @else
                <span class="help-block"></span>
                @enderror
            </div>
            <div class="half-wrapper">
                <div class="form-group">
                    <label class="control-label" for="budget">{{ __('create_task.form_bg') }}</label>
                    <input class="budget-icon" id="budget" type="text" name="budget" value="{{ old('budget') }}">
                    @error('budget')
                    <span class="is-invalid">{{ $message }}</span>
                    @else
                    <span class="help-block"></span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="control-label" for="period-execution">{{ __('create_task.form_ded') }}</label>
                    <input id="period-execution" type="date" name="deadline" value="{{ old('deadline') }}">
                    @error('deadline')
                    <span class="is-invalid">{{ $message }}</span>
                    @else
                    <span class="help-block"></span>
                    @enderror
                </div>
            </div>
            <p class="form-label">{{ __('create_task.form_files') }}</p>
            <label for="file-upload" class="new-file">
                {{ __('create_task.form_add_f') }}
                <input id="file-upload" type="file" name="files[]" multiple style="display:none;">
            </label>
            @error('files')
            <span class="is-invalid">{{ $message }}</span>
            @else
            <span class="help-block"></span>
            @enderror
            <input type="submit" class="button button--blue" value="{{ __('create_task.form_submit') }}">
        </form>
    </div>
</x-layout.layout>

