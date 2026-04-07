@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">
            🚨 Dispatch Armada
        </h1>

        <div class="flex gap-2 w-full sm:w-auto">
            <!-- EXPORT PDF -->
            <a href="{{ route('admin.dispatches.export.pdf') }}"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm shadow flex-1 sm:flex-none text-center">
                📄 Export PDF
            </a>

            <!-- DISPATCH BARU -->
            <a href="{{ route('admin.dispatches.create') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm shadow flex-1 sm:flex-none text-center">
                ➕ Dispatch Baru
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden" 
        x-data="{ 
            loading: false,
            refresh() {
                if (this.loading) return;
                this.loading = true;
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    this.$refs.tableContainer.innerHTML = html;
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        }" 
        x-init="setInterval(() => refresh(), 10000)"
        @new-patient-request.window="refresh()">
        <div class="overflow-x-auto" x-ref="tableContainer">
            @include('admin.dispatches._table')
        </div>
    </div>
</div>
@endsection