<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-emerald-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-teal-600 px-8 py-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">{{ $form->title }}</h1>
                @if ($form->description)
                    <p class="text-blue-50 text-lg leading-relaxed">{{ $form->description }}</p>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-6 shadow-sm">
                <div class="flex items-start">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
            <form action="{{ route('form.submit') }}" method="POST" id="doorprizeForm" class="space-y-8">
                @csrf

                @foreach ($form->fields as $field)
                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">
                            {{ $field->label }}
                            @if ($field->is_required)
                                <span class="text-red-500 ml-1">*</span>
                            @endif
                        </label>

                        @switch($field->field_type)
                            @case('text')
                                <input type="text" name="field_{{ $field->id }}" id="field_{{ $field->id }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-gray-900 placeholder-gray-400"
                                    placeholder="Masukkan {{ strtolower($field->label) }}"
                                    value="{{ old('field_' . $field->id) }}" {{ $field->is_required ? 'required' : '' }}>
                            @break

                            @case('radio')
                                @if ($field->options)
                                    <div class="space-y-3">
                                        @foreach ($field->options as $option)
                                            <label class="flex items-center group cursor-pointer">
                                                <input type="radio" name="field_{{ $field->id }}"
                                                    value="{{ $option }}"
                                                    class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                                    {{ old('field_' . $field->id) == $option ? 'checked' : '' }}
                                                    {{ $field->is_required ? 'required' : '' }}>
                                                <span
                                                    class="ml-3 text-gray-700 group-hover:text-gray-900 transition">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            @break

                            @case('checkbox')
                                @if ($field->options)
                                    <div class="space-y-3">
                                        @foreach ($field->options as $option)
                                            <label class="flex items-center group cursor-pointer">
                                                <input type="checkbox" name="field_{{ $field->id }}[]"
                                                    value="{{ $option }}"
                                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                                    {{ is_array(old('field_' . $field->id)) && in_array($option, old('field_' . $field->id)) ? 'checked' : '' }}>
                                                <span
                                                    class="ml-3 text-gray-700 group-hover:text-gray-900 transition">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            @break

                            @case('dropdown')
                                @if ($field->options)
                                    <select name="field_{{ $field->id }}" id="field_{{ $field->id }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-gray-900 bg-white cursor-pointer"
                                        {{ $field->is_required ? 'required' : '' }}>
                                        <option value="" class="text-gray-400">-- Pilih {{ strtolower($field->label) }}
                                            --</option>
                                        @foreach ($field->options as $option)
                                            <option value="{{ $option }}"
                                                {{ old('field_' . $field->id) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            @break
                        @endswitch

                        <span class="text-red-600 text-sm font-medium error-message hidden mt-2 block"
                            id="error_field_{{ $field->id }}"></span>
                    </div>
                @endforeach

                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-teal-600 hover:from-blue-700 hover:to-teal-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 text-lg">
                        Kirim Formulir
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
