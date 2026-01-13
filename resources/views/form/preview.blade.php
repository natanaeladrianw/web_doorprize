{{-- Preview form untuk modal admin --}}
<div class="space-y-6">
    {{-- Preview Note - Di atas --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-yellow-800">Mode Pratinjau</h4>
                <p class="text-xs text-yellow-700 mt-1">Ini adalah pratinjau tampilan form yang akan dilihat oleh pengguna. Semua field dinonaktifkan dalam mode pratinjau.</p>
            </div>
        </div>
    </div>

    {{-- Form Header --}}
    <div class="border-b pb-4">
        <h3 class="text-xl font-bold text-gray-900">{{ $form->title }}</h3>
        @if($form->description)
            <p class="mt-2 text-gray-600 text-sm">{{ $form->description }}</p>
        @endif
        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $form->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $form->is_active ? 'Aktif' : 'Tidak Aktif' }}
            </span>
            <span>{{ $form->fields->count() }} field</span>
            <span>Dibuat: {{ $form->created_at->format('d M Y') }}</span>
        </div>
    </div>

    {{-- Form Fields Preview --}}
    @if($form->fields->count() > 0)
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Field Formulir</h4>

            @foreach($form->fields as $index => $field)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                {{ $index + 1 }}. {{ $field->label }}
                                @if($field->is_required)
                                    <span class="text-red-500 ml-1">*</span>
                                @endif
                            </label>

                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mb-3">
                                {{ ucfirst($field->field_type) }}
                            </span>

                            {{-- Text Input Preview --}}
                            @if($field->field_type === 'text')
                                <input type="text" disabled placeholder="Text input..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-400 cursor-not-allowed">
                            @endif

                            {{-- Radio Buttons Preview --}}
                            @if($field->field_type === 'radio' && $field->options)
                                <div class="space-y-2">
                                    @foreach($field->options as $option)
                                        <label
                                            class="flex items-center p-2 border border-gray-200 rounded-md bg-white cursor-not-allowed">
                                            <input type="radio" disabled class="w-4 h-4 text-indigo-600">
                                            <span class="ml-2 text-gray-600 text-sm">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Checkboxes Preview --}}
                            @if($field->field_type === 'checkbox' && $field->options)
                                <div class="space-y-2">
                                    @foreach($field->options as $option)
                                        <label
                                            class="flex items-center p-2 border border-gray-200 rounded-md bg-white cursor-not-allowed">
                                            <input type="checkbox" disabled class="w-4 h-4 text-indigo-600 rounded">
                                            <span class="ml-2 text-gray-600 text-sm">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Dropdown Preview --}}
                            @if($field->field_type === 'dropdown' && $field->options)
                                <select disabled
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-400 cursor-not-allowed">
                                    <option>-- Pilih {{ $field->label }} --</option>
                                    @foreach($field->options as $option)
                                        <option>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <p class="mt-2">Belum ada field yang ditambahkan ke formulir ini.</p>
            <a href="{{ route('admin.forms.builder', $form) }}"
                class="mt-3 inline-block text-blue-600 hover:underline text-sm">
                Tambah field menggunakan Form Builder →
            </a>
        </div>
    @endif
</div>