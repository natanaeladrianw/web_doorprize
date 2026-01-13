<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        /* Sidebar transition */
        .sidebar {
            transition: margin-left 0.3s ease-in-out;
        }

        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }
    </style>
    @vite(['resources/js/app.js'])
</head>

<body class="bg-slate-100">
    <div class="flex h-screen" x-data="{ sidebarOpen: true }">

        <!-- Sidebar -->
        <aside
            class="bg-gradient-to-b from-slate-900 to-slate-800 text-slate-200 w-64 flex-shrink-0 shadow-xl fixed h-full z-30 sidebar"
            :style="sidebarOpen ? 'margin-left: 0' : 'margin-left: -256px'">
            <div class="p-5 border-b border-slate-700 pl-8">
                <h2 class="text-xl font-bold tracking-wide text-white whitespace-nowrap">
                    Doorprize Admin
                </h2>
            </div>

            <nav class="mt-6 space-y-1">

                @php
                    $menuClass = 'flex items-center px-4 py-3 rounded-lg mx-3 transition whitespace-nowrap';
                    $active = 'bg-indigo-600 text-white shadow';
                    $inactive = 'text-slate-300 hover:bg-slate-700 hover:text-white';
                @endphp

                @if(auth()->user()->hasPermission('dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.dashboard') ? $active : $inactive }}">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('forms'))
                    <a href="{{ route('admin.forms.index') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.forms.*') ? $active : $inactive }}">
                        <i class="fas fa-clipboard-list w-5"></i>
                        <span class="ml-3">Kelola Forms</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('peserta'))
                    <a href="{{ route('admin.peserta') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.peserta') ? $active : $inactive }}">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-3">Kelola Peserta</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('hadiah'))
                <a href="{{ route('admin.hadiah') }}"
                    class="{{ $menuClass }} {{ request()->routeIs('admin.hadiah*') || request()->routeIs('admin.pemenang*') ? $active : $inactive }}">
                    <i class="fas fa-gift w-5"></i>
                    <span class="ml-3">Kelola Hadiah</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('pemenang'))
                    {{-- Additional link for Managing Winners specifically if needed, or if it's the same permission --}}
                    <a href="{{ route('admin.winners') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.winners') ? $active : $inactive }}">
                        <i class="fas fa-list-ol w-5"></i>
                        <span class="ml-3">Kelola Pemenang</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('undian'))
                    <a href="{{ route('admin.undian') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.undian') ? $active : $inactive }}">
                        <i class="fas fa-dice w-5"></i>
                        <span class="ml-3">Undian</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('laporan'))
                    <a href="{{ route('admin.laporan') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.laporan') ? $active : $inactive }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span class="ml-3">Laporan</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('admin'))
                    <a href="{{ route('admin.admins.index') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.admins.*') ? $active : $inactive }}">
                        <i class="fas fa-user-shield w-5"></i>
                        <span class="ml-3">Kelola Admin</span>
                    </a>
                @endif

                @if(auth()->user()->hasPermission('setting'))
                    <a href="{{ route('admin.settings.index') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.settings.index') ? $active : $inactive }}">
                        <i class="fas fa-cog w-5"></i>
                        <span class="ml-3">Pengaturan Undian</span>
                    </a>

                    <a href="{{ route('admin.settings.header') }}"
                        class="{{ $menuClass }} {{ request()->routeIs('admin.settings.header') ? $active : $inactive }}">
                        <i class="fas fa-image w-5"></i>
                        <span class="ml-3">Pengaturan Header</span>
                    </a>
                @endif


            </nav>

            <!-- Logout -->
            @if (Route::has('logout'))
                <div class="absolute bottom-0 w-64 p-4 border-t border-slate-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="w-full flex items-center justify-center gap-2 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition whitespace-nowrap">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            @endif
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col main-content" :style="sidebarOpen ? 'margin-left: 256px' : 'margin-left: 0'">

            <!-- Navbar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        <!-- Hamburger Button -->
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-800 transition focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Sistem Manajemen Doorprize
                        </h1>
                    </div>

                    @auth
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-600 to-blue-500 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-sm hidden sm:block">
                                <p class="font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
                {{-- Jika ada perubahan --}}
                @if (session('success'))
                    <div x-data="{ open: true }" x-show="open" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div class="bg-[#A7C7C5] rounded-2xl w-full max-w-md p-8 text-center shadow-2xl"
                            @click.outside="open = false"> <!-- Icon -->
                            <div class="flex justify-center mb-4">
                                <div class="w-20 h-20 rounded-full bg-white/80 flex items-center justify-center"> <i
                                        class="fas fa-check text-4xl text-green-500"></i> </div>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Berhasil! </h2>
                            <p class="text-gray-700 mb-6"> {{ session('success') }} </p> <button @click="open = false"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 rounded-xl transition">
                                OK </button>
                        </div>
                    </div>
                @endif

                {{-- Jika tidak ada perubahan --}}
                @if (session('info'))
                    <div x-data="{ open: true }" x-show="open" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div class="bg-[#E5E7EB] rounded-2xl w-full max-w-md p-8 text-center shadow-2xl"
                            @click.outside="open = false">

                            <div class="flex justify-center mb-4">
                                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center">
                                    <i class="fa-solid fa-xmark text-4xl text-red-600"></i>
                                </div>
                            </div>


                            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                                Tidak Ada Perubahan
                            </h2>

                            <p class="text-gray-700 mb-6">
                                {{ session('info') }}
                            </p>

                            <button @click="open = false"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold py-3 rounded-xl">
                                OK
                            </button>
                        </div>
                    </div>
                @endif

            </main>
        </div>
    </div>
</body>

</html>