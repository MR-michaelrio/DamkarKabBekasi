<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kebijakan Privasi | Damkar Kabupaten Bekasi</title>
    <link rel="icon" href="{{ asset('logo-damkar.png') }}" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <div class="max-w-3xl mx-auto px-6 py-12">
        <div class="text-center mb-10">
            <img src="{{ asset('logo-damkar.png') }}" alt="Damkar Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Kebijakan Privasi</h1>
            <p class="text-gray-500 mt-2">Dinas Pemadam Kebakaran dan Penyelamatan Kab. Bekasi</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8">
            
            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3 underline decoration-emerald-500">1. Penghapusan Akun & Data</h2>
                <p class="leading-relaxed text-gray-600 mb-4">
                    Demi keamanan operasional, akun aplikasi Damkar Dispatch (Admin dan Driver) dikelola secara terpusat oleh administrator. Kami menyediakan hak bagi pengguna untuk meminta penghapusan akun dan seluruh data terkait.
                </p>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                    <h3 class="font-bold text-emerald-800 mb-1 font-sm uppercase tracking-wider">Cara Mengajukan Penghapusan:</h3>
                    <p class="text-emerald-700">
                        Silakan hubungi Administrator kami melalui:
                        <br><strong>Telepon:</strong> 02122137870 / 02122162577
                        <br><strong>Email:</strong> iptrunkbackup@gmail.com (Subject: Request Hapus Akun)
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3 underline decoration-emerald-500">2. Data yang Dihapus</h2>
                <p class="leading-relaxed text-gray-600">
                    Setelah permintaan diverifikasi, admin akan menghapus:
                </p>
                <ul class="list-disc list-inside mt-2 text-gray-600 space-y-1">
                    <li>Informasi profil pribadi (Nama, No HP).</li>
                    <li>Kredensial login (Email/Sandi).</li>
                    <li>Riwayat koordinat lokasi GPS terakhir.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3 underline decoration-emerald-500">3. Retensi Data</h2>
                <p class="leading-relaxed text-gray-600">
                    Sesuai dengan standar prosedur operasional medis dan hukum yang berlaku di Indonesia, data terkait log penugasan (dispatch) dan riwayat penanganan pasien tetap disimpan dalam arsip internal kami selama minimal 5 tahun sebagai data audit medis, namun identitas akun penggunanya akan dianonimkan atau dihapus.
                </p>
            </section>

             <section>
                <h2 class="text-xl font-bold text-gray-800 mb-3 underline decoration-emerald-500">4. Hubungi Kami</h2>
                <p class="leading-relaxed text-gray-600">
                    Jika Anda memiliki pertanyaan mengenai penggunaan data Anda, silakan hubungi kantor operasional kami di Jl. Tebet Barat Dalam Raya No.10, Jakarta Selatan.
                </p>
            </section>
        </div>

        <div class="mt-12 text-center text-sm text-gray-400">
            <a href="/" class="text-emerald-600 font-bold hover:underline"> Kembali ke Beranda</a>
            <p class="mt-4">© {{ date('Y') }} Damkar Kabupaten Bekasi.</p>
        </div>
    </div>

</body>
</html>
