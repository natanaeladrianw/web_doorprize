@extends('layouts.admin')

@section('title', 'Undian Doorprize')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <div x-data="multiRaffleApp()" x-init="init()" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Undian Doorprize</h2>
                <p class="text-sm text-gray-500">
                    Lakukan pengundian peserta secara acak dan transparan
                </p>
            </div>
        </div>

        <!-- Selection Area -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                <!-- Select Form -->
                <div class="flex-1 lg:max-w-md">
                    <label for="formSelect" class="block text-sm font-medium text-gray-700 mb-2">Pilih Form
                        Doorprize</label>
                    <select id="formSelect" x-model="selectedFormId" @change="fetchData()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        <option value="">-- Pilih Form --</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Category Filter -->
                <div class="flex-1 lg:max-w-md" x-show="selectedFormId && prizes.length > 0" x-transition>
                    <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori
                        Hadiah</label>
                    <select id="categoryFilter" x-model="selectedCategory"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        <option value="">-- Semua Kategori --</option>
                        <template x-for="category in uniqueCategories" :key="category">
                            <option :value="category" x-text="category"></option>
                        </template>
                    </select>
                </div>

                <!-- Info -->
                <div x-show="selectedFormId" x-transition class="flex-1">
                    <div x-show="prizes.length === 0 && !isLoading"
                        class="flex items-center gap-2 text-sm text-red-600 bg-red-50 px-4 py-2.5 rounded-lg border border-red-200">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Tidak ada hadiah tersedia untuk form ini.</span>
                    </div>
                    <div x-show="prizes.length > 0 && !isLoading"
                        class="flex items-center gap-3 text-sm bg-green-50 px-4 py-2.5 rounded-lg border border-green-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-gift text-green-600"></i>
                        </div>
                        <div class="text-slate-600">
                            <span class="font-semibold text-green-700" x-text="filteredPrizes.length + ' hadiah'"></span>
                            <span x-show="selectedCategory" class="text-xs text-gray-500">(difilter)</span>
                            tersedia untuk diundi
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="isLoading" class="text-center py-12" x-cloak>
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-purple-500 border-t-transparent">
            </div>
            <p class="mt-4 text-gray-500">Memuat data peserta...</p>
        </div>

        <!-- Main Raffle Area -->
        <div x-show="selectedFormId && !isLoading && prizes.length > 0" x-transition.opacity.duration.500ms x-cloak>

            <!-- Stats Bar -->
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-4 mb-6 text-white flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-indigo-200"></i>
                        <span class="text-sm">Total Kandidat: <strong x-text="stats.totalCandidates"></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-gift text-purple-200"></i>
                        <span class="text-sm">Hadiah Tampil: <strong x-text="filteredPrizes.length"></strong></span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Tombol Mulai Semua -->
                    <button @click="startAllRaffles()" x-show="!isAnySpinning() && hasUnstoppedPrizes()"
                        :disabled="candidates.length === 0"
                        class="bg-green-500 hover:bg-green-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-play"></i> Mulai Undian
                    </button>

                    <!-- Tombol Stop Semua -->
                    <button @click="stopAllRaffles()" x-show="isAnySpinning()"
                        class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 shadow-lg animate-pulse">
                        <i class="fas fa-stop"></i> Stop Undian
                    </button>

                    <!-- Tombol Fullscreen -->
                    <button @click="toggleFullscreen()"
                        class="bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                        <span x-text="isFullscreen ? 'Keluar' : 'Fullscreen'"></span>
                    </button>
                </div>
            </div>

            <!-- Prize Cards Grid -->
            <div id="raffle-container"
                :class="{'fixed inset-0 z-50 bg-gradient-to-br from-slate-800 via-indigo-900 to-purple-900 overflow-auto p-8': isFullscreen}"
                class="transition-all duration-300">

                <!-- Fullscreen Header -->
                <div x-show="isFullscreen" class="text-center mb-8">
                    <!-- Close Button (Top Right) -->
                    <button @click="toggleFullscreen()"
                        class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 z-10">
                        <i class="fas fa-times"></i> Tutup
                    </button>

                    <h1
                        class="text-4xl md:text-5xl font-black text-white mb-2 transform transition-all hover:scale-105 duration-300">
                        {{ \App\Models\Setting::get('raffle_title', 'DOORPRIZE') }}
                    </h1>
                    <p class="text-indigo-200 text-lg mb-6">
                        {{ \App\Models\Setting::get('raffle_subtitle', 'Pengundian Hadiah') }}
                    </p>

                    <!-- Fullscreen Control Buttons -->
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <!-- Tombol Mulai Undian -->
                        <button @click="startAllRaffles()" x-show="!isAnySpinning() && hasUnstoppedPrizes()"
                            :disabled="candidates.length === 0"
                            class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-xl text-lg font-bold transition flex items-center gap-3 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-play text-xl"></i> Mulai Undian
                        </button>

                        <!-- Tombol Stop Undian -->
                        <button @click="stopAllRaffles()" x-show="isAnySpinning()"
                            class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-xl text-lg font-bold transition flex items-center gap-3 shadow-lg animate-pulse">
                            <i class="fas fa-stop text-xl"></i> Stop Undian
                        </button>

                        <!-- Info jika semua sudah ada pemenang -->
                        <div x-show="!hasUnstoppedPrizes() && !isAnySpinning()"
                            class="bg-green-500/20 backdrop-blur text-green-300 px-6 py-3 rounded-xl text-lg font-medium flex items-center gap-3">
                            <i class="fas fa-check-circle text-xl"></i> Semua hadiah sudah ada pemenang
                        </div>
                    </div>
                </div>

                <!-- Grid Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    :class="{'max-w-7xl mx-auto': isFullscreen}">
                    <template x-for="(prize, index) in filteredPrizes" :key="prize.id">
                        <div class="relative overflow-hidden rounded-2xl shadow-xl transition-all duration-300 transform hover:scale-[1.02]"
                            :class="prizeStates[prize.id]?.winner ? 'ring-4 ring-green-400' : (prizeStates[prize.id]?.isSpinning ? 'ring-4 ring-yellow-400' : '')">

                            <!-- Card Header - Prize Name -->
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                                <h3 class="text-white font-bold text-center uppercase tracking-wider text-sm md:text-base truncate"
                                    x-text="prize.name"></h3>
                            </div>

                            <!-- Card Body - Winner Display -->
                            <div class="bg-white p-6 min-h-[180px] flex flex-col items-center justify-center relative">

                                <!-- Winner Badge -->
                                <div x-show="prizeStates[prize.id]?.winner && !prizeStates[prize.id]?.isSpinning"
                                    class="absolute top-2 right-2">
                                    <span x-show="prizeStates[prize.id]?.isSaved"
                                        class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-bold flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> TERSIMPAN
                                    </span>
                                    <span x-show="!prizeStates[prize.id]?.isSaved"
                                        class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold">
                                        ✓ PEMENANG
                                    </span>
                                </div>

                                <!-- Spinning/Winner Name Display -->
                                <div class="text-center w-full">
                                    <!-- Status Text -->
                                    <p class="text-xs uppercase tracking-widest mb-3"
                                        :class="prizeStates[prize.id]?.isSpinning ? 'text-yellow-600' : (prizeStates[prize.id]?.winner ? 'text-green-600' : 'text-gray-400')"
                                        x-text="prizeStates[prize.id]?.isSpinning ? 'Mengundi...' : (prizeStates[prize.id]?.winner ? 'Pemenang' : 'Belum diundi')">
                                    </p>

                                    <!-- Name Display - Dynamic sizing based on name length -->
                                    <div class="min-h-[4rem] flex items-center justify-center px-2">
                                        <p class="font-black transition-all duration-100 break-words text-center leading-tight"
                                            :class="[
                                                                prizeStates[prize.id]?.winner && !prizeStates[prize.id]?.isSpinning 
                                                                    ? 'text-green-600' 
                                                                    : (prizeStates[prize.id]?.isSpinning ? 'text-purple-600 animate-pulse' : 'text-gray-300'),
                                                                (prizeStates[prize.id]?.displayName?.length || 0) > 30 
                                                                    ? 'text-lg md:text-xl' 
                                                                    : ((prizeStates[prize.id]?.displayName?.length || 0) > 20 
                                                                        ? 'text-xl md:text-2xl' 
                                                                        : 'text-2xl md:text-3xl')
                                                            ]" x-text="prizeStates[prize.id]?.displayName || '---'">
                                        </p>
                                    </div>
                                </div>

                                <!-- Winner Info / Status -->
                                <div class="mt-4 flex gap-2 justify-center">
                                    <!-- Jika sudah tersimpan - tampilkan info -->
                                    <div x-show="prizeStates[prize.id]?.winner && !prizeStates[prize.id]?.isSpinning"
                                        class="flex flex-col items-center gap-2">
                                        <template x-if="prizeStates[prize.id]?.isSaved && prizeStates[prize.id]?.savedAt">
                                            <div class="text-center">
                                                <p class="text-xs text-gray-400">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span x-text="prizeStates[prize.id]?.savedAt"></span>
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Spinning Overlay Effect -->
                            <div x-show="prizeStates[prize.id]?.isSpinning"
                                class="absolute inset-0 pointer-events-none bg-gradient-to-t from-purple-500/10 to-transparent">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Fullscreen Footer -->
                <div x-show="isFullscreen" class="text-center mt-8">
                    <p x-show="hasUnstoppedPrizes() && !isAnySpinning()" class="text-indigo-200 text-sm">
                        Klik tombol <strong class="text-green-400">Mulai Undian</strong> untuk memulai pengundian semua hadiah
                    </p>
                    <p x-show="isAnySpinning()" class="text-yellow-300 text-sm animate-pulse">
                        Undian sedang berjalan... Klik <strong class="text-red-400">Stop Undian</strong> untuk menghentikan dan memilih pemenang
                    </p>
                    <p x-show="!hasUnstoppedPrizes() && !isAnySpinning()" class="text-green-300 text-sm">
                        Semua hadiah sudah memiliki pemenang
                    </p>
                </div>

                <div x-show="showPopup" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/50" @click="closePopup()"></div>

                    <!-- Modal Content -->
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                        <!-- Header dengan Icon -->
                        <div class="p-6 text-center">
                            <!-- Icon berdasarkan type -->
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" :class="{
                                                                                                'bg-green-100': popupType === 'success',
                                                                                                'bg-red-100': popupType === 'error',
                                                                                                'bg-blue-100': popupType === 'info'
                                                                                            }">
                                <i class="fas text-4xl" :class="{
                                                                                                    'fa-check-circle text-green-500': popupType === 'success',
                                                                                                    'fa-times-circle text-red-500': popupType === 'error',
                                                                                                    'fa-info-circle text-blue-500': popupType === 'info'
                                                                                                }"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-2xl font-bold mb-2" :class="{
                                                                                                'text-green-600': popupType === 'success',
                                                                                                'text-red-600': popupType === 'error',
                                                                                                'text-blue-600': popupType === 'info'
                                                                                            }" x-text="popupTitle"></h3>

                            <!-- Message -->
                            <p class="text-gray-600 text-lg" x-text="popupMessage"></p>
                        </div>

                        <!-- Footer dengan Button -->
                        <div class="px-6 pb-6">
                            <button @click="closePopup()"
                                class="w-full py-3 px-6 rounded-xl font-semibold text-white transition-all duration-200 transform hover:scale-[1.02]"
                                :class="{
                                                                                                'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700': popupType === 'success',
                                                                                                'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700': popupType === 'error',
                                                                                                'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700': popupType === 'info'
                                                                                            }">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Prizes Message -->
        <div x-show="selectedFormId && !isLoading && prizes.length === 0"
            class="text-center py-12 bg-white rounded-xl shadow" x-cloak>
            <div class="text-gray-400 mb-4">
                <i class="fas fa-gift text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Hadiah</h3>
            <p class="text-gray-500">Form ini belum memiliki hadiah yang tersedia untuk diundi.</p>
            <a href="{{ route('admin.pemenang') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                Tambah hadiah →
            </a>
        </div>

        <!-- Popup Modal Notifikasi (HARUS di dalam x-data scope) -->
    </div>

    <script>
        function multiRaffleApp() {
            return {
                selectedFormId: '',
                selectedCategory: '', // State for category filtering
                candidates: [],
                prizes: [],
                prizeStates: {},
                stats: {
                    totalCandidates: 0,
                    totalPrizes: 0,
                    availableCandidates: 0,
                    availablePrizes: 0
                },
                isLoading: false,
                isFullscreen: false,
                allSpinning: false,

                // Popup state
                showPopup: false,
                popupType: 'success', // 'success', 'error', 'info'
                popupTitle: '',
                popupMessage: '',

                init() {
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                },

                // Computed getter for unique categories
                get uniqueCategories() {
                    if (!this.prizes || this.prizes.length === 0) return [];
                    const categories = this.prizes.map(p => p.category).filter(c => c);
                    return [...new Set(categories)].sort();
                },

                // Computed getter for filtered prizes by category
                get filteredPrizes() {
                    if (!this.selectedCategory) {
                        return this.prizes;
                    }
                    return this.prizes.filter(prize => prize.category === this.selectedCategory);
                },

                toggleFullscreen() {
                    const elem = document.getElementById('raffle-container');
                    if (!document.fullscreenElement) {
                        if (elem.requestFullscreen) {
                            elem.requestFullscreen();
                        } else if (elem.webkitRequestFullscreen) {
                            elem.webkitRequestFullscreen();
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                },

                fetchData() {
                    if (!this.selectedFormId) {
                        this.resetAll();
                        return;
                    }

                    this.isLoading = true;
                    this.resetAll();

                    fetch(`/admin/forms/${this.selectedFormId}/candidates`)
                        .then(response => response.json())
                        .then(data => {
                            this.candidates = data.candidates;
                            this.prizes = data.prizes;
                            this.stats = data.stats || {
                                totalCandidates: data.candidates.length,
                                totalPrizes: data.prizes.length,
                                availableCandidates: data.candidates.length,
                                availablePrizes: data.prizes.length
                            };

                            // Initialize state for each prize
                            // Simpan juga preset_winner dan saved_winner jika ada
                            this.prizes.forEach(prize => {
                                // Cek apakah sudah ada winner yang tersimpan
                                const isSaved = prize.is_saved || false;
                                const savedWinner = prize.saved_winner || null;

                                this.prizeStates[prize.id] = {
                                    isSpinning: false,
                                    displayName: isSaved ? savedWinner.name : '---',
                                    winner: isSaved ? savedWinner : null,
                                    intervalId: null,
                                    // Simpan preset winner dari server
                                    hasPresetWinner: prize.has_preset_winner || false,
                                    presetWinner: prize.preset_winner || null,
                                    // Status simpan
                                    isSaving: false,
                                    isSaved: isSaved,
                                    // Info tambahan untuk yang sudah tersimpan
                                    savedAt: savedWinner?.selected_at || null,
                                    selectionMethod: savedWinner?.selection_method || null
                                };
                            });
                        })
                        .catch(error => {
                            this.showNotification('error', 'Error', 'Gagal memuat data undian.');
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                resetAll() {
                    // Clear all intervals
                    Object.values(this.prizeStates).forEach(state => {
                        if (state.intervalId) clearInterval(state.intervalId);
                    });

                    this.candidates = [];
                    this.prizes = [];
                    this.prizeStates = {};
                    this.selectedCategory = ''; // Reset filter
                    this.stats = {
                        totalCandidates: 0,
                        totalPrizes: 0,
                        availableCandidates: 0,
                        availablePrizes: 0
                    };
                },

                startPrizeRaffle(prizeId) {
                    if (this.candidates.length === 0) return;

                    const state = this.prizeStates[prizeId];
                    if (!state || state.isSpinning || state.winner) return;

                    state.isSpinning = true;
                    state.winner = null;

                    // Start animation - tetap random untuk efek visual (40ms = lebih cepat)
                    state.intervalId = setInterval(() => {
                        const randomIndex = Math.floor(Math.random() * this.candidates.length);
                        state.displayName = this.candidates[randomIndex].name;
                    }, 40);
                },

                stopPrizeRaffle(prizeId) {
                    const state = this.prizeStates[prizeId];
                    if (!state || !state.isSpinning) return;

                    // Stop animation
                    if (state.intervalId) {
                        clearInterval(state.intervalId);
                        state.intervalId = null;
                    }

                    // Cek apakah ada preset winner (pemenang yang sudah ditentukan sebelumnya)
                    if (state.hasPresetWinner && state.presetWinner) {
                        // Gunakan pemenang yang sudah ditentukan (manual)
                        state.winner = state.presetWinner;
                        state.displayName = state.presetWinner.name;
                        state.selectionMethod = 'manual'; // Preset = manual
                    } else {
                        // Pilih pemenang secara random
                        const randomIndex = Math.floor(Math.random() * this.candidates.length);
                        state.winner = this.candidates[randomIndex];
                        state.displayName = state.winner.name;
                        state.selectionMethod = 'random'; // Random selection
                    }

                    state.isSpinning = false;

                    // Fire confetti
                    this.fireConfetti();

                    // Auto save functionality
                    this.saveWinner(prizeId, true);
                },

                resetPrizeRaffle(prizeId) {
                    const state = this.prizeStates[prizeId];
                    if (!state) return;

                    if (state.intervalId) {
                        clearInterval(state.intervalId);
                    }

                    state.isSpinning = false;
                    state.displayName = '---';
                    state.winner = null;
                    state.intervalId = null;
                    // Tidak reset hasPresetWinner dan presetWinner agar bisa diundi ulang
                },

                startAllRaffles() {
                    if (this.candidates.length === 0) return;

                    this.allSpinning = true;
                    // Only start filtered prizes that don't have winners yet
                    this.filteredPrizes.forEach(prize => {
                        if (!this.prizeStates[prize.id]?.winner) {
                            this.startPrizeRaffle(prize.id);
                        }
                    });
                },

                stopAllRaffles() {
                    // Stop all spinning prizes
                    this.filteredPrizes.forEach(prize => {
                        if (this.prizeStates[prize.id]?.isSpinning) {
                            this.stopPrizeRaffle(prize.id);
                        }
                    });
                    this.allSpinning = false;
                },

                isAnySpinning() {
                    // Check if any prize is currently spinning
                    return this.filteredPrizes.some(prize => this.prizeStates[prize.id]?.isSpinning);
                },

                hasUnstoppedPrizes() {
                    // Only check filtered prizes
                    return this.filteredPrizes.some(prize => !this.prizeStates[prize.id]?.winner);
                },

                // Simpan pemenang via AJAX (tanpa meninggalkan halaman)
                async saveWinner(prizeId, isAuto = false) {
                    const state = this.prizeStates[prizeId];

                    if (!state) {
                        this.showNotification('error', 'Error', 'State not found');
                        return;
                    }

                    // ... validation ...

                    if (!state.winner) {
                        this.showNotification('error', 'Error', 'Tidak ada pemenang yang dipilih');
                        return;
                    }

                    if (state.isSaving) {
                        return;
                    }

                    if (state.isSaved) {
                        this.showNotification('info', 'Info', 'Pemenang sudah tersimpan!');
                        return;
                    }

                    state.isSaving = true;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            throw new Error('CSRF token not found');
                        }

                        const response = await fetch(`/admin/prizes/${prizeId}/select-manual`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                submission_id: state.winner.id,
                                selection_method: state.selectionMethod || 'random' // Default random jika tidak ada preset
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            state.isSaved = true;
                            // Update prize state
                            const prize = this.prizes.find(p => p.id === prizeId);
                            if (prize) {
                                prize.has_preset_winner = true;
                                prize.preset_winner = state.winner;
                            }
                            state.hasPresetWinner = true;
                            state.presetWinner = state.winner;

                            // Show success message only if not auto-save
                            if (!isAuto) {
                                this.showNotification('success', 'Berhasil!', data.message || 'Pemenang berhasil disimpan!');
                            }
                        } else {
                            this.showNotification('error', 'Gagal', data.message || 'Gagal menyimpan pemenang');
                        }
                    } catch (error) {
                        this.showNotification('error', 'Error', 'Terjadi kesalahan: ' + error.message);
                    } finally {
                        state.isSaving = false;
                    }
                },

                // Cek apakah hadiah sudah ada preset winner (untuk tampilan badge)
                hasPresetWinner(prizeId) {
                    return this.prizeStates[prizeId]?.hasPresetWinner || false;
                },

                fireConfetti() {
                    if (typeof confetti === 'undefined') return;

                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 },
                        zIndex: 9999
                    });
                },

                // Tampilkan popup notifikasi
                showNotification(type, title, message) {
                    this.popupType = type;
                    this.popupTitle = title;
                    this.popupMessage = message;
                    this.showPopup = true;
                },

                closePopup() {
                    this.showPopup = false;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Hide scrollbar for fullscreen mode */
        #raffle-container {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        #raffle-container::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse {
            animation: pulse 1s ease-in-out infinite;
        }
    </style>
@endsection