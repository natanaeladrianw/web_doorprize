@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Form Builder: {{ $form->title }}</h1>
        <a href="{{ route('admin.forms.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="fieldBuilder()">
        <!-- List Field -->
        <div class="md:col-span-2 space-y-2">
            <!-- Info Drag Drop -->
            <div
                class="flex items-center gap-2 text-sm text-gray-500 mb-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                <span>Seret dan lepas field untuk mengubah urutan.</span>
            </div>

            <!-- Sortable Container -->
            <div id="sortable-fields" class="space-y-2">
                @foreach ($form->fields as $field)
                    <div class="field-item bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-indigo-300 transition-all duration-200 cursor-grab active:cursor-grabbing flex justify-between items-center group"
                        data-id="{{ $field->id }}">
                        <div class="flex items-center gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <i class="fas fa-grip-vertical text-lg"></i>
                            </div>

                            <div>
                                <h3 class="font-bold text-lg text-gray-800">{{ $field->label }}
                                    <span
                                        class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full ml-1">({{ $field->field_type }})</span>
                                    @if ($field->is_required)
                                        <span class="text-red-500 text-xs font-medium ml-1">*Required</span>
                                    @endif
                                </h3>
                                @if (in_array($field->field_type, ['radio', 'checkbox', 'dropdown']))
                                    <div class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-list-ul text-xs mr-1"></i>
                                        Options: {{ implode(', ', $field->options ?? []) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Edit Field Button -->
                            <button type="button" @click="editField({ 
                                                                                id: {{ $field->id }}, 
                                                                                label: '{{ addslashes($field->label) }}', 
                                                                                field_type: '{{ $field->field_type }}', 
                                                                                options: {{ json_encode($field->options ?? []) }}, 
                                                                                is_required: {{ $field->is_required ? 'true' : 'false' }} 
                                                                            })"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>

                            <!-- Delete Field -->
                            <div x-data="{ showDeleteModal: false }" class="inline">
                                <button type="button" @click="showDeleteModal = true"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1 rounded hover:bg-red-50 transition">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>

                                <!-- Delete Confirmation Modal -->
                                <div x-show="showDeleteModal" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                    style="background-color: rgba(0,0,0,0.5);">
                                    <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl"
                                        @click.away="showDeleteModal = false">
                                        <div class="p-6 text-center">
                                            <!-- Warning Icon -->
                                            <div
                                                class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                                            </div>

                                            <!-- Title -->
                                            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Field?</h3>

                                            <!-- Message -->
                                            <p class="text-gray-600 mb-6">
                                                Apakah Anda yakin ingin menghapus field "<strong>{{ $field->label }}</strong>"?
                                                Tindakan ini tidak dapat dibatalkan.
                                            </p>

                                            <!-- Buttons -->
                                            <div class="flex gap-3">
                                                <button type="button" @click="showDeleteModal = false"
                                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition">
                                                    Batal
                                                </button>
                                                <form action="{{ route('admin.forms.fields.destroy', [$form, $field]) }}"
                                                    method="POST" class="flex-1">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                                                        Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($form->fields->isEmpty())
                <div
                    class="text-gray-500 text-center py-12 bg-white rounded-lg shadow-sm border-2 border-dashed border-gray-300">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p>Belum ada field. Tambahkan field baru di sebelah kanan.</p>
                </div>
            @endif

            <!-- Toast Notification -->
            <div id="toast-notification"
                class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center gap-2 z-50">
                <i class="fas fa-check-circle"></i>
                <span id="toast-message">Urutan berhasil diperbarui!</span>
            </div>
        </div>

        <!-- Add/Edit Field Form -->
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 top-4 sticky">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2"
                    x-text="isEditMode ? '✏️ Edit Field' : '➕ Add New Field'"></h2>

                <form :action="formAction" method="POST">
                    @csrf
                    <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Label</label>
                        <input type="text" name="label"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            required x-model="formData.label" placeholder="Masukkan label field...">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Type</label>
                        <select name="field_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            x-model="formData.field_type">
                            <option value="text">Text Input</option>
                            <option value="radio">Radio Button</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="dropdown">Dropdown</option>
                        </select>
                    </div>

                    <div class="mb-4" x-show="['radio', 'checkbox', 'dropdown'].includes(formData.field_type)" x-transition>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Options</label>
                        <template x-for="(option, index) in formData.options" :key="index">
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="options[]"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="Option value" x-model="formData.options[index]">
                                <button type="button" @click="removeOption(index)"
                                    class="text-red-500 hover:text-red-700 px-2 transition">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="addOption()"
                            class="text-sm text-blue-600 hover:text-blue-800 hover:underline transition">
                            <i class="fas fa-plus mr-1"></i>Add Option
                        </button>
                    </div>

                    <!-- Opsi Batasan Input (hanya untuk Text Input) -->
                    <div class="mb-4 space-y-3" x-show="formData.field_type === 'text'" x-transition>
                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-ruler-horizontal mr-1 text-indigo-500"></i>Batasan Input
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-500 text-xs mb-1">Min Karakter</label>
                                    <input type="number" name="min_length" min="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        placeholder="0" x-model="formData.min_length">
                                </div>
                                <div>
                                    <label class="block text-gray-500 text-xs mb-1">Max Karakter</label>
                                    <input type="number" name="max_length" min="1"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        placeholder="255" x-model="formData.max_length">
                                </div>
                            </div>
                        </div>

                        <!-- Perlu Validasi (Cek Duplikat Kombinasi) -->
                        <div class="border border-amber-200 rounded-lg p-3 bg-amber-50">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="needs_validation" value="1"
                                    class="mr-2 mt-0.5 w-4 h-4 rounded text-amber-600 focus:ring-amber-500 transition"
                                    x-model="formData.needs_validation">
                                <div>
                                    <span class="text-gray-700 text-sm font-medium">
                                        <i class="fas fa-shield-alt text-amber-500 mr-1"></i>Validasi Kombinasi
                                    </span>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Form ditolak jika <strong>SEMUA</strong> field yang dicentang nilainya sama dengan
                                        submission yang sudah ada.
                                    </p>
                                    <p class="text-xs text-amber-600 mt-1">
                                        <i class="fas fa-lightbulb mr-1"></i>Contoh: Centang NIP dan Nama, maka form ditolak
                                        jika NIP <strong>DAN</strong> Nama sama persis.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Validasi Unik (Field Harus Unik) -->
                        <div class="border border-purple-200 rounded-lg p-3 bg-purple-50">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="is_unique" value="1"
                                    class="mr-2 mt-0.5 w-4 h-4 rounded text-purple-600 focus:ring-purple-500 transition"
                                    x-model="formData.is_unique">
                                <div>
                                    <span class="text-gray-700 text-sm font-medium">
                                        <i class="fas fa-fingerprint text-purple-500 mr-1"></i>Nilai Harus Unik
                                    </span>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Form ditolak jika nilai field ini <strong>SUDAH ADA</strong> di submission lain
                                        (terlepas dari field lainnya).
                                    </p>
                                    <p class="text-xs text-purple-600 mt-1">
                                        <i class="fas fa-lightbulb mr-1"></i>Contoh: Jika NIP dicentang, maka NIP yang sama
                                        tidak bisa daftar lagi meskipun Nama berbeda.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_required" value="1"
                                class="mr-2 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 transition"
                                x-model="formData.is_required">
                            <span class="text-gray-700 text-sm">Required?</span>
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                            x-text="isEditMode ? 'Update Field' : 'Add Field'"></button>
                        <button type="button" x-show="isEditMode" @click="resetForm()"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toast notification function
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.className = toast.className.replace('bg-green-500', isSuccess ? 'bg-green-500' : 'bg-red-500');
            toast.className = toast.className.replace('bg-red-500', isSuccess ? 'bg-green-500' : 'bg-red-500');

            // Show toast
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }

        // Initialize Sortable
        document.addEventListener('DOMContentLoaded', function () {
            const sortableContainer = document.getElementById('sortable-fields');

            if (sortableContainer && sortableContainer.children.length > 0) {
                new Sortable(sortableContainer, {
                    animation: 200,
                    handle: '.field-item',
                    ghostClass: 'bg-indigo-100',
                    chosenClass: 'shadow-lg',
                    dragClass: 'opacity-75',
                    easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                    onEnd: function (evt) {
                        // Get new order
                        const items = sortableContainer.querySelectorAll('.field-item');
                        const fieldIds = Array.from(items).map(item => parseInt(item.dataset.id));

                        // Send to server
                        fetch('{{ route('admin.forms.fields.reorder', $form) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ field_ids: fieldIds })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showToast('Urutan field berhasil diperbarui!', true);
                                } else {
                                    showToast('Gagal memperbarui urutan.', false);
                                }
                            })
                            .catch(error => {
                                showToast('Terjadi kesalahan.', false);
                            });
                    }
                });
            }
        });

        function fieldBuilder() {
            return {
                isEditMode: false,
                baseAction: "{{ route('admin.forms.fields.store', $form) }}",
                formAction: "{{ route('admin.forms.fields.store', $form) }}",
                formData: {
                    label: '',
                    field_type: 'text',
                    options: [''],
                    is_required: false,
                    min_length: '',
                    max_length: '',
                    needs_validation: false,
                    is_unique: false
                },

                init() {
                    this.resetForm();
                },

                addOption() {
                    this.formData.options.push('');
                },

                removeOption(index) {
                    this.formData.options.splice(index, 1);
                },

                editField(field) {
                    this.isEditMode = true;
                    // Extract constraints from options if they exist
                    const opts = field.options || {};
                    const optionsArray = Array.isArray(opts) ? opts : [];

                    this.formData = {
                        label: field.label,
                        field_type: field.field_type,
                        options: optionsArray.length > 0 ? optionsArray : [''],
                        is_required: field.is_required,
                        min_length: opts.min_length || '',
                        max_length: opts.max_length || '',
                        needs_validation: opts.needs_validation || false,
                        is_unique: opts.is_unique || false
                    };
                    let baseUrl = "{{ route('admin.forms.fields.update', [$form, ':id']) }}";
                    this.formAction = baseUrl.replace(':id', field.id);
                },

                resetForm() {
                    this.isEditMode = false;
                    this.formAction = this.baseAction;
                    this.formData = {
                        label: '',
                        field_type: 'text',
                        options: [''],
                        is_required: false,
                        min_length: '',
                        max_length: '',
                        needs_validation: false,
                        is_unique: false
                    };
                }
            }
        }
    </script>

    <style>
        .field-item.sortable-ghost {
            opacity: 0.4;
            background-color: #e0e7ff !important;
            border: 2px dashed #6366f1 !important;
        }

        .field-item.sortable-chosen {
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .field-item:hover .drag-handle {
            opacity: 1;
        }

        .drag-handle {
            opacity: 0.5;
            transition: opacity 0.2s ease;
        }
    </style>
@endsection