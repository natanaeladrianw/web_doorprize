@extends('layouts.admin')

@section('content')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem !important;
            padding-left: 0.5rem !important;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem !important;
            border-radius: 0.25rem !important;
            border: 1px solid #e2e8f0 !important;
        }
    </style>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Forms</h1>
        <a href="{{ route('admin.forms.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 transition">
            <i class="fas fa-plus"></i>
            Create New Form
        </a>
    </div>

    <!-- Toast Notification -->
    @if(session('success'))
        <div id="toast"
            class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl p-6 z-50 min-w-[300px] border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-2 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Berhasil!</h3>
                    <p class="text-gray-600">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded p-6 my-6 overflow-x-auto">
        <table id="formsTable" class="min-w-full bg-white display responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Title</th>
                    <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Status</th>
                    <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($forms as $form)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="text-left py-3 px-6">{{ $form->title }}</td>
                        <td class="text-left py-3 px-6">
                            <form action="{{ route('admin.forms.update-status', $form) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="is_active" onchange="this.form.submit()"
                                    class="inline-block pl-3 pr-8 py-1 text-xs border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-full {{ $form->is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200' }} cursor-pointer appearance-none">
                                    <option value="1" {{ $form->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$form->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-left py-3 px-6">
                            <div class="flex items-center gap-2">
                                <!-- Detail Button -->
                                <button onclick="openPreviewModal({{ $form->id }})"
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Detail
                                </button>

                                <!-- Builder Button -->
                                <a href="{{ route('admin.forms.builder', $form) }}"
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z">
                                        </path>
                                    </svg>
                                    Builder
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('admin.forms.edit', $form) }}"
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit
                                </a>

                                <!-- Delete Button -->
                                <form id="delete-form-{{ $form->id }}" action="{{ route('admin.forms.destroy', $form) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="openDeleteModal({{ $form->id }}, '{{ addslashes($form->title) }}')"
                                        class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Preview Form</h2>
                <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="previewContent" class="p-6">
                <!-- Form preview will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all">
            <!-- Modal Header -->
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Form</h3>
                <p class="text-gray-600 mb-2">Apakah Anda yakin ingin menghapus form ini?</p>
                <p id="deleteFormTitle" class="text-lg font-semibold text-red-600 mb-4"></p>
                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan. Semua data peserta yang terkait juga
                    akan dihapus.</p>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-center gap-3">
                <button onclick="closeDeleteModal()"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-200">
                    Batal
                </button>
                <button onclick="confirmDelete()"
                    class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        // Toast notification auto-hide
        @if(session('success'))
            setTimeout(() => {
                const toast = document.getElementById('toast');
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.5s';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        @endif

            // Preview modal functions
            function openPreviewModal(formId) {
                document.getElementById('previewModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                fetch(`/admin/forms/${formId}/preview`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('previewContent').innerHTML = html;
                    })
                    .catch(error => {
                        document.getElementById('previewContent').innerHTML = '<p class="text-red-600">Error loading preview</p>';
                    });
            }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on backdrop click
        document.getElementById('previewModal')?.addEventListener('click', function (e) {
            if (e.target === this) closePreviewModal();
        });

        // DataTables initialization
        $(document).ready(function () {
            $('#formsTable').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ], // Sort by Title by default
                columnDefs: [{
                    orderable: false,
                    targets: [1, 2]
                }, // Disable sorting for Status and Actions
                {
                    width: '40%',
                    targets: 0
                },
                {
                    width: '20%',
                    targets: 1
                },
                {
                    width: '40%',
                    targets: 2
                }
                ],
                language: {
                    search: "Search Forms:",
                    lengthMenu: "Show _MENU_ forms",
                    info: "Showing _START_ to _END_ of _TOTAL_ forms"
                }
            });
        });

        // Delete modal functions
        let deleteFormId = null;

        function openDeleteModal(formId, formTitle) {
            deleteFormId = formId;
            document.getElementById('deleteFormTitle').textContent = '"' + formTitle + '"';
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            deleteFormId = null;
        }

        function confirmDelete() {
            if (deleteFormId) {
                document.getElementById('delete-form-' + deleteFormId).submit();
            }
        }

        // Close delete modal on backdrop click
        document.getElementById('deleteModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closePreviewModal();
            }
        });
    </script>
@endsection