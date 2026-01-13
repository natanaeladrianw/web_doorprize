@extends('layouts.app')

@section('content')

    @php
        $headerImage = \App\Models\Setting::get('form_header_image');
    @endphp

    @if($headerImage)
        <!-- Header with Image -->
        <div class="mb-8 rounded-lg overflow-hidden" style="height: 164px;">
            <img src="{{ asset($headerImage) }}" alt="Form Header" class="w-full h-full object-cover">
        </div>
    @else
        <!-- Default Header with Gradient -->
        <div class="mb-8 text-center bg-gradient-to-r from-[#708993] via-[#E7F2EF] to-[#A1C2BD] py-12 px-4 rounded-lg">
            <h1 class="text-3xl font-bold text-gray-900">Daftar Form Doorprize</h1>
            <p class="mt-2 text-gray-600">Pilih form yang ingin Anda ikuti dan isi data dengan lengkap</p>
        </div>
    @endif

    @if($forms->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Form Aktif</h3>
            <p class="text-gray-600">Saat ini belum ada form doorprize yang tersedia. Silakan cek kembali nanti.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($forms as $form)
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                    <div class="p-6 flex-grow flex flex-col">
                        <!-- Form Title -->
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $form->title }}
                        </h3>

                        <!-- Form Description -->
                        @if($form->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $form->description }}
                            </p>
                        @else
                            <p class="text-gray-400 text-sm mb-4 italic">Tidak ada deskripsi</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ $form->submissions_count }} peserta</span>

                            <svg class="w-4 h-4 ml-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ $form->fields->count() }} pertanyaan</span>
                        </div>

                        <!-- Spacer to push button to bottom -->
                        <div class="flex-grow"></div>

                        <!-- Action Button -->
                        <a href="{{ route('forms.show', ['form' => $form, 'page' => request('page', 1)]) }}"
                            class="block w-full text-center bg-[#19183B] hover:bg-[#19183B]/80 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Ikuti Sekarang
                        </a>
                    </div>


                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $forms->links() }}
        </div>
    @endif
@endsection