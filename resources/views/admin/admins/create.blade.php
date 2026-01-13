@extends('layouts.admin')

@section('title', 'Tambah Akun')

@section('content')

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            <strong class="font-bold">Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-2xl mx-auto" x-data="{ role: '{{ old('role', 'admin') }}' }">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.admins.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-2 transition">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Tambah Akun Baru</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.admins.store') }}" method="POST">
                @csrf

                <div class="space-y-6">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select name="role" x-model="role" required
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">
                            <strong>Admin (Full Access):</strong> Akses penuh ke semua modul.<br>
                            <strong>Custom (Access):</strong> Pilih hak akses modul di bawah.
                        </p>
                    </div>

                    {{-- Hak Akses Modul --}}
                    <div x-show="role === 'input_hadiah'" x-transition>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Hak Akses
                        </label>
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

                            @foreach($permissions as $permission)
                                <div class="flex items-center">
                                    <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]"
                                        value="{{ $permission->id }}"
                                        class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500">
                                    <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-slate-700">
                                        {{ $permissionLabels[$permission->name] ?? ucfirst($permission->name) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-10"
                                placeholder="Minimal 8 karakter">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Password minimal 8 karakter.
                        </p>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" required minlength="8"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-10"
                                placeholder="Ulangi password">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="pt-4 flex justify-end gap-3">
                        <a href="{{ route('admin.admins.index') }}"
                            class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Simpan Akun
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection