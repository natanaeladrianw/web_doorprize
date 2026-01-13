@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
        <p class="text-gray-600 mt-1">Kelola pengaturan tampilan dan konfigurasi sistem</p>
    </div>

    <!-- Pengaturan Tampilan Undian -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gift text-2xl text-purple-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Tampilan Kartu Undian</h2>
                    <p class="text-sm text-gray-500">Atur field yang ditampilkan pada kartu undian saat mengundi</p>
                </div>
            </div>
        </div>

        <div class="p-6" x-data="settingsForm()">
            <!-- Form Selector -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-file-alt text-blue-500 mr-1"></i>
                    Pilih Form Terlebih Dahulu
                </label>
                <select x-model="selectedFormId" @change="loadFormFields()"
                    class="w-full max-w-md border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700">
                    <option value="">-- Pilih Form --</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}" 
                            {{ $selectedFormId == $form->id ? 'selected' : '' }}>
                            {{ $form->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Placeholder when no form selected -->
            <div x-show="!selectedFormId" class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                <i class="fas fa-hand-pointer text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-500">Silakan Pilih Form</h3>
                <p class="text-gray-400 mt-1">Pilih form di atas untuk mengatur tampilan kartu undian</p>
            </div>

            <!-- Form Settings (show when form selected) -->
            <form action="{{ route('admin.settings.raffle-display') }}" method="POST" x-show="selectedFormId" x-cloak>
                @csrf
                <input type="hidden" name="form_id" x-bind:value="selectedFormId">

                <!-- Field Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-list-check text-indigo-500 mr-1"></i>
                        Pilih Field yang Ditampilkan
                    </label>
                    <p class="text-sm text-gray-500 mb-4">
                        Centang field yang ingin ditampilkan pada kartu undian.
                    </p>

                    @foreach($forms as $form)
                        <div x-show="selectedFormId == {{ $form->id }}" 
                            class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <h4 class="font-medium text-gray-700">
                                    <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                                    Field dari: {{ $form->title }}
                                </h4>
                            </div>
                            <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($form->fields as $field)
                                    @php
                                        $formSettings = $allSettings[$form->id] ?? [];
                                        $isChecked = in_array($field->label, $formSettings);
                                    @endphp
                                    <label class="flex items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition cursor-pointer">
                                        <input type="checkbox" 
                                            name="display_fields[]" 
                                            value="{{ $field->label }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ $field->label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Separator -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-minus text-indigo-500 mr-1"></i>
                        Pemisah Antar Field
                    </label>
                    <p class="text-sm text-gray-500 mb-3">
                        Karakter untuk memisahkan antar field. Contoh: " - " menghasilkan "Nama - Jabatan"
                    </p>
                    <input type="text" 
                        name="display_separator" 
                        value="{{ $raffleDisplaySeparator }}"
                        class="w-full max-w-xs border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder=" - ">
                </div>

                <!-- Preview -->
                <div class="mb-6 p-4 bg-gradient-to-r from-purple-100 to-indigo-100 rounded-xl">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-eye text-purple-500 mr-1"></i>
                        Contoh Tampilan
                    </label>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <p class="text-2xl font-bold text-purple-600 text-center">
                            Nathanael Adrian Wirawan - Manager
                        </p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        Jika dipilih "Nama Lengkap" dan "Jabatan" dengan pemisah " - "
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function settingsForm() {
            return {
                selectedFormId: '{{ $selectedFormId ?? "" }}',
                
                loadFormFields() {
                    // Form fields are already loaded via Blade
                    // This method can be used for additional AJAX if needed
                }
            }
        }
    </script>
@endsection
