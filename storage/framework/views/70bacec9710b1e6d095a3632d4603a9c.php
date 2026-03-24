<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kejadian | Damkar Bekasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-red-100 min-h-screen">

<div class="max-w-2xl mx-auto px-6 py-12">

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
        <div class="mb-6 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            <?php echo e(session('success')); ?>

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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama <span class="text-red-500">*</span>
                </label>
                <input type="text" name="patient_name" value="<?php echo e(old('patient_name')); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="Masukkan nama Anda">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kejadian <span class="text-red-500">*</span>
                </label>
                <select name="service_type" id="service_type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Jenis Kejadian --</option>
                    <option value="kebakaran" <?php echo e(old('service_type') === 'kebakaran' ? 'selected' : ''); ?>>
                        🔥 Kebakaran
                    </option>
                    <option value="rescue" <?php echo e(old('service_type') === 'rescue' ? 'selected' : ''); ?>>
                        🚒 Penyelamatan (Rescue)
                    </option>
                </select>
            </div>

            <!-- Address Detail Fields -->
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat TKP (Lengkap) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="pickup_address" rows="2" required
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                              placeholder="Contoh: Jl. Raya Cikarang No. 123"><?php echo e(old('pickup_address')); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blok (Opsional)</label>
                    <input type="text" name="blok" value="<?php echo e(old('blok')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                        <input type="text" name="rt" value="<?php echo e(old('rt')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                        <input type="text" name="rw" value="<?php echo e(old('rw')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                    <input type="text" name="kelurahan" value="<?php echo e(old('kelurahan')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="<?php echo e(old('kecamatan')); ?>" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <!-- Request Date & Time -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kejadian <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="request_date" value="<?php echo e(old('request_date', date('Y-m-d'))); ?>" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jam Kejadian <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="pickup_time" value="<?php echo e(old('pickup_time', date('H:i'))); ?>" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    No HP <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="08xxxxxxxxxx">
            </div>

            <!-- Patient Condition (Conditional for Ambulance) -->
            <div id="patient_condition_wrapper" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kondisi Korban / Pasien <span class="text-red-500">*</span>
                </label>
                <select name="patient_condition" id="patient_condition"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="emergency" <?php echo e(old('patient_condition') === 'emergency' ? 'selected' : ''); ?>>
                        🚨 EMERGENCY
                    </option>
                    <option value="kontrol" <?php echo e(old('patient_condition') === 'kontrol' ? 'selected' : ''); ?>>
                        🏥 KONTROL
                    </option>
                    <option value="pasien_pulang" <?php echo e(old('patient_condition') === 'pasien_pulang' ? 'selected' : ''); ?>>
                        🏠 PULANG
                    </option>
                </select>
            </div>

            <!-- Trip Type (Conditional for Kontrol) -->
            <div id="trip_type_wrapper" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Perjalanan <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="trip_type" value="one_way" checked
                               class="text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Pergi Saja</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="trip_type" value="round_trip"
                               class="text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-700">Pulang Pergi</span>
                    </label>
                </div>
            </div>

            <!-- Return Address (Conditional for Round Trip) -->
            <div id="return_address_wrapper" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat Pulang <span class="text-red-500">*</span>
                </label>
                <textarea name="return_address" id="return_address" rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                          placeholder="Masukkan alamat pengantaran pulang"><?php echo e(old('return_address')); ?></textarea>
            </div>

            <input type="hidden" name="destination" value="-">

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" id="submit-btn"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 px-6 rounded-xl shadow-lg transition duration-200 transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="btn-text">📤 KIRIM LAPORAN SEKARANG</span>
                    <span id="btn-loading" class="hidden">⌛ SEDANG MENGIRIM...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Footer Info -->
    <div class="mt-8 text-center text-sm text-gray-600">
        <p>Kami akan merespon laporan Anda secepatnya.</p>
        <p class="mt-2 text-lg font-bold">Layanan 24 Jam: <a href="tel:+6281286858680" class="text-red-600">+62 812-8685-8680</a></p>
    </div>

</div>

<script>
    const serviceType = document.getElementById('service_type');
    const patientConditionWrapper = document.getElementById('patient_condition_wrapper');
    const patientConditionSelect = document.getElementById('patient_condition');
    const tripTypeWrapper = document.getElementById('trip_type_wrapper');
    const returnAddressWrapper = document.getElementById('return_address_wrapper');
    const returnAddressInput = document.getElementById('return_address');
    const tripTypeRadios = document.getElementsByName('trip_type');

    function toggleFormFields() {
        // Condition Visibility
        if (serviceType.value === 'ambulance') {
            patientConditionWrapper.style.display = 'block';
            patientConditionSelect.required = true;
        } else {
            patientConditionWrapper.style.display = 'none';
            patientConditionSelect.required = false;
            patientConditionSelect.value = '';
        }

        // Trip Type Visibility (only for Kontrol)
        if (patientConditionSelect.value === 'kontrol') {
            tripTypeWrapper.style.display = 'block';
        } else {
            tripTypeWrapper.style.display = 'none';
            // Reset to one_way
            tripTypeRadios[0].checked = true;
        }

        updateReturnAddressVisibility();
    }

    function updateReturnAddressVisibility() {
        let isRoundTrip = false;
        tripTypeRadios.forEach(radio => {
            if (radio.checked && radio.value === 'round_trip') {
                isRoundTrip = true;
            }
        });

        if (isRoundTrip && patientConditionSelect.value === 'kontrol') {
            returnAddressWrapper.style.display = 'block';
            returnAddressInput.required = true;
        } else {
            returnAddressWrapper.style.display = 'none';
            returnAddressInput.required = false;
            returnAddressInput.value = '';
        }
    }

    // Initial check
    toggleFormFields();

    // Listen for changes
    serviceType.addEventListener('change', toggleFormFields);
    patientConditionSelect.addEventListener('change', toggleFormFields);
    tripTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateReturnAddressVisibility);
    });

    // Loading State for Submit
    document.getElementById('request-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        const text = document.getElementById('btn-text');
        const loading = document.getElementById('btn-loading');
        
        btn.disabled = true;
        text.classList.add('hidden');
        loading.classList.remove('hidden');
    });
</script>

</body>
</html>
<?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/patient_request/create.blade.php ENDPATH**/ ?>