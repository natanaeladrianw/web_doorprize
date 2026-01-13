@extends('layouts.admin')

@section('title', 'Kelola Forms')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold mb-4">Kelola Forms</h2>

    <table class="min-w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Nama Form</th>
                <th class="px-4 py-2 border">Jumlah Peserta</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forms as $index => $form)
                <tr>
                    <td class="px-4 py-2 border">
                        {{ $forms->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-2 border">
                        {{ $form->name ?? '-' }}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        {{ $form->submissions_count ?? 0 }}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                            Detail
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">
                        Belum ada form
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $forms->links() }}
    </div>
</div>
@endsection
@extends('layouts.admin')