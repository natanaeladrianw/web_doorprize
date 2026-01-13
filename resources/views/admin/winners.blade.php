@extends('layouts.admin')

@section('title', 'Kelola Pemenang')

@section('content')

    <div x-data="{
                                                selectedFormId: {{ session('selected_form_id') ?? 'null' }},
                                                forms: {{ Js::from($forms) }},
                                                searchQuery: '',
                                                currentPage: 0,
                                                perPage: 6,
                                                get selectedForm() {
                                                    return this.forms.find(f => f.id == this.selectedFormId) || null;
                                                },
                                                get filteredForms() {
                                                    if (!this.searchQuery.trim()) return this.forms;
                                                    const query = this.searchQuery.toLowerCase();
                                                    return this.forms.filter(f => 
                                                        f.title.toLowerCase().includes(query) || 
                                                        (f.description && f.description.toLowerCase().includes(query))
                                                    );
                                                },
                                                get totalPages() {
                                                    return Math.ceil(this.filteredForms.length / this.perPage);
                                                },
                                                get paginatedForms() {
                                                    const start = this.currentPage * this.perPage;
                                                    return this.filteredForms.slice(start, start + this.perPage);
                                                },
                                                nextPage() {
                                                    if (this.currentPage < this.totalPages - 1) this.currentPage++;
                                                },
                                                prevPage() {
                                                    if (this.currentPage > 0) this.currentPage--;
                                                },
                                                resetPage() {
                                                    this.currentPage = 0;
                                                }
                                            }">

        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Kelola Pemenang</h2>
            <p class="text-slate-500 mt-1">
                Kelola hadiah dan pilih pemenang untuk setiap doorprize
            </p>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">&times;</button>
            </div>
        @endif

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Pemenang</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalPemenang }}</p>
                    </div>
                    <div class="text-indigo-500 text-3xl">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Pemenang Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600">{{ $pemenangHariIni }}</p>
                    </div>
                    <div class="text-green-500 text-3xl">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Hadiah</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalHadiah }}</p>
                    </div>
                    <div class="text-purple-500 text-3xl">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Hadiah Tersedia</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $hadiahTersedia }}</p>
                    </div>
                    <div class="text-orange-500 text-3xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pilih Form Doorprize --}}
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-slate-800 mb-4">
                <i class="fas fa-clipboard-list text-indigo-600 mr-2"></i>
                Pilih Form Doorprize
            </h3>

            <div class="flex flex-wrap items-center gap-4 mb-4">
                {{-- Search Input --}}
                <div class="relative flex-1 min-w-[200px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" x-model="searchQuery" @input="resetPage()"
                        placeholder="Cari form berdasarkan judul atau deskripsi..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <button x-show="searchQuery.length > 0" @click="searchQuery = ''; resetPage()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Sort Dropdown --}}
                <div class="flex items-center gap-2">
                    <p class="text-slate-600">Urut:</p>
                    <form method="GET" id="sortForm">
                        <select name="sort" onchange="this.form.submit()"
                            class="border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="latest" @selected(request('sort') === 'latest' || !request('sort'))>Terbaru
                            </option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Terdahulu</option>
                            <option value="az" @selected(request('sort') === 'az')>A–Z</option>
                            <option value="za" @selected(request('sort') === 'za')>Z–A</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- Results Info --}}
            <div class="flex items-center justify-between mb-4 text-sm text-slate-500">
                <span x-text="'Menampilkan ' + paginatedForms.length + ' dari ' + filteredForms.length + ' form'"></span>
                <span x-show="totalPages > 1" x-text="'Halaman ' + (currentPage + 1) + ' dari ' + totalPages"></span>
            </div>

            @if ($forms->count() > 0)
                <div class="relative">
                    {{-- Left Button --}}
                    <button @click="prevPage()"
                        :class="currentPage === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100'"
                        :disabled="currentPage === 0"
                        class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full bg-white shadow-[0px_0px_6px_0px_rgba(0,0,0,0.3)] transition">
                        <i class="fas fa-chevron-left text-slate-600"></i>
                    </button>

                    {{-- Grid Forms --}}
                    <div class="md:mx-12">
                        {{-- Empty State saat tidak ada hasil pencarian --}}
                        <div x-show="filteredForms.length === 0" class="text-center py-8 text-slate-500">
                            <i class="fas fa-search text-4xl mb-3 text-slate-300"></i>
                            <p>Tidak ada form yang cocok dengan pencarian "<span x-text="searchQuery"
                                    class="font-semibold"></span>"</p>
                            <button @click="searchQuery = ''; resetPage()" class="mt-3 text-indigo-600 hover:underline">
                                Hapus pencarian
                            </button>
                        </div>

                        {{-- Form Cards Grid --}}
                        <div x-show="filteredForms.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                            <template x-for="form in paginatedForms" :key="form.id">
                                <div @click="selectedFormId = form.id"
                                    :class="selectedFormId == form.id ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'hover:bg-slate-50 hover:border-indigo-200'"
                                    class="border rounded-xl p-4 cursor-pointer transition-all">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-slate-800 truncate" x-text="form.title"></h4>
                                            <p class="text-sm text-slate-500 mt-1 line-clamp-2"
                                                x-text="form.description || 'Tidak ada deskripsi'"></p>
                                            <div class="flex items-center gap-3 mt-2 text-xs">
                                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                                    <i class="fas fa-users mr-1"></i>
                                                    <span x-text="(form.submissions || []).length"></span> peserta
                                                </span>
                                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded">
                                                    <i class="fas fa-gift mr-1"></i>
                                                    <span x-text="(form.prizes || []).length"></span> hadiah
                                                </span>
                                            </div>
                                        </div>
                                        <div x-show="selectedFormId == form.id" class="text-indigo-600 ml-2">
                                            <i class="fas fa-check-circle text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Right Button --}}
                    <button @click="nextPage()"
                        :class="currentPage >= totalPages - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100'"
                        :disabled="currentPage >= totalPages - 1"
                        class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full bg-white shadow-[0px_0px_6px_0px_rgba(0,0,0,0.3)] transition">
                        <i class="fas fa-chevron-right text-slate-600"></i>
                    </button>
                </div>

                {{-- Mobile Pagination --}}
                <div x-show="totalPages > 1" class="flex md:hidden justify-center gap-2 mt-4">
                    <button @click="prevPage()" :disabled="currentPage === 0"
                        :class="currentPage === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-4 py-2 bg-slate-200 rounded-lg text-slate-700">
                        <i class="fas fa-chevron-left"></i> Prev
                    </button>
                    <span class="px-4 py-2 text-slate-600" x-text="(currentPage + 1) + ' / ' + totalPages"></span>
                    <button @click="nextPage()" :disabled="currentPage >= totalPages - 1"
                        :class="currentPage >= totalPages - 1 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-4 py-2 bg-slate-200 rounded-lg text-slate-700">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @else
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-clipboard-list text-4xl mb-3 text-slate-300"></i>
                    <p>Belum ada form yang aktif.</p>
                    <a href="{{ route('admin.forms.create') }}" class="mt-3 inline-block text-indigo-600 hover:underline">
                        Buat form baru →
                    </a>
                </div>
            @endif
        </div>

        {{-- Kelola Hadiah (Muncul setelah pilih form) --}}
        <div x-show="selectedFormId" x-cloak>
            @foreach ($forms as $form)
                <div x-show="selectedFormId == {{ $form->id }}" class="bg-white rounded-xl shadow overflow-hidden mb-8">
                    {{-- Form Header --}}
                    <div class="p-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-xl">Kelola Hadiah: {{ $form->title }}</h4>
                                <p class="text-sm text-indigo-100 mt-1">{{ $form->submissions->count() }} peserta tersedia
                                    untuk
                                    diundi</p>
                            </div>
                            <button @click="selectedFormId = null" class="text-white/70 hover:text-white">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Kelola Kategori Hadiah --}}


                        {{-- Tambah Hadiah --}}


                        {{-- Daftar Hadiah --}}
                        @if ($form->prizes->count() > 0)
                            <div x-data="{
                                                                                                prizeSearchQuery: '',
                                                                                                prizeCategoryFilter: '',
                                                                                                prizeCurrentPage: 0,
                                                                                                prizesPerPage: 5,
                                                                                                allPrizes: {{ Js::from($form->prizes->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'category_id' => $p->category_id, 'category_name' => $p->category?->name])) }},
                                                                                                categories: {{ Js::from($form->prizeCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
                                                                                                get filteredPrizes() {
                                                                                                    let result = this.allPrizes;

                                                                                                    // Filter by Category
                                                                                                    if (this.prizeCategoryFilter) {
                                                                                                        result = result.filter(p => p.category_id == this.prizeCategoryFilter);
                                                                                                    }

                                                                                                    // Filter by Search
                                                                                                    if (this.prizeSearchQuery.trim()) {
                                                                                                        const query = this.prizeSearchQuery.toLowerCase();
                                                                                                        result = result.filter(p => 
                                                                                                            p.name.toLowerCase().includes(query) || 
                                                                                                            (p.category_name && p.category_name.toLowerCase().includes(query))
                                                                                                        );
                                                                                                    }

                                                                                                    return result;
                                                                                                },
                                                                                                get prizeTotalPages() {
                                                                                                    return Math.ceil(this.filteredPrizes.length / this.prizesPerPage);
                                                                                                },
                                                                                                get paginatedPrizeIds() {
                                                                                                    const start = this.prizeCurrentPage * this.prizesPerPage;
                                                                                                    return this.filteredPrizes.slice(start, start + this.prizesPerPage).map(p => p.id);
                                                                                                },
                                                                                                prizeNextPage() {
                                                                                                    if (this.prizeCurrentPage < this.prizeTotalPages - 1) this.prizeCurrentPage++;
                                                                                                },
                                                                                                prizePrevPage() {
                                                                                                    if (this.prizeCurrentPage > 0) this.prizeCurrentPage--;
                                                                                                },
                                                                                                prizeResetPage() {
                                                                                                    this.prizeCurrentPage = 0;
                                                                                                },
                                                                                                isPrizeVisible(prizeId) {
                                                                                                    return this.paginatedPrizeIds.includes(prizeId);
                                                                                                }
                                                                                            }" class="space-y-4">
                                {{-- Header dengan Search --}}
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <h5 class="font-semibold text-slate-700">
                                        📋 Daftar Hadiah (<span
                                            x-text="filteredPrizes.length"></span>/<span>{{ $form->prizes->count() }}</span>)
                                    </h5>

                                    <div class="flex flex-1 items-center gap-2 max-w-lg">
                                        {{-- Filter Kategori --}}
                                        <select x-model="prizeCategoryFilter" @change="prizeResetPage()"
                                            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                            <option value="">Semua Kategori</option>
                                            <template x-for="cat in categories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>

                                        {{-- Search Input --}}
                                        <div class="relative flex-1 min-w-[150px]">
                                            <i
                                                class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                            <input type="text" x-model="prizeSearchQuery" @input="prizeResetPage()"
                                                placeholder="Cari hadiah..."
                                                class="w-full pl-9 pr-8 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                            <button x-show="prizeSearchQuery.length > 0"
                                                @click="prizeSearchQuery = ''; prizeResetPage()"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info Hasil --}}
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span
                                        x-text="'Menampilkan ' + paginatedPrizeIds.length + ' dari ' + filteredPrizes.length + ' hadiah'"></span>
                                    <span x-show="prizeTotalPages > 1"
                                        x-text="'Halaman ' + (prizeCurrentPage + 1) + ' dari ' + prizeTotalPages"></span>
                                </div>

                                {{-- Empty State saat tidak ada hasil pencarian --}}
                                <div x-show="filteredPrizes.length === 0"
                                    class="text-center py-6 text-slate-500 border-2 border-dashed rounded-lg">
                                    <i class="fas fa-search text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-sm">Tidak ada hadiah yang cocok dengan pencarian "<span x-text="prizeSearchQuery"
                                            class="font-semibold"></span>"</p>
                                    <button @click="prizeSearchQuery = ''; prizeResetPage()"
                                        class="mt-2 text-sm text-indigo-600 hover:underline">
                                        Hapus pencarian
                                    </button>
                                </div>

                                @foreach ($form->prizes as $prize)
                                    <div x-show="isPrizeVisible({{ $prize->id }})" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        class="border rounded-lg p-4 {{ $prize->winner ? 'bg-green-50 border-green-200' : 'bg-white' }}">
                                        <div class="flex flex-wrap items-start justify-between gap-4">
                                            <div class="flex-1">
                                                {{-- Kategori Badge --}}
                                                @if ($prize->category)
                                                    <span
                                                        class="inline-block bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full mb-2">
                                                        <i class="fas fa-folder-open mr-1"></i>{{ $prize->category->name }}
                                                    </span>
                                                @endif
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h5 class="font-bold text-lg text-slate-800">{{ $prize->name }}</h5>
                                                    @if ($prize->winner)
                                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">✓
                                                            Sudah Ada
                                                            Pemenang</span>
                                                    @else
                                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Belum
                                                            Ada
                                                            Pemenang</span>
                                                    @endif
                                                </div>

                                                {{-- Info Preset Winner (dipilih tapi belum disimpan) --}}
                                                @if ($prize->presetSubmission && !$prize->winner)
                                                    <div class="mt-3 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500 shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-clock text-yellow-600"></i>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-slate-700">
                                                                    Preset Pemenang:
                                                                    @php
                                                                        $presetData =
                                                                            $prize->presetSubmission->submission_data;
                                                                        $presetName = is_array($presetData)
                                                                            ? $presetData['Nama Lengkap'] ??
                                                                            ($presetData['nama'] ??
                                                                                'Peserta #' .
                                                                                $prize->presetSubmission->id)
                                                                            : 'Peserta #' .
                                                                            $prize->presetSubmission->id;
                                                                    @endphp
                                                                    <span class="font-bold text-yellow-700">{{ $presetName }}</span>
                                                                </p>
                                                                <p class="text-xs text-yellow-600 mt-0.5">
                                                                    <i class="fas fa-info-circle mr-1"></i>
                                                                    Belum tercatat ke Daftar Pemenang. Otomatis ke simpan saat stop undian.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Info Pemenang yang sudah tersimpan --}}
                                                @if ($prize->winner)
                                                    <div class="mt-3 p-4 bg-white rounded-lg border-l-4 border-green-500 shadow-sm">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-trophy text-green-600"></i>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-slate-700">
                                                                    Pemenang (Tersimpan):
                                                                    @php
                                                                        $winnerData =
                                                                            $prize->winner->submission->submission_data;
                                                                        $winnerName = is_array($winnerData)
                                                                            ? $winnerData['Nama Lengkap'] ??
                                                                            ($winnerData['nama'] ??
                                                                                'Peserta #' .
                                                                                $prize->winner->submission->id)
                                                                            : 'Peserta #' .
                                                                            $prize->winner->submission->id;
                                                                    @endphp
                                                                    <span class="font-bold text-green-700">{{ $winnerName }}</span>
                                                                </p>
                                                                <p
                                                                    class="text-xs text-slate-400 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                                        {{ $prize->winner->selected_at->format('d M Y, H:i') }}
                                                                    </span>
                                                                    <span class="text-slate-300">•</span>
                                                                    <span class="inline-flex items-center">
                                                                        <i class="fas fa-dice mr-1"></i>
                                                                        {{ ucfirst($prize->winner->selection_method) }}
                                                                    </span>
                                                                    @if ($prize->winner->selector)
                                                                        <span class="text-slate-300">•</span>
                                                                        <span class="inline-flex items-center">
                                                                            <i class="fas fa-user mr-1"></i>
                                                                            {{ $prize->winner->selector->name }}
                                                                        </span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex flex-wrap gap-2">
                                                @if (!$prize->winner && !$prize->presetSubmission)
                                                    {{-- Pilih Manual --}}
                                                    @php
                                                        // Prepare submissions data for Alpine.js
                                                        $submissionsData = $form->eligibleSubmissions->map(function ($submission) {
                                                            $subData = $submission->submission_data;
                                                            $subName = 'Peserta #' . $submission->id;
                                                            $subNip = null;
                                                            $subEmail = null;

                                                            if (is_array($subData)) {
                                                                foreach ($subData as $key => $value) {
                                                                    if (stripos($key, 'Nama') !== false && !empty($value)) {
                                                                        $subName = $value;
                                                                        break;
                                                                    }
                                                                }
                                                                foreach ($subData as $key => $value) {
                                                                    if (stripos($key, 'NIP') !== false && !empty($value)) {
                                                                        $subNip = $value;
                                                                        break;
                                                                    }
                                                                }
                                                                foreach ($subData as $key => $value) {
                                                                    if (stripos($key, 'Email') !== false && !empty($value)) {
                                                                        $subEmail = $value;
                                                                        break;
                                                                    }
                                                                }
                                                            }

                                                            $identifier = $subNip ? "NIP: {$subNip}" : ($subEmail ? $subEmail : "ID: {$submission->id}");
                                                            $winCount = $submission->winners->count();

                                                            return [
                                                                'id' => $submission->id,
                                                                'name' => $subName,
                                                                'identifier' => $identifier,
                                                                'nip' => $subNip,
                                                                'email' => $subEmail,
                                                                'winCount' => $winCount,
                                                                'displayText' => $subName . ' — ' . $identifier . ($winCount > 0 ? " (sudah menang {$winCount}x)" : '')
                                                            ];
                                                        });
                                                    @endphp
                                                    <div x-data="{ 
                                                                                                                                        showModal: false,
                                                                                                                                        searchQuery: '',
                                                                                                                                        selectedSubmissionId: null,
                                                                                                                                        selectedSubmission: null,
                                                                                                                                        submissions: {{ Js::from($submissionsData) }},
                                                                                                                                        get filteredSubmissions() {
                                                                                                                                            if (!this.searchQuery.trim()) return this.submissions;
                                                                                                                                            const query = this.searchQuery.toLowerCase();
                                                                                                                                            return this.submissions.filter(s => 
                                                                                                                                                s.name.toLowerCase().includes(query) || 
                                                                                                                                                s.identifier.toLowerCase().includes(query) ||
                                                                                                                                                (s.nip && s.nip.toLowerCase().includes(query)) ||
                                                                                                                                                (s.email && s.email.toLowerCase().includes(query))
                                                                                                                                            );
                                                                                                                                        },
                                                                                                                                        selectSubmission(submission) {
                                                                                                                                            this.selectedSubmissionId = submission.id;
                                                                                                                                            this.selectedSubmission = submission;
                                                                                                                                        },
                                                                                                                                        resetModal() {
                                                                                                                                            this.searchQuery = '';
                                                                                                                                            this.selectedSubmissionId = null;
                                                                                                                                            this.selectedSubmission = null;
                                                                                                                                        }
                                                                                                                                    }">
                                                        <button @click="showModal = true; resetModal()"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                                            <i class="fas fa-user-check mr-1"></i>Pilih Manual
                                                        </button>

                                                        {{-- Modal Pilih Manual --}}
                                                        <div x-show="showModal" x-cloak
                                                            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                                            <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl max-h-[90vh] flex flex-col"
                                                                @click.away="showModal = false">
                                                                <div class="p-4 border-b flex-shrink-0">
                                                                    <h3 class="text-lg font-bold">Pilih Pemenang untuk
                                                                        "{{ $prize->name }}"
                                                                    </h3>
                                                                </div>
                                                                <form action="{{ route('admin.prizes.select-manual', $prize) }}"
                                                                    method="POST" class="p-4 flex-1 overflow-hidden flex flex-col">
                                                                    @csrf
                                                                    <input type="hidden" name="submission_id"
                                                                        x-model="selectedSubmissionId">

                                                                    {{-- Search Input --}}
                                                                    <div class="mb-3">
                                                                        <div class="relative">
                                                                            <i
                                                                                class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                            <input type="text" x-model="searchQuery"
                                                                                placeholder="Cari berdasarkan nama, NIP, atau email..."
                                                                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                                            <button type="button" x-show="searchQuery.length > 0"
                                                                                @click="searchQuery = ''"
                                                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Info hasil --}}
                                                                    <div class="text-xs text-slate-500 mb-2">
                                                                        <span
                                                                            x-text="filteredSubmissions.length + ' peserta ditemukan'"></span>
                                                                    </div>

                                                                    {{-- Daftar Peserta --}}
                                                                    <div class="flex-1 overflow-y-auto border rounded-lg mb-4 max-h-60">
                                                                        <template x-if="filteredSubmissions.length === 0">
                                                                            <div class="p-4 text-center text-slate-500">
                                                                                <i class="fas fa-search text-2xl mb-2 text-slate-300"></i>
                                                                                <p class="text-sm">Tidak ada peserta yang cocok</p>
                                                                            </div>
                                                                        </template>
                                                                        <template x-for="submission in filteredSubmissions"
                                                                            :key="submission.id">
                                                                            <div @click="selectSubmission(submission)"
                                                                                :class="selectedSubmissionId === submission.id ? 'bg-blue-50 border-blue-500' : 'hover:bg-slate-50 border-transparent'"
                                                                                class="p-3 border-l-4 cursor-pointer transition-all">
                                                                                <div class="flex items-start justify-between gap-2">
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <p class="font-medium text-slate-800 truncate"
                                                                                            x-text="submission.name"></p>
                                                                                        <p class="text-xs text-slate-500"
                                                                                            x-text="submission.identifier"></p>
                                                                                        <p x-show="submission.winCount > 0"
                                                                                            class="text-xs text-orange-600 mt-1">
                                                                                            <i class="fas fa-trophy mr-1"></i>
                                                                                            <span
                                                                                                x-text="'Sudah menang ' + submission.winCount + 'x'"></span>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div x-show="selectedSubmissionId === submission.id"
                                                                                        class="text-blue-600 flex-shrink-0">
                                                                                        <i class="fas fa-check-circle text-lg"></i>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </template>
                                                                    </div>

                                                                    {{-- Selected Info --}}
                                                                    <div x-show="selectedSubmission"
                                                                        class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                                        <p class="text-xs text-blue-600 font-medium mb-1">Peserta Terpilih:
                                                                        </p>
                                                                        <p class="font-bold text-blue-800"
                                                                            x-text="selectedSubmission?.name"></p>
                                                                        <p class="text-xs text-blue-600"
                                                                            x-text="selectedSubmission?.identifier"></p>
                                                                    </div>

                                                                    <div class="flex gap-2 flex-shrink-0">
                                                                        <button type="submit" :disabled="!selectedSubmissionId"
                                                                            :class="!selectedSubmissionId ? 'opacity-50 cursor-not-allowed' : ''"
                                                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                                                                            Konfirmasi
                                                                        </button>
                                                                        <button type="button" @click="showModal = false"
                                                                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">
                                                                            Batal
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($prize->presetSubmission || $prize->winner)
                                                    {{-- Reset Pemenang/Preset dengan Modal --}}
                                                    <div x-data="{ showResetModal: false }">
                                                        <button type="button" @click="showResetModal = true"
                                                            class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                                            <i class="fas fa-undo mr-1"></i>Reset
                                                        </button>

                                                        {{-- Modal Konfirmasi Reset --}}
                                                        <div x-show="showResetModal" x-cloak
                                                            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                                            <div class="bg-white rounded-xl max-w-md w-full shadow-2xl"
                                                                @click.away="showResetModal = false">
                                                                <div class="p-6 text-center">
                                                                    <div
                                                                        class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                                        <i class="fas fa-undo text-orange-600 text-2xl"></i>
                                                                    </div>
                                                                    <h3 class="text-lg font-bold text-slate-800 mb-2">Reset
                                                                        {{ $prize->winner ? 'Pemenang' : 'Preset' }}
                                                                    </h3>
                                                                    <p class="text-slate-500 mb-6">Reset
                                                                        {{ $prize->winner ? 'pemenang' : 'preset pemenang' }}
                                                                        untuk hadiah
                                                                        <strong>"{{ $prize->name }}"</strong>?
                                                                    </p>
                                                                    <div class="flex gap-3 justify-center">
                                                                        <form action="{{ route('admin.prizes.reset', $prize) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-6 rounded-lg transition">
                                                                                Ya, Reset
                                                                            </button>
                                                                        </form>
                                                                        <button type="button" @click="showResetModal = false"
                                                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg transition">
                                                                            Batal
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Pagination Controls --}}
                                <div x-show="prizeTotalPages > 1" class="flex items-center justify-center gap-3 pt-4 border-t mt-4">
                                    <button @click="prizePrevPage()" :disabled="prizeCurrentPage === 0"
                                        :class="prizeCurrentPage === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-100'"
                                        class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg transition flex items-center gap-2">
                                        <i class="fas fa-chevron-left"></i> Sebelumnya
                                    </button>

                                    <div class="flex items-center gap-1">
                                        <template x-for="page in prizeTotalPages" :key="page">
                                            <button @click="prizeCurrentPage = page - 1"
                                                :class="prizeCurrentPage === page - 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                                class="w-8 h-8 text-sm font-medium rounded-lg transition" x-text="page"></button>
                                        </template>
                                    </div>

                                    <button @click="prizeNextPage()" :disabled="prizeCurrentPage >= prizeTotalPages - 1"
                                        :class="prizeCurrentPage >= prizeTotalPages - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-100'"
                                        class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg transition flex items-center gap-2">
                                        Selanjutnya <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-slate-500 border-2 border-dashed rounded-lg">
                                <i class="fas fa-gift text-4xl mb-3 text-slate-300"></i>
                                <p>Belum ada hadiah untuk form ini.</p>
                                <p class="text-sm">Tambahkan hadiah menggunakan form di atas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pesan jika belum pilih form --}}
        <div x-show="!selectedFormId" class="bg-white rounded-xl shadow p-8 text-center mb-8">
            <div class="text-slate-400 mb-4">
                <i class="fas fa-hand-pointer text-5xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">Pilih Form Doorprize</h3>
            <p class="text-slate-500">Klik salah satu form di atas untuk mulai mengelola hadiah dan memilih pemenang</p>
        </div>

        {{-- Daftar Pemenang --}}
        <div class="bg-white rounded-xl shadow">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-slate-800">🏆 Daftar Semua Pemenang</h3>
                <p class="text-sm text-slate-500">Riwayat lengkap pemenang doorprize</p>
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
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($winners as $index => $winner)
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
                            <tr class="hover:bg-slate-50" x-data="{ showDetailModal: false }">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800">
                                    {{ $wName }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="font-mono text-sm text-gray-600">{{ $wNip ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ $winner->prize->name ?? ($winner->prize_name ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">{{ $winner->submission->form->title ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-500">
                                    {{ $winner->selected_at?->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-3">
                                    <button type="button" @click="showDetailModal = true"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium py-1.5 px-3 rounded-lg transition flex items-center gap-1">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>

                                    {{-- Modal Detail Pemenang --}}
                                    <div x-show="showDetailModal" x-cloak
                                        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                        <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl max-h-[90vh] overflow-y-auto"
                                            @click.away="showDetailModal = false">
                                            <div class="p-6">
                                                {{-- Header --}}
                                                <div class="flex justify-between items-center mb-6">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                                            <span class="text-2xl">🏆</span>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-lg font-bold text-slate-800">Detail Pemenang
                                                            </h3>
                                                            <p class="text-sm text-slate-500">Informasi lengkap pemenang
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <button @click="showDetailModal = false"
                                                        class="text-slate-400 hover:text-slate-600">
                                                        <i class="fas fa-times text-xl"></i>
                                                    </button>
                                                </div>

                                                {{-- Winner Info --}}
                                                <div class="space-y-4">
                                                    {{-- Nama Pemenang --}}
                                                    <div
                                                        class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 p-4 rounded-lg">
                                                        <p class="text-xs text-yellow-600 font-semibold mb-1">PEMENANG</p>
                                                        <p class="text-xl font-bold text-slate-800">{{ $wName }}
                                                        </p>
                                                    </div>

                                                    {{-- Grid Info --}}
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="bg-slate-50 p-3 rounded-lg">
                                                            <p class="text-xs text-slate-500 mb-1">Hadiah</p>
                                                            <p class="font-semibold text-purple-600">
                                                                {{ $winner->prize->name ?? ($winner->prize_name ?? '-') }}
                                                            </p>
                                                        </div>
                                                        <div class="bg-slate-50 p-3 rounded-lg">
                                                            <p class="text-xs text-slate-500 mb-1">Form</p>
                                                            <p class="font-semibold text-slate-700">
                                                                {{ $winner->submission->form->title ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="bg-slate-50 p-3 rounded-lg">
                                                            <p class="text-xs text-slate-500 mb-1">Metode Seleksi</p>
                                                            <span
                                                                class="px-2 py-1 rounded text-xs font-medium {{ $winner->selection_method === 'random' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                                                {{ ucfirst($winner->selection_method) }}
                                                            </span>
                                                        </div>
                                                        <div class="bg-slate-50 p-3 rounded-lg">
                                                            <p class="text-xs text-slate-500 mb-1">Tanggal Terpilih</p>
                                                            <p class="font-semibold text-slate-700">
                                                                {{ $winner->selected_at?->format('d M Y, H:i') }}
                                                            </p>
                                                        </div>
                                                        <div class="bg-slate-50 p-3 rounded-lg col-span-2">
                                                            <p class="text-xs text-slate-500 mb-1">Dipilih Oleh</p>
                                                            <p class="font-semibold text-slate-700">
                                                                {{ $winner->selector->name ?? 'System' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {{-- Submission Data --}}
                                                    <div class="bg-white border rounded-lg">
                                                        <div
                                                            class="bg-gradient-to-r from-teal-500 to-teal-600 px-4 py-2 rounded-t-lg">
                                                            <h4 class="font-semibold text-white text-sm">Data Peserta</h4>
                                                        </div>
                                                        <div class="p-4 space-y-2">
                                                            @if (is_array($wData))
                                                                @foreach ($wData as $key => $value)
                                                                    <div
                                                                        class="flex justify-between border-b border-slate-100 pb-2 last:border-0">
                                                                        <span class="text-sm text-slate-500">{{ $key }}</span>
                                                                        <span
                                                                            class="text-sm font-medium text-slate-700">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-slate-500 text-sm">Tidak ada data</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Footer --}}
                                                <div class="mt-6 flex justify-end">
                                                    <button @click="showDetailModal = false"
                                                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium py-2 px-6 rounded-lg transition">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-500">
                                    Belum ada data pemenang
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

@endsection