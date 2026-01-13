@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Sistem</h2>
        <p class="text-slate-500 mt-1">
            Ringkasan data peserta, pemenang, dan riwayat pengisian formulir.
        </p>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Peserta</p>
                    <p class="text-3xl font-bold text-indigo-600">
                        {{ $totalPeserta ?? 0 }}
                    </p>
                </div>
                <div class="text-indigo-500 text-3xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Pemenang</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $totalPemenang ?? 0 }}
                    </p>
                </div>
                <div class="text-green-500 text-3xl">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Form Aktif</p>
                    <p class="text-3xl font-bold text-blue-600">
                        {{ $totalForm ?? 0 }}
                    </p>
                </div>
                <div class="text-blue-500 text-3xl">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pengisian Form (Filter & Table) --}}
    <div class="bg-white rounded-xl shadow mb-8">
        <div class="p-6 border-b flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">📋 Riwayat Isi Formulir</h3>
                <p class="text-sm text-slate-500">Data masuk dari peserta (Filter & Cari)</p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="p-6 bg-slate-50 border-b">
            <form action="{{ route('admin.laporan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nama peserta / data...">
                </div>

                {{-- Filter Form --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Form</label>
                    <select name="form_id"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Form</option>
                        @foreach ($forms as $form)
                            <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>
                                {{ $form->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Rentang Tanggal</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.laporan') }}"
                        class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table Submissions --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100/50 text-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama Peserta</th>
                        <th class="px-6 py-3 text-left">NIP</th>
                        <th class="px-6 py-3 text-left">Form</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $submission)
                        @php
                            $submissionData = $submission->submission_data ?? [];
                            $name = null;
                            $nip = null;

                            // Extract Nama dan NIP dari submission_data
                            foreach ($submissionData as $key => $value) {
                                if (stripos($key, 'Nama') !== false && !$name) {
                                    $name = $value;
                                }
                                if (stripos($key, 'NIP') !== false && !$nip) {
                                    $nip = $value;
                                }
                            }

                            // Fallback untuk nama
                            if (!$name) {
                                $name = 'Peserta #' . $submission->id;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-gray-500">
                                {{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}
                            </td>
                            <td class="px-6 py-3 font-medium text-blue-600">
                                {{ Str::limit($name, 30) }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="font-mono text-sm text-gray-600">{{ $nip ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $submission->form->title ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-xs text-gray-500">
                                    {{ $submission->winner ? '🏆 Winner' : 'Peserta' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-search text-4xl mb-3 text-gray-200 block"></i>
                                Tidak ada data yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $submissions->links() }}
        </div>
    </div>

    {{-- Tabel Data Pemenang Terbaru --}}
    <div class="bg-white rounded-xl shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">📄 Data Pemenang Terbaru</h3>
                <p class="text-sm text-slate-500">
                    Daftar pemenang hasil undian terbaru
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.laporan.export-pdf') }}" target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('admin.laporan.export') }}" target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Pemenang</th>
                        <th class="px-6 py-3 text-left">NIP</th>
                        <th class="px-6 py-3 text-left">Hadiah</th>
                        <th class="px-6 py-3 text-left">Form</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($winners ?? [] as $winner)
                        @php
                            $wData = $winner->submission->submission_data ?? [];
                            $wName = null;
                            $wNip = null;

                            // Extract Nama dan NIP
                            if (is_array($wData)) {
                                foreach ($wData as $key => $value) {
                                    if (stripos($key, 'Nama') !== false && !$wName) {
                                        $wName = $value;
                                    }
                                    if (stripos($key, 'NIP') !== false && !$wNip) {
                                        $wNip = $value;
                                    }
                                }
                            }

                            if (!$wName) {
                                $wName = 'Peserta #' . $winner->submission->id;
                            }
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">{{ $loop->iteration }}</td>
                            <td class="px-6 py-3 font-medium text-slate-800">
                                {{ $wName }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="font-mono text-sm text-gray-600">{{ $wNip ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-medium">
                                    {{ $winner->prize->name ?? 'Hadiah dihapus' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ $winner->submission->form->title ?? '-' }}</td>
                            <td class="px-6 py-3 text-slate-500">
                                {{ $winner->selected_at?->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                                    <p>Belum ada data pemenang</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
