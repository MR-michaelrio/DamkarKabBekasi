<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kejadian | Damkar Kabupaten Bekasi</title>
    <link rel="icon" href="<?php echo e(asset('logo-damkar.png')); ?>" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">

    <div class="max-w-4xl mx-auto px-6 py-12">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-red-700 mb-2">
                🚒 Form Laporan Kejadian
            </h1>
            <p class="text-gray-800 font-bold uppercase">
                Dinas Pemadam Kebakaran dan Penyelamatan<br>
                Kabupaten Bekasi
            </p>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
        <div class="mb-6 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded text-center">
            <p class="font-bold text-lg">✅ Laporan Berhasil Terkirim!</p>
            <p class="text-sm">Petugas akan segera menindaklanjuti laporan Anda.</p>
        </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if($errors->any()): ?>
        <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="bg-white shadow-lg rounded-lg p-8">
            <form id="request-form" method="POST" action="<?php echo e(route('patient-request.store')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>

                <!-- INFO BOX -->
                <div class="p-4 bg-amber-50 border border-amber-300 rounded-lg">
                    <p class="text-sm text-amber-800"><strong>ℹ️ Data lengkap akan dilengkapi oleh admin</strong><br>
                    Warga hanya perlu mengisi informasi dasar kejadian untuk laporan awal.</p>
                </div>

                <!-- E. DATA PELAPOR -->
                <div class="border-t-4 border-red-600 pt-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">👤 Data Pelapor</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Pelapor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="patient_name" value="<?php echo e(old('patient_name')); ?>" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                No. HP / WhatsApp (Aktif) <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3 font-bold"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </div>

                <!-- JENIS KEJADIAN -->
                <div class="border-t-4 border-red-600 pt-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">🚨 Jenis Kejadian</h2>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Jenis Kejadian <span class="text-red-500">*</span>
                        </label>
                        <select id="service_type_select" name="service_type" required
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                            <option value="">-- Pilih Jenis Kejadian --</option>
                            <option value="kebakaran" <?php echo e(old('service_type')==='kebakaran' ? 'selected' : ''); ?>>
                                🔥 Kebakaran
                            </option>
                            <option value="rescue" <?php echo e(old('service_type')==='rescue' ? 'selected' : ''); ?>>
                                🚒 Penyelamatan (Rescue)
                            </option>
                        </select>
                    </div>
                </div>

                <!-- KRONOLOGI KEJADIAN -->
                <div class="border-t-4 border-red-600 pt-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📝 Kronologi Kejadian</h2>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Deskripsikan Kejadian <span class="text-red-500">*</span>
                        </label>
                        <textarea name="event_description" rows="4" required placeholder="Deskripsikan kejadian secara detail (apa yang terjadi, dimulai dari kapan, dll)"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3"><?php echo e(old('event_description')); ?></textarea>
                    </div>
                </div>

                <!-- LOKASI KEJADIAN -->
                <div class="border-t-4 border-red-600 pt-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">📍 Lokasi Kejadian</h2>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Alamat TKP (Titik Kenal Lokasi) <span class="text-red-500">*</span>
                        </label>
                        <textarea name="pickup_address" rows="2" required placeholder="Contoh: Jl. Utama No. 12, Samping Indomaret"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3"><?php echo e(old('pickup_address')); ?></textarea>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Blok</label>
                            <input type="text" name="blok" value="<?php echo e(old('blok')); ?>"
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                            <input type="text" name="rt" value="<?php echo e(old('rt')); ?>"
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                            <input type="text" name="rw" value="<?php echo e(old('rw')); ?>"
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                            <input type="text" name="kelurahan" value="<?php echo e(old('kelurahan')); ?>"
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" value="<?php echo e(old('kecamatan')); ?>"
                            class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                    </div>
                </div>

                <!-- TANGGAL & WAKTU -->
                <div class="border-t-4 border-red-600 pt-4 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">⏰ Tanggal & Waktu Kejadian</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Tanggal Kejadian <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="request_date" value="<?php echo e(old('request_date', date('Y-m-d'))); ?>" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Jam Kejadian (WIB) <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="pickup_time" value="<?php echo e(old('pickup_time', date('H:i'))); ?>" required
                                class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 py-2 px-3">
                        </div>
                    </div>
                </div>

                <!-- Hidden Fields -->
                <div style="display:none;">
                    <input type="hidden" name="destination" value="-">
                    <input type="hidden" name="trip_type" value="one_way">
                    <input type="hidden" name="owner_name" value="">
                    <input type="hidden" name="owner_age" value="">
                    <input type="hidden" name="owner_phone" value="">
                    <input type="hidden" name="owner_profession" value="">
                    <input type="hidden" name="community_leader_name" value="">
                    <input type="hidden" name="community_leader_phone" value="">
                    <input type="hidden" name="unit_assistance" value="">
                    <input type="hidden" name="building_type" value="">
                    <input type="hidden" name="fire_cause" value="">
                    <input type="hidden" name="affected_area" value="">
                    <input type="hidden" name="time_finished" value="">
                    <input type="hidden" name="scba_usage" value="0">
                    <input type="hidden" name="apar_usage" value="0">
                    <input type="hidden" name="injured_count" value="0">
                    <input type="hidden" name="fatalities_count" value="0">
                    <input type="hidden" name="displaced_count" value="0">
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="submit-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 px-6 rounded-xl shadow-lg transition duration-200 transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text">📤 KIRIM LAPORAN</span>
                        <span id="btn-loading" class="hidden">⌛ SEDANG MENGIRIM...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>Layanan Darurat Pemerintah Kabupaten Bekasi</p>
            <p class="mt-2 text-lg font-bold">Layanan 24 Jam: <a href="tel:02122137870" class="text-red-600">02122137870</a> / <a href="tel:02122162577" class="text-red-600">02122162577</a></p>
        </div>

    </div>

    <script>
        // Show/hide kebakaran fields based on service type
        const serviceTypeSelect = document.getElementById('service_type_select');
        const kebakaranFields = document.getElementById('kebakaran-fields');

        serviceTypeSelect.addEventListener('change', function() {
            if (this.value === 'kebakaran') {
                kebakaranFields.style.display = 'block';
            } else {
                kebakaranFields.style.display = 'none';
            }
        });

        // Trigger change on page load
        serviceTypeSelect.dispatchEvent(new Event('change'));

        // Loading State for Submit
        document.getElementById('request-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const loading = document.getElementById('btn-loading');

            btn.disabled = true;
            text.classList.add('hidden');
            loading.classList.remove('hidden');
        });
    </script>

</body>

</html><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/patient_request/create.blade.php ENDPATH**/ ?>