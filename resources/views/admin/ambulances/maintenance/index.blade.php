@extends('layouts.app')

@section('title', 'Riwayat Perbaikan - ' . $ambulance->code)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Breadcrumbs -->
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
            <li>
                <a href="{{ route('admin.ambulances.index') }}" class="hover:text-blue-600">Armada</a>
            </li>
            <li>
                <span class="mx-2">/</span>
                <span class="text-gray-800 font-semibold">Riwayat Perbaikan {{ $ambulance->code }}</span>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            🛠️ Riwayat Perbaikan: {{ $ambulance->code }} ({{ $ambulance->plate_number }})
        </h1>
        <button onclick="document.getElementById('addMaintenanceModal').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow">
            ➕ Tambah Riwayat
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- History Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Bengkel</th>
                        <th class="px-6 py-3">Biaya</th>
                        <th class="px-6 py-3">Odometer</th>
                        <th class="px-6 py-3">Sparepart</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($maintenances as $maintenance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $maintenance->maintenance_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="capitalize">{{ $maintenance->maintenance_type }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $maintenance->workshop }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                            Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($maintenance->odometer, 0, ',', '.') }}
                            km</td>
                        <td class="px-6 py-4">
                            @if($maintenance->spare_parts && count($maintenance->spare_parts) > 0)
                            <ul class="list-disc list-inside text-xs text-gray-600">
                                @foreach($maintenance->spare_parts as $part)
                                <li>{{ $part }}</li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-gray-400 italic text-xs">Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button onclick="editMaintenance({{ json_encode($maintenance) }})"
                                class="text-blue-600 hover:text-blue-800 font-bold">
                                Edit
                            </button>
                            <form action="{{ route('admin.maintenance.destroy', $maintenance) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus riwayat ini?')"
                                    class="text-red-600 hover:text-red-800 font-bold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic text-lg">
                            Belum ada riwayat perbaikan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Maintenance Modal -->
<div id="addMaintenanceModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Tambah Riwayat Perbaikan</h2>
            <button onclick="document.getElementById('addMaintenanceModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-800 text-2xl font-bold leading-none">&times;</button>
        </div>

        <form action="{{ route('admin.ambulances.maintenance.store', $ambulance) }}" method="POST"
            class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Perbaikan</label>
                    <input type="date" name="maintenance_date" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Perbaikan</label>
                    <input type="text" name="maintenance_type" placeholder="Contoh: Service Rutin, Ganti Ban" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bengkel</label>
                    <input type="text" name="workshop" placeholder="Masukkan nama bengkel" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya (Rp)</label>
                    <input type="number" name="cost" placeholder="0" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Odometer (km)</label>
                    <input type="number" name="odometer" placeholder="0" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Sparepart Perbaikan</label>
                <div id="spareparts-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="spare_parts[]" placeholder="Nama sparepart"
                            class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" onclick="removeSparepart(this)"
                            class="bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition">
                            &times;
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addSparepart('spareparts-container')"
                    class="text-blue-600 text-sm font-bold hover:text-blue-800 transition flex items-center gap-1 mt-2">
                    <span>+</span> Tambah Sparepart
                </button>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addMaintenanceModal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700">Batal</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                    Simpan Riwayat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Maintenance Modal -->
<div id="editMaintenanceModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Edit Riwayat Perbaikan</h2>
            <button onclick="document.getElementById('editMaintenanceModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-800 text-2xl font-bold leading-none">&times;</button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Perbaikan</label>
                    <input type="date" name="maintenance_date" id="edit_date" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Perbaikan</label>
                    <input type="text" name="maintenance_type" id="edit_type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bengkel</label>
                    <input type="text" name="workshop" id="edit_workshop" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya (Rp)</label>
                    <input type="number" name="cost" id="edit_cost" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Odometer (km)</label>
                    <input type="number" name="odometer" id="edit_odometer" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Sparepart Perbaikan</label>
                <div id="edit-spareparts-container" class="space-y-2">
                    <!-- Dynamic fields will be added here -->
                </div>
                <button type="button" onclick="addSparepart('edit-spareparts-container')"
                    class="text-blue-600 text-sm font-bold hover:text-blue-800 transition flex items-center gap-1 mt-2">
                    <span>+</span> Tambah Sparepart
                </button>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editMaintenanceModal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700">Batal</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function addSparepart(containerId, value = '') {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" name="spare_parts[]" value="${value}" placeholder="Nama sparepart" 
                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeSparepart(this)" 
                    class="bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition">
                &times;
            </button>
        `;
        container.appendChild(div);
    }

    function removeSparepart(button) {
        button.parentElement.remove();
    }

    function editMaintenance(maintenance) {
        const form = document.getElementById('editForm');
        form.action = `/admin/maintenance/${maintenance.id}`;

        document.getElementById('edit_date').value = maintenance.maintenance_date.substring(0, 10);
        document.getElementById('edit_type').value = maintenance.maintenance_type;
        document.getElementById('edit_workshop').value = maintenance.workshop;
        document.getElementById('edit_cost').value = Math.round(maintenance.cost);
        document.getElementById('edit_odometer').value = maintenance.odometer;

        const container = document.getElementById('edit-spareparts-container');
        container.innerHTML = '';

        if (maintenance.spare_parts && maintenance.spare_parts.length > 0) {
            maintenance.spare_parts.forEach(part => {
                addSparepart('edit-spareparts-container', part);
            });
        } else {
            addSparepart('edit-spareparts-container');
        }

        document.getElementById('editMaintenanceModal').classList.remove('hidden');
    }
</script>
@endsection