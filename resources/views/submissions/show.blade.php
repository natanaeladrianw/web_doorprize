@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('forms.index', ['page' => $returnPage ?? 1]) }}" class="text-sm font-medium mb-4 inline-flex items-center bg-[#19183B] hover:bg-[#19183B]/80 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
            </svg>
            <span>Kembali ke Daftar Form</span>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $form->title }}</h1>
        @if($form->description)
        <p class="mt-2 text-gray-600">{{ $form->description }}</p>
        @endif
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <form action="{{ route('forms.submit', $form) }}" method="POST">
            @csrf
            <input type="hidden" name="return_page" value="{{ $returnPage ?? 1 }}">

            @foreach($form->fields as $index => $field)
            <div class="mb-6 {{ $index === 0 ? '' : 'pt-6 border-t border-gray-100' }}">
                <!-- Field Label -->
                <label class="block text-sm font-medium text-gray-900 mb-2">
                    {{ $field->label }}
                    @if($field->is_required)
                    <span class="text-red-500">*</span>
                    @endif
                </label>

                @php
                $fieldName = 'field_' . $field->id;
                $hasError = $errors->has($fieldName);
                $oldValue = old($fieldName);
                @endphp

                <!-- Text Input -->
                @if($field->field_type === 'text')
                @php
                    $options = $field->options ?? [];
                    $minLength = $options['min_length'] ?? null;
                    $maxLength = $options['max_length'] ?? null;
                    $needsValidation = $options['needs_validation'] ?? false;
                @endphp
                <input
                    type="text"
                    name="{{ $fieldName }}"
                    value="{{ $oldValue }}"
                    class="w-full px-4 py-2 border {{ $hasError ? 'border-red-300' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    {{ $field->is_required ? 'required' : '' }}
                    @if($minLength) minlength="{{ $minLength }}" @endif
                    @if($maxLength) maxlength="{{ $maxLength }}" @endif
                    placeholder="{{ $needsValidation ? 'Masukkan ' . $field->label . ' Anda' : '' }}">
                @if($minLength || $maxLength || $needsValidation)
                <p class="mt-1 text-xs text-gray-500">
                    @if($minLength && $maxLength)
                        Harus {{ $minLength }} - {{ $maxLength }} karakter.
                    @elseif($minLength)
                        Minimal {{ $minLength }} karakter.
                    @elseif($maxLength)
                        Maksimal {{ $maxLength }} karakter.
                    @endif
                    @if($needsValidation)
                        <span class="text-amber-600 font-medium"><i class="fas fa-shield-alt text-xs"></i> Field ini digunakan untuk validasi.</span>
                    @endif
                </p>
                @endif
                @endif

                <!-- Radio Buttons -->
                @if($field->field_type === 'radio' && $field->options)
                <div class="space-y-2">
                    @foreach($field->options as $option)
                    <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">
                        <input
                            type="radio"
                            name="{{ $fieldName }}"
                            value="{{ $option }}"
                            {{ $oldValue == $option ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                            {{ $field->is_required ? 'required' : '' }}>
                        <span class="ml-3 text-gray-700">{{ $option }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                <!-- Checkboxes -->
                @if($field->field_type === 'checkbox' && $field->options)
                <div class="space-y-2">
                    @foreach($field->options as $option)
                    <label class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">
                        <input
                            type="checkbox"
                            name="{{ $fieldName }}[]"
                            value="{{ $option }}"
                            {{ is_array($oldValue) && in_array($option, $oldValue) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-gray-700">{{ $option }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                <!-- Dropdown -->
                @if($field->field_type === 'dropdown' && $field->options)
                <select
                    name="{{ $fieldName }}"
                    class="w-full px-4 py-2 border {{ $hasError ? 'border-red-300' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    {{ $field->is_required ? 'required' : '' }}>
                    <option value="">-- Pilih {{ $field->label }} --</option>
                    @foreach($field->options as $option)
                    <option value="{{ $option }}" {{ $oldValue == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @endif

                <!-- Error Message -->
                @error($fieldName)
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="w-full bg-[#19183B] hover:bg-[#19183B]/80 text-white font-semibold py-3 px-6 rounded-md transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Kirim Form
                </button>
                <p class="mt-3 text-xs text-center text-gray-500">
                    Dengan mengirim form ini, Anda setuju untuk berpartisipasi dalam undian doorprize
                </p>
            </div>
        </form>
    </div>
</div>
@endsection