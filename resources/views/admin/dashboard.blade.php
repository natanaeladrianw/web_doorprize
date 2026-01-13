@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Peserta</p>
                        <p class="text-3xl font-bold text-blue-500 mt-2">{{ number_format($stats['total_peserta']) }}</p>
                        <p class="text-gray-500 text-xs mt-1">+{{ $stats['peserta_baru'] }} bulan ini</p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Pemenang</p>
                        <p class="text-3xl font-bold text-green-500 mt-2">{{ number_format($stats['total_pemenang']) }}</p>
                        <p class="text-gray-500 text-xs mt-1">Dari semua undian</p>
                    </div>
                    <i class="fas fa-trophy text-green-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Peserta Baru</p>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">{{ number_format($stats['peserta_baru']) }}</p>
                        <p class="text-gray-500 text-xs mt-1">30 hari terakhir</p>
                    </div>
                    <i class="fas fa-user-plus text-yellow-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Forms Aktif</p>
                        <p class="text-3xl font-bold text-purple-500 mt-2">{{ number_format($stats['forms_aktif']) }}</p>
                        <p class="text-gray-500 text-xs mt-1">Sedang berjalan</p>
                    </div>
                    <i class="fas fa-clipboard-list text-purple-500 text-4xl opacity-20"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pendaftaran Terbaru</h2>
                <div class="space-y-3">
                    @forelse($recent_activities as $activity)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Peserta baru terdaftar</p>
                                <p class="text-xs text-gray-500">{{ $activity->form->title ?? 'Form' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.peserta') }}"
                    class="block mt-4 text-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Lihat Semua Peserta →
                </a>
            </div>

            <!-- Recent Winners - Hanya untuk admin penuh -->
            <!-- Recent Winners -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pemenang Terbaru</h2>
                <div class="space-y-3">
                    @forelse($recent_winners as $winner)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    <i class="fas fa-trophy text-yellow-500 mr-1"></i>
                                    {{ $winner->prize_name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $winner->submission->form->title ?? 'Form' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $winner->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada pemenang</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.pemenang') }}"
                    class="block mt-4 text-center text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Lihat Semua Pemenang →
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        @if(auth()->user()->hasPermission('forms') || auth()->user()->hasPermission('peserta') || auth()->user()->hasPermission('undian'))
            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @if(auth()->user()->hasPermission('forms'))
                        <a href="{{ route('admin.forms.index') }}"
                            class="flex flex-col items-center justify-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <i class="fas fa-plus-circle text-4xl text-blue-600 mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Buat Form Baru</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('peserta'))
                        <a href="{{ route('admin.peserta') }}"
                            class="flex flex-col items-center justify-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <i class="fas fa-users text-4xl text-green-600 mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Lihat Peserta</span>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('undian'))
                        <a href="{{ route('admin.undian') }}"
                            class="flex flex-col items-center justify-center p-6 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                            <i class="fas fa-gift text-4xl text-yellow-600 mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Mulai Undian</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection