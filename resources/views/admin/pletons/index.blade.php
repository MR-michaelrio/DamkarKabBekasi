@extends('layouts.app')

@section('title', 'Manajemen Pleton | Damkar Dispatch')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            🛡️ Manajemen Pleton
        </h1>

        <a href="{{ route('admin.pletons.create') }}"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow w-full sm:w-auto text-center">
            ➕ Tambah Pleton
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3 whitespace-nowrap">Nama Pleton</th>
                        <th class="px-6 py-3 whitespace-nowrap">Jumlah Anggota</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($pletons as $pleton)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $pleton->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                    {{ $pleton->drivers_count }} Anggota
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                <a href="{{ route('admin.pletons.edit', $pleton) }}"
                                   class="text-blue-600 hover:text-blue-800 font-bold">
                                    Edit
                                </a>

                                <form action="{{ route('admin.pletons.destroy', $pleton) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Hapus pleton ini?')"
                                        class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada data pleton
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
