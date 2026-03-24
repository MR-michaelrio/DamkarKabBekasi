<nav class="bg-white border-b shadow" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('logo-damkar.png') }}" alt="Damkar Logo" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Desktop Links -->
                <div class="hidden space-x-8 lg:-my-px lg:ml-10 lg:flex items-center">
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.ambulances.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-red-600 transition">Mobil Damkar</a>
                    <a href="{{ route('admin.ambulance-types.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">Tipe Armada</a>
                    <a href="{{ route('admin.drivers.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">Driver</a>
                    <a href="{{ route('admin.dispatches.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">Dispatch</a>
                    <a href="{{ route('admin.patient-requests.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">📋 Laporan Masyarakat</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">👥 User</a>
                    <a href="{{ route('admin.maps') }}"
                        class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">🗺️ Maps</a>
                    @endif
                </div>
            </div>

            <!-- User Auth & Burger -->
            <div class="flex items-center space-x-4">
                <div class="hidden lg:block">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm font-semibold text-red-600 hover:text-red-800 transition">Logout</button>
                    </form>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center lg:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden border-t border-gray-100 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.ambulances.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-red-300 transition">Mobil
                Damkar</a>
            <a href="{{ route('admin.ambulance-types.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">Tipe
                Armada</a>
            <a href="{{ route('admin.drivers.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">Driver</a>
            <a href="{{ route('admin.dispatches.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">Dispatch</a>
            <a href="{{ route('admin.schedules.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">📅
                Jadwal</a>
            <a href="{{ route('admin.event-requests.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">🎪
                Event</a>
            <a href="{{ route('admin.patient-requests.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">📋
                Laporan Masyarakat</a>
            <a href="{{ route('admin.users.index') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">👥
                User</a>
            <a href="{{ route('admin.maps') }}"
                class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">🗺️
                Maps</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-gray-100">
                @csrf
                <button
                    class="w-full text-left pl-3 pr-4 py-2 text-base font-medium text-red-600 hover:bg-gray-50 transition">Logout</button>
            </form>
        </div>
    </div>
</nav>