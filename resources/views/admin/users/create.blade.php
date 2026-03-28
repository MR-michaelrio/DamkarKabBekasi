@extends('layouts.app')

@section('title', 'Tambah User | Damkar Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            👤 Tambah User Baru
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Buat akun pengguna baru untuk sistem Damkar
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-6 border border-gray-100">

        <!-- Error Validation -->
        @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Nama Lengkap
                </label>
                <input type="text" name="name" required value="{{ old('name') }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan nama lengkap">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Alamat Email
                </label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="email@example.com">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Role -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Role / Hak Akses
                    </label>
                    <select name="role" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="user" {{ old('role')==='user' ? 'selected' : '' }}>User / Operator</option>
                        <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" name="password" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Minimal 8 karakter">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ulangi password">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}"
                    class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection