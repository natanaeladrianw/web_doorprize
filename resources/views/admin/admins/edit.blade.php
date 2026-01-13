@extends('layouts.admin')

@section('title', 'Edit Akun')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.admins.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-2 transition">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Edit Akun</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6" x-data="{ role: '{{ old('role', $admin->role) }}' }">
            <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select name="role" id="role" x-model="role" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            {{ $admin->id === auth()->id() ? 'disabled' : '' }}>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @if($admin->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $admin->role }}">
                            <p class="mt-1 text-xs text-amber-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                Anda tidak dapat mengubah role akun Anda sendiri.
                            </p>
                        @else
                            <p class="mt-1 text-xs text-slate-500">
                                <strong>Full Access:</strong> Akses penuh ke semua fitur.<br>
                                <strong>Custom:</strong> Pilih hak akses spesifik di bawah.
                            </p>
                        @endif
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Permissions --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Hak Akses</label>
                        <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-lg border">
                            @php
                                $permissionLabels = [
                                    'dashboard' => 'Dashboard',
                                    'forms' => 'Kelola Forms',
                                    'peserta' => 'Kelola Peserta',
                                    'hadiah' => 'Kelola Hadiah',
                                    'pemenang' => 'Kelola Pemenang',
                                    'undian' => 'Undian',
                                    'laporan' => 'Laporan',
                                    'admin' => 'Kelola Admin',
                                    'setting' => 'Pengaturan Undian',
                                ];
                            @endphp

                            @forelse($permissions as $permission)
                                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ 
                                                                    in_array($permission->id, old('permissions', $userPermissions ?? []))
                                ? 'checked' : '' 
                                                                }} :disabled="role === 'admin'"
                                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                    {{ $permissionLabels[$permission->name] ?? ucfirst($permission->name) }}
                                                </label>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada hak akses tersedia.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="text-lg font-medium text-slate-800 mb-4">Ubah Password</h3>
                        <p class="text-sm text-slate-500 mb-4">Biarkan kosong jika tidak ingin mengubah password.</p>

                        <div class="space-y-6">
                            <div x-data="{ show: false }">
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password
                                    Baru</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" id="password"
                                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-10"
                                        placeholder="********">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ show: false }">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation"
                                        id="password_confirmation"
                                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-10"
                                        placeholder="********">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.admins.index') }}"
                            class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection