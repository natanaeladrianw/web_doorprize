@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Header Form</h1>
        <p class="text-gray-600 mt-1">Upload gambar header untuk halaman daftar form doorprize</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Text Settings Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Teks Header</h2>
            
            <form action="{{ route('admin.settings.header.text') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="raffle_title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Utama
                    </label>
                    <input type="text" id="raffle_title" name="raffle_title" value="{{ old('raffle_title', $raffleTitle) }}" required
                        class="block w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 shadow-sm"
                        placeholder="Contoh: DOORPRIZE">
                    <p class="mt-1 text-sm text-gray-500">Teks besar di bagian atas halaman undian.</p>
                </div>

                <div class="mb-6">
                    <label for="raffle_subtitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Sub Judul
                    </label>
                    <input type="text" id="raffle_subtitle" name="raffle_subtitle" value="{{ old('raffle_subtitle', $raffleSubtitle) }}"
                        class="block w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 shadow-sm"
                        placeholder="Contoh: Pengundian Hadiah">
                    <p class="mt-1 text-sm text-gray-500">Teks kecil di bawah judul utama.</p>
                </div>

                <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    Simpan Teks
                </button>
            </form>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Header Image</h2>
            
            <form id="uploadForm" action="{{ route('admin.settings.header.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Gambar
                    </label>
                    <input type="file" id="imageInput" accept="image/*" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('header_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Cropper Container -->
                <div id="cropperContainer" class="hidden mb-4">
                    <div class="mb-2 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Crop Gambar (Rasio 6:1)</span>
                        <button type="button" id="resetCrop" class="text-xs text-blue-600 hover:text-blue-800">Reset</button>
                    </div>
                    <div class="border border-gray-300 rounded-lg overflow-hidden" style="max-height: 400px;">
                        <img id="cropperImage" style="max-width: 100%;">
                    </div>
                </div>

                <input type="hidden" id="croppedImage" name="cropped_image">

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">📏 Informasi:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• <strong>Rasio:</strong> 6:1 (landscape) - otomatis saat crop</li>
                        <li>• <strong>Format:</strong> JPG, PNG, GIF</li>
                        <li>• Hasil crop: max 1920x320px (otomatis resize)</li>
                    </ul>
                    <p class="text-xs text-blue-700 mt-2 italic">
                        Gambar akan ditampilkan penuh di bagian atas halaman daftar form
                    </p>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    Upload Header
                </button>
            </form>
        </div>

        <!-- Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Preview Header Saat Ini</h2>
            
            @if($headerImage)
                <div class="space-y-4">
                    <div class="rounded-lg overflow-hidden border border-gray-200">
                        <img src="{{ asset($headerImage) }}" alt="Header Image" class="w-full h-auto">
                    </div>
                    
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Header aktif</span>
                        <form action="{{ route('admin.settings.header.delete') }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus header image?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                Hapus Header
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Belum ada header image</p>
                    <p class="text-xs text-gray-500 mt-1">Upload gambar untuk menampilkan header</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <script>
        let cropper;
        const imageInput = document.getElementById('imageInput');
        const cropperContainer = document.getElementById('cropperContainer');
        const cropperImage = document.getElementById('cropperImage');
        const croppedImageInput = document.getElementById('croppedImage');
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');
        const resetBtn = document.getElementById('resetCrop');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperContainer.classList.remove('hidden');
                    
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 6 / 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        resetBtn.addEventListener('click', function() {
            if (cropper) {
                cropper.reset();
            }
        });

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (cropper) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Memproses...';
                
                cropper.getCroppedCanvas({
                    maxWidth: 1920,
                    maxHeight: 320,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                }).toBlob(function(blob) {
                    const formData = new FormData(uploadForm);
                    formData.delete('cropped_image');
                    formData.set('header_image', blob, 'header.jpg');
                    
                    fetch(uploadForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Upload gagal!');
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Upload Header';
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error);
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Upload Header';
                    });
                }, 'image/jpeg', 0.9);
            } else {
                uploadForm.submit();
            }
        });
    </script>
@endsection
