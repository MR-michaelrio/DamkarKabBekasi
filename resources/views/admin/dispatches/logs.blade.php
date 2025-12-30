@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <h1 class="text-xl font-bold mb-4">📜 Log Dispatch</h1>

    <table class="w-full text-sm bg-white rounded shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Waktu</th>
                <th class="p-2">Dispatch</th>
                <th class="p-2">Status</th>
                <th class="p-2">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $l)
            <tr class="border-t">
                <td class="p-2">{{ $l->created_at }}</td>
                <td class="p-2">{{ $l->dispatch_id }}</td>
                <td class="p-2">{{ strtoupper($l->status) }}</td>
                <td class="p-2">{{ $l->note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
