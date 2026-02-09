<nav class="bg-white border-b shadow">
    <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">

        <div class="flex items-center space-x-6">
            <a href="{{ route('dashboard') }}" class="font-bold text-lg">🚑 GMCI Dispatch</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.ambulances.index') }}">Ambulance</a>
                <a href="{{ route('admin.drivers.index') }}">Driver</a>
                <a href="{{ route('admin.dispatches.index') }}">Dispatch</a>
                <a href="{{ route('admin.patient-requests.index') }}">📋 Permintaan</a>
                <a href="{{ route('admin.maps') }}">🗺️ Maps</a>
            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-red-600">Logout</button>
        </form>
    </div>
</nav>
