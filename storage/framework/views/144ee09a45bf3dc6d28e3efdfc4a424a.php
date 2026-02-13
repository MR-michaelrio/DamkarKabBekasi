<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permintaan Layanan | GMCI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">

<div class="max-w-2xl mx-auto px-6 py-12">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            🚑 Form Permintaan Layanan
        </h1>
        <p class="text-gray-600">
            GMCI - Global Medical Care Indonesia
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
        <form method="POST" action="<?php echo e(route('patient-request.store')); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>

            <!-- Patient Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Pasien <span class="text-red-500">*</span>
                </label>
                <input type="text" name="patient_name" value="<?php echo e(old('patient_name')); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama pasien">
            </div>

            <!-- Service Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Layanan <span class="text-red-500">*</span>
                </label>
                <select name="service_type" id="service_type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Layanan --</option>
                    <option value="ambulance" <?php echo e(old('service_type') === 'ambulance' ? 'selected' : ''); ?>>
                        🚑 Ambulance
                    </option>
                    <option value="jenazah" <?php echo e(old('service_type') === 'jenazah' ? 'selected' : ''); ?>>
                        ⚰️ Mobil Jenazah
                    </option>
                </select>
            </div>

            <!-- Patient Condition (Conditional) -->
            <div id="patient_condition_wrapper" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kondisi Pasien <span class="text-red-500">*</span>
                </label>
                <select name="patient_condition" id="patient_condition"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="emergency" <?php echo e(old('patient_condition') === 'emergency' ? 'selected' : ''); ?>>
                        🚨 Emergency
                    </option>
                    <option value="kontrol" <?php echo e(old('patient_condition') === 'kontrol' ? 'selected' : ''); ?>>
                        🏥 Kontrol
                    </option>
                    <option value="pasien_pulang" <?php echo e(old('patient_condition') === 'pasien_pulang' ? 'selected' : ''); ?>>
                        🏘️ Pasien Pulang
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
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Pergi Saja</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="trip_type" value="round_trip"
                               class="text-blue-600 focus:ring-blue-500">
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
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan alamat pengantaran pulang"><?php echo e(old('return_address')); ?></textarea>
            </div>

            <!-- Request Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" name="request_date" value="<?php echo e(old('request_date')); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    No. Telepon <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="08xxxxxxxxxx">
            </div>

            <!-- Pickup Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lokasi Jemput <span class="text-red-500">*</span>
                </label>
                <textarea name="pickup_address" rows="3" required
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan alamat penjemputan"><?php echo e(old('pickup_address')); ?></textarea>
            </div>

            <!-- Destination -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tujuan <span class="text-red-500">*</span>
                </label>
                <textarea name="destination" rows="3" required
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Masukkan alamat tujuan"><?php echo e(old('destination')); ?></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition duration-200">
                    📤 Kirim Permintaan
                </button>
            </div>
        </form>
    </div>

    <!-- Footer Info -->
    <div class="mt-8 text-center text-sm text-gray-600">
        <p>Kami akan menghubungi Anda secepatnya setelah permintaan diterima.</p>
        <p class="mt-2 text-lg font-bold">Layanan 24 Jam: <a href="tel:+6281286858680" class="text-emerald-600">+62 812-8685-8680</a></p>
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
</script>

</body>
</html>
<?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/patient_request/create.blade.php ENDPATH**/ ?>