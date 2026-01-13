@extends('layouts.admin')

@section('title', 'Kelola Admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kelola Admin</h2>
            <p class="text-slate-600">Daftar semua akun administrator sistem</p>
        </div>
        <a href="{{ route('admin.admins.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Akun</span>
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="px-6 py-4 font-semibold text-sm">Nama</th>
                        <th class="px-6 py-4 font-semibold text-sm">Email</th>
                        <th class="px-6 py-4 font-semibold text-sm">Role</th>
                        <th class="px-6 py-4 font-semibold text-sm">Bergabung Pada</th>
                        <th class="px-6 py-4 font-semibold text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $admin->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $admin->email }}</td>
                            <td class="px-6 py-4">
                                @if($admin->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        Admin
                                    </span>
                                @elseif($admin->role === 'input_hadiah')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-cog mr-1"></i>
                                        Custom
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($admin->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $admin->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.admins.edit', $admin) }}"
                                        class="text-amber-500 hover:text-amber-600 hover:bg-amber-50 p-2 rounded-lg transition"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($admin->id !== auth()->id())
                                        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition"
                                                title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data akun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($admins->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
@endsection

