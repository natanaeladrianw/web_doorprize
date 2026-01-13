@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('submissions.history') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mb-4 inline-block">
            ← Kembali ke Riwayat
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Detail Submission</h1>
        <p class="mt-2 text-gray-600">Informasi lengkap tentang submission Anda</p>
    </div>

    <!-- Winner Status Banner -->
    @if($submission->winner)
        <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg shadow-lg p-8 mb-6 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-3xl font-bold text-yellow-900 mb-2">Selamat, Anda Menang!</h2>
            <p class="text-yellow-800 text-lg">
                Anda terpilih sebagai pemenang untuk form ini
            </p>
            @if($submission->winner->selected_at)
                <p class="text-yellow-700 text-sm mt-2">
                    Dipilih pada {{ $submission->winner->selected_at->format('d M Y, H:i') }}
                </p>
            @endif
            @if($submission->winner->selection_method)
                <span class="inline-block mt-3 bg-yellow-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Metode: {{ ucfirst($submission->winner->selection_method) }}
                </span>
            @endif
        </div>
    @endif

    <!-- Form Information Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Form</h3>
        </div>
        <div class="p-6">
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Form</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $submission->form->title }}</dd>
                </div>
                @if($submission->form->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="mt-1 text-gray-700">{{ $submission->form->description }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Submission</dt>
                    <dd class="mt-1 text-gray-900">{{ $submission->created_at->format('d F Y, H:i') }} WIB</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Submission Data Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Data yang Anda Kirim</h3>
        </div>
        <div class="p-6">
            <dl class="space-y-6">
                @foreach($submission->formatted_data as $data)
                    <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                        <dt class="text-sm font-medium text-gray-500 mb-2">{{ $data['label'] }}</dt>
                        <dd class="text-base text-gray-900 bg-gray-50 px-4 py-3 rounded-md">
                            {{ $data['value'] }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex justify-between items-center">
        <a href="{{ route('submissions.history') }}" 
           class="text-gray-600 hover:text-gray-900 font-medium">
            ← Kembali ke Riwayat
        </a>
        <a href="{{ route('forms.index') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-md">
            Ikuti Form Lain
        </a>
    </div>
</div>
@endsection

