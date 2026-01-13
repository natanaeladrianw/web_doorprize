@extends('layouts.admin')

@section('title', 'Kelola Hadiah')

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
            <h2 class="text-2xl font-bold text-slate-800">Kelola Hadiah</h2>
            <p class="text-slate-500 mt-1">
                Kelola hadiah untuk setiap doorprize
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
                        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <h5 class="font-semibold text-purple-700 mb-3">
                                <i class="fas fa-folder-open mr-2"></i>Kelola Kategori Hadiah
                            </h5>

                            {{-- Form Tambah Kategori --}}
                            <form action="{{ route('admin.prize-categories.store', $form) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="flex gap-3">
                                    <input type="text" name="name" required placeholder="Contoh: Hadiah Utama, Hadiah Doorprize"
                                        class="flex-1 border border-purple-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <button type="submit"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center gap-2">
                                        <i class="fas fa-plus"></i>
                                        <span class="hidden sm:inline">Tambah Kategori</span>
                                    </button>
                                </div>
                            </form>

                            {{-- Daftar Kategori --}}
                            @if ($form->prizeCategories->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($form->prizeCategories as $category)
                                        <div x-data="{ showDeleteModal: false }"
                                            class="inline-flex items-center gap-2 bg-white border border-purple-300 rounded-full px-3 py-1.5 text-sm">
                                            <span class="text-purple-700 font-medium">{{ $category->name }}</span>
                                            <span class="text-purple-400 text-xs">({{ $category->prizes->count() }} hadiah)</span>

                                            {{-- Tombol Hapus --}}
                                            <button type="button" @click="showDeleteModal = true"
                                                class="text-red-400 hover:text-red-600 transition ml-1" title="Hapus kategori">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            {{-- Modal Konfirmasi Hapus --}}
                                            <div x-show="showDeleteModal" x-cloak
                                                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                                <div class="bg-white rounded-xl max-w-sm w-full shadow-2xl p-6 text-center"
                                                    @click.away="showDeleteModal = false">
                                                    <div
                                                        class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <i class="fas fa-trash text-red-600 text-xl"></i>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Kategori</h3>
                                                    <p class="text-slate-500 mb-4 text-sm">
                                                        Hapus kategori "<strong>{{ $category->name }}</strong>"?
                                                        @if ($category->prizes->count() > 0)
                                                            <br><span class="text-red-500 font-medium">⚠️ Kategori ini memiliki
                                                                {{ $category->prizes->count() }} hadiah!</span>
                                                        @endif
                                                    </p>
                                                    <div class="flex gap-2 justify-center">
                                                        <form action="{{ route('admin.prize-categories.destroy', $category) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                                                Ya, Hapus
                                                            </button>
                                                        </form>
                                                        <button type="button" @click="showDeleteModal = false"
                                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-purple-500 text-sm italic">Belum ada kategori. Tambahkan kategori terlebih dahulu
                                    untuk membuat hadiah.</p>
                            @endif
                        </div>

                        {{-- Tambah Hadiah --}}
                        @if ($form->prizeCategories->count() > 0)
                            <form action="{{ route('admin.prizes.store', $form) }}" method="POST"
                                class="mb-6 p-4 bg-slate-50 rounded-lg">
                                @csrf
                                <h5 class="font-semibold text-slate-700 mb-3">➕ Tambah Hadiah Baru</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-600 mb-1">Kategori Hadiah *</label>
                                        <select name="category_id" required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($form->prizeCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama Hadiah *</label>
                                        <input type="text" name="name" required placeholder="Contoh: iPhone 15 Pro"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                            <i class="fas fa-plus mr-2"></i>Tambah Hadiah
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-center">
                                <i class="fas fa-info-circle text-yellow-600 text-2xl mb-2"></i>
                                <p class="text-yellow-700 font-medium">Tambahkan Kategori Hadiah Terlebih Dahulu</p>
                                <p class="text-yellow-600 text-sm">Anda perlu membuat kategori hadiah sebelum dapat menambahkan
                                    hadiah.</p>
                            </div>
                        @endif

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


                                                {{-- Edit Hadiah (hanya jika belum ada pemenang) --}}
                                                @if(!$prize->winner)
                                                    <div x-data="{ showEditModal: false }">
                                                        <button type="button" @click="showEditModal = true"
                                                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                                            <i class="fas fa-edit mr-1"></i>Edit
                                                        </button>

                                                        {{-- Modal Edit Hadiah --}}
                                                        <div x-show="showEditModal" x-cloak
                                                            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                                            <div class="bg-white rounded-xl max-w-md w-full shadow-2xl"
                                                                @click.away="showEditModal = false">
                                                                <div class="p-6 text-left">
                                                                    <div class="flex justify-between items-center mb-4">
                                                                        <h3 class="text-lg font-bold text-slate-800">Edit Hadiah</h3>
                                                                        <button @click="showEditModal = false"
                                                                            class="text-slate-400 hover:text-slate-600">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>

                                                                    <form action="{{ route('admin.prizes.update', $prize) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')

                                                                        <div class="space-y-4">
                                                                            <div>
                                                                                <label
                                                                                    class="block text-sm font-medium text-slate-600 mb-1">Kategori</label>
                                                                                <select name="prize_category_id"
                                                                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                                                                    <option value="">-- Pilih Kategori --</option>
                                                                                    @foreach($form->prizeCategories as $cat)
                                                                                        <option value="{{ $cat->id }}"
                                                                                            @selected($prize->category_id == $cat->id)>
                                                                                            {{ $cat->name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div>
                                                                                <label
                                                                                    class="block text-sm font-medium text-slate-600 mb-1">Nama
                                                                                    Hadiah</label>
                                                                                <input type="text" name="name" value="{{ $prize->name }}"
                                                                                    required
                                                                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                                                            </div>
                                                                        </div>

                                                                        <div class="flex gap-2 justify-end mt-6">
                                                                            <button type="button" @click="showEditModal = false"
                                                                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                                                                                Batal
                                                                            </button>
                                                                            <button type="submit"
                                                                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                                                                Simpan Perubahan
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Hapus Hadiah dengan Modal --}}
                                                <div x-data="{ showDeleteModal: false }">
                                                    <button type="button" @click="showDeleteModal = true"
                                                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                                                        <i class="fas fa-trash mr-1"></i>Hapus
                                                    </button>

                                                    {{-- Modal Konfirmasi Hapus --}}
                                                    <div x-show="showDeleteModal" x-cloak
                                                        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                                        <div class="bg-white rounded-xl max-w-md w-full shadow-2xl"
                                                            @click.away="showDeleteModal = false">
                                                            <div class="p-6 text-center">
                                                                <div
                                                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                                    <i class="fas fa-trash text-red-600 text-2xl"></i>
                                                                </div>
                                                                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus
                                                                    Hadiah</h3>
                                                                <p class="text-slate-500 mb-6">
                                                                    Hapus hadiah <strong>"{{ $prize->name }}"</strong>?
                                                                    @if ($prize->winner)
                                                                        <br><span class="text-red-500 font-medium">⚠️
                                                                            Pemenang juga akan
                                                                            dihapus!</span>
                                                                    @endif
                                                                </p>
                                                                <div class="flex gap-3 justify-center">
                                                                    <form action="{{ route('admin.prizes.destroy', $prize) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition">
                                                                            Ya, Hapus
                                                                        </button>
                                                                    </form>
                                                                    <button type="button" @click="showDeleteModal = false"
                                                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg transition">
                                                                        Batal
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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