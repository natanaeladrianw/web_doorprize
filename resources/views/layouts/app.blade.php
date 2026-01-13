<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Doorprize System' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">


    <!-- Main Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <!-- Success Popup Modal -->
                <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="background-color: rgba(0,0,0,0.5);">
                    <div class="bg-gradient-to-b from-[#A1C2BD] to-[#8BB5AE] rounded-2xl max-w-md w-full shadow-2xl">
                        <div class="p-6 text-center">
                            <!-- Success Icon GIF -->
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('icons8-correct-ezgif.com-gif-maker.gif') }}" alt="Success"
                                    class="w-20 h-20">
                            </div>

                            <!-- Title -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Berhasil!</h3>

                            <!-- Message -->
                            <p class="text-gray-700 mb-6 leading-relaxed">
                                {{ session('success') }}
                            </p>

                            <!-- OK Button -->
                            <button type="button" onclick="document.getElementById('successModal').remove();"
                                class="w-full bg-[#19183B] hover:bg-[#19183B]/80 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#19183B] focus:ring-offset-2">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <!-- Error Popup Modal -->
                <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="background-color: rgba(0,0,0,0.5);">
                    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform animate-pulse">
                        <div class="p-6 text-center">
                            <!-- Error Icon -->
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Data Sudah Terdaftar!</h3>

                            <!-- Message -->
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ session('error') }}
                            </p>

                            <!-- OK Button -->
                            <button type="button" onclick="document.getElementById('errorModal').remove();"
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                OK, Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>
</body>

</html>