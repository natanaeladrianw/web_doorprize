@extends('layouts.admin')

@section('title', 'Kelola Peserta')

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

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Kelola Peserta</h2>
                <p class="text-sm text-gray-500">Manajemen data peserta undian</p>
            </div>
        </div>

        <!-- Statistik singkat -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-sm text-gray-500">Total Peserta</p>
                <p class="text-3xl font-bold text-blue-600">{{ \App\Models\FormSubmission::count() }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-sm text-gray-500">Peserta Hari Ini</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ \App\Models\FormSubmission::whereDate('created_at', today())->count() }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow">
                <p class="text-sm text-gray-500">Pemenang</p>
                <p class="text-3xl font-bold text-yellow-600">{{ \App\Models\Winner::count() }}</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="formFilter" class="text-sm font-medium text-gray-700">Filter Form:</label>
                    <select id="formFilter"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[250px]">
                        <option value="">Semua Form</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->title }}">{{ $form->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button id="resetFilter"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition duration-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded p-6 my-6 overflow-x-auto">
            <table id="pesertaTable" class="min-w-full bg-white display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Nama Peserta
                        </th>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">NIP</th>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Form</th>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Waktu Submit
                        </th>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Status</th>
                        <th class="py-4 px-6 uppercase font-semibold text-sm text-left bg-gray-50 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($submissions as $submission)
                        @php
                            $submissionData = $submission->submission_data ?? [];
                            $pesertaName = null;
                            $pesertaNip = null;

                            // Extract Nama dan NIP dari submission_data
                            foreach ($submissionData as $key => $value) {
                                if (stripos($key, 'Nama') !== false && !$pesertaName) {
                                    $pesertaName = $value;
                                }
                                if (stripos($key, 'NIP') !== false && !$pesertaNip) {
                                    $pesertaNip = $value;
                                }
                            }

                            // Fallback untuk nama
                            if (!$pesertaName) {
                                $pesertaName = 'Peserta #' . $submission->id;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="text-left py-3 px-6">
                                <span class="font-semibold text-blue-600">{{ Str::limit($pesertaName, 30) }}</span>
                            </td>
                            <td class="text-left py-3 px-6">
                                <span class="font-mono text-sm text-gray-700">{{ $pesertaNip ?? '-' }}</span>
                            </td>
                            <td class="text-left py-3 px-6">
                                <div class="text-sm font-medium text-gray-900">{{ $submission->form->title }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($submission->form->description, 40) }}</div>
                            </td>
                            <td class="text-left py-3 px-6 text-sm">
                                {{ $submission->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="text-left py-3 px-6">
                                @if($submission->winners->count() > 0)
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        🏆 Pemenang ({{ $submission->winners->count() }}x)
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✓ Terdaftar
                                    </span>
                                @endif
                            </td>
                            <td class="text-left py-3 px-6">
                                <button onclick="showDetail({{ $submission->id }})"
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition duration-200"
                                    data-submission='@json($submission)'>
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Detail Data Peserta</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="p-6">
                <!-- Content will be loaded here via JavaScript -->
            </div>
        </div>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        // DataTables initialization
        let pesertaTable;

        $(document).ready(function () {
            pesertaTable = $('#pesertaTable').DataTable({
                responsive: true,
                order: [
                    [2, 'desc']
                ], // Sort by Waktu Submit descending by default
                columnDefs: [{
                    orderable: false,
                    targets: [4]
                }, // Disable sorting for Actions
                {
                    width: '10%',
                    targets: 0
                },
                {
                    width: '30%',
                    targets: 1
                },
                {
                    width: '20%',
                    targets: 2
                },
                {
                    width: '15%',
                    targets: 3
                },
                {
                    width: '25%',
                    targets: 4
                }
                ],
                language: {
                    search: "Search Peserta:",
                    lengthMenu: "Show _MENU_ peserta",
                    info: "Showing _START_ to _END_ of _TOTAL_ peserta",
                    zeroRecords: "Tidak ada data peserta yang ditemukan",
                    infoEmpty: "Showing 0 to 0 of 0 peserta",
                    infoFiltered: "(filtered from _MAX_ total peserta)"
                }
            });

            // Form filter event
            $('#formFilter').on('change', function () {
                const filterValue = $(this).val();
                pesertaTable.column(2).search(filterValue).draw();
            });

            // Reset filter button
            $('#resetFilter').on('click', function () {
                $('#formFilter').val('');
                pesertaTable.column(2).search('').draw();
            });
        });

        // Data submissions untuk JavaScript
        const submissionsData = @json($submissions);

        function showDetail(id) {
            const submission = submissionsData.find(s => s.id === id);
            if (!submission) return;

            let content = `
                                                <div class="space-y-4">
                                                    <!-- Winner Status (Di Atas) -->
                                                    ${submission.winners && submission.winners.length > 0 ? `
                                                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-300 p-4 rounded-lg shadow-md">
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center mr-4">
                                                                    <span class="text-3xl">🏆</span>
                                                                </div>
                                                                <div>
                                                                    <h4 class="font-bold text-yellow-900 text-lg">Peserta Ini Adalah Pemenang! (${submission.winners.length}x)</h4>
                                                                    <div class="text-sm text-yellow-700 space-y-1">
                                                                        ${submission.winners.map(w => `<p>• ${w.prize_name} - ${new Date(w.selected_at).toLocaleString('id-ID')}</p>`).join('')}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ` : ''}}

                                                    <!-- Form Info -->
                                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                                                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Informasi Form
                                                        </h4>
                                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                                            <div class="bg-white p-3 rounded shadow-sm">
                                                                <span class="text-gray-500 block text-xs mb-1">ID Peserta:</span>
                                                                <span class="font-semibold text-gray-900">#${submission.id}</span>
                                                            </div>
                                                            <div class="bg-white p-3 rounded shadow-sm">
                                                                <span class="text-gray-500 block text-xs mb-1">Nama Form:</span>
                                                                <span class="font-semibold text-gray-900">${submission.form.title}</span>
                                                            </div>
                                                            <div class="bg-white p-3 rounded shadow-sm">
                                                                <span class="text-gray-500 block text-xs mb-1">Waktu Submit:</span>
                                                                <span class="font-semibold text-gray-900">${new Date(submission.created_at).toLocaleString('id-ID')}</span>
                                                            </div>
                                                            <div class="bg-white p-3 rounded shadow-sm">
                                                                <span class="text-gray-500 block text-xs mb-1">IP Address:</span>
                                                                <span class="font-semibold text-gray-900">${submission.ip_address}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Submission Data -->
                                                    <div class="bg-white border rounded-lg shadow-sm">
                                                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-4 py-3 border-b">
                                                            <h4 class="font-semibold text-white flex items-center">
                                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                                Data yang Diisi
                                                            </h4>
                                                        </div>
                                                        <div class="p-4 space-y-3">
                                            `;

            // Handle different submission_data structures
            let submissionDataArray = submission.submission_data;
            let dataEntries = [];

            if (Array.isArray(submissionDataArray)) {
                // Format: [{label: '...', value: '...'}, ...]
                dataEntries = submissionDataArray.map(field => ({
                    label: field.label,
                    value: field.value
                }));
            } else if (typeof submissionDataArray === 'object' && submissionDataArray !== null) {
                // Format: {"Label": "Value", ...} - key-value pairs
                dataEntries = Object.entries(submissionDataArray).map(([label, value]) => ({
                    label: label,
                    value: value
                }));
            }

            dataEntries.forEach((field, index) => {
                let value = field.value;
                if (Array.isArray(value)) {
                    value = value.join(', ');
                }

                content += `
                                                    <div class="flex items-start border-b border-gray-100 pb-3 last:border-0">
                                                        <div class="flex-shrink-0 w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center mr-3">
                                                            <span class="text-teal-600 text-xs font-bold">${index + 1}</span>
                                                        </div>
                                                        <div class="flex-grow">
                                                            <div class="text-xs text-gray-500 mb-1 font-medium">${field.label}</div>
                                                            <div class="text-sm font-semibold text-gray-900">${value || '-'}</div>
                                                        </div>
                                                    </div>
                                                `;
            });

            content += `
                                                        </div>
                                                    </div>
                                                </div>
                                            `;

            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('detailModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endsection