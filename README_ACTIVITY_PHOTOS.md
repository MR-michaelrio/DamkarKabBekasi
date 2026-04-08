# 📸 Sistem Laporan Foto Aktivitas - Panduan Lengkap

**Versi**: 1.0  
**Status**: ✅ Production Ready  
**Update**: 8 April 2026

---

## 📖 Daftar Isi

1. [Ringkasan](#ringkasan)
2. [Fitur Utama](#fitur-utama)
3. [Instalasi & Setup](#instalasi--setup)
4. [Penggunaan](#penggunaan)
5. [Integrasi](#integrasi)
6. [API Reference](#api-reference)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 Ringkasan

Sistem laporan foto yang memungkinkan setiap aktivitas driver dilengkapi dengan maksimal **5 foto**. Pengguna dapat mengambil foto langsung menggunakan kamera atau memilih foto dari galeri.

**Use Cases:**
- 📷 Dokumentasi kegiatan dispatch
- 📋 Laporan kejadian/insiden
- ✅ Verifikasi penyelesaian tugas
- 📊 Audit trail dengan bukti visual

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 📸 **Capture Photo** | Ambil foto langsung dengan kamera perangkat |
| 🖼️ **Gallery Select** | Pilih foto dari galeri perangkat |
| 📝 **Description** | Tambahkan deskripsi untuk setiap foto |
| 🔄 **Reorder** | Otomatis mengurutkan foto saat ada perubahan |
| 🗑️ **Delete** | Hapus foto yang tidak diinginkan |
| ✅ **Max 5 Photos** | Batasan maksimal 5 foto per aktivitas |
| 📊 **Progress Bar** | Indikator progress saat upload |
| 🔒 **Secure Upload** | Validasi file & user verification |
| 📱 **Responsive** | Bekerja di mobile, tablet, desktop |

---

## 🚀 Instalasi & Setup

### Step 1: Update Kode (Sudah Dilakukan ✅)

Semua file sudah dibuat:
- ✅ Database migration
- ✅ Models & services
- ✅ API controllers
- ✅ Vue components
- ✅ Routes

### Step 2: Run Migration

```bash
cd /Applications/Dev/damkar-dispatch

php artisan migrate
```

Output yang diharapkan:
```
Migrating: 2026_04_08_create_activity_photos_table
Migrated:  2026_04_08_create_activity_photos_table (XXXms)
```

### Step 3: Create Storage Symlink

```bash
php artisan storage:link
```

### Step 4: Install Capacitor (untuk mobile)

```bash
npm install @capacitor/camera
npx cap sync
```

### Step 5: Verifikasi Setup

```bash
# Check routes
php artisan route:list | grep activity-photos

# Check database
php artisan tinker
>>> ActivityLog::count()
>>> ActivityPhoto::count()
```

**✅ Setup Selesai!**

---

## 💡 Penggunaan

### Opsi 1: Gunakan Setup Script (Recommended)

```bash
./setup-activity-photos.sh
```

Script akan:
- ✅ Run migrations
- ✅ Create storage symlink
- ✅ Check permissions
- ✅ Install Capacitor (optional)

### Opsi 2: Setup Manual

```bash
# 1. Migration
php artisan migrate

# 2. Storage symlink
php artisan storage:link

# 3. Check permissions
ls -la storage/

# 4. Capacitor (optional)
npm install @capacitor/camera
npx cap sync
```

---

## 🔗 Integrasi

### A. Di dalam Vue Component

#### Mobile (Capacitor)
```vue
<template>
  <div class="container">
    <!-- Activity Photo Uploader Component -->
    <ActivityPhotoUploader 
      :activity-id="dispatch.id"
      :max-photos="5"
      @updated="onPhotosChanged"
      @error="onUploadError"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'

const dispatch = ref({ id: 123 })

const onPhotosChanged = (photos) => {
  console.log('Foto diperbarui:', photos)
  // Handle photos update (e.g., save to backend, show notification)
}

const onUploadError = (error) => {
  console.error('Error:', error)
  // Handle upload error (e.g., show error message)
}
</script>
```

#### Web (Drag & Drop)
```vue
<template>
  <div class="container">
    <!-- Gunakan Web component untuk web interface -->
    <ActivityPhotoUploaderWeb 
      :activity-id="dispatch.id"
      @updated="onPhotosChanged"
    />
  </div>
</template>

<script setup lang="ts">
import ActivityPhotoUploaderWeb from '@/components/ActivityPhotoUploaderWeb.vue'
// ... rest of component
</script>
```

#### Auto-detect (Mobile atau Web)
```vue
<template>
  <component 
    :is="isMobile ? 'ActivityPhotoUploader' : 'ActivityPhotoUploaderWeb'"
    :activity-id="dispatch.id"
    @updated="onPhotosChanged"
  />
</template>

<script setup>
import { computed } from 'vue'

const isMobile = computed(() => {
  return /Android|iPhone|iPad/i.test(navigator.userAgent)
})
</script>
```

### B. Di dalam PHP Controller

#### Create Activity dengan Photo Support
```php
<?php
// app/Http/Controllers/Driver/DispatchController.php

public function completeDispatch(Request $request, Dispatch $dispatch)
{
    // Update dispatch
    $dispatch->update(['status' => 'completed']);

    // Create activity log untuk foto
    $activity = ActivityLog::create([
        'user_id' => auth('ambulance')->id(),
        'action' => 'dispatch_completed',
        'model' => 'Dispatch',
        'model_id' => $dispatch->id,
        'description' => 'Dispatch selesai'
    ]);

    // Return activity ID agar frontend bisa upload foto
    return response()->json([
        'success' => true,
        'activity_id' => $activity->id,
        'message' => 'Dispatch selesai. Silakan upload laporan foto.'
    ]);
}
```

#### Get Activity with Photos
```php
public function getActivityReport($activityId)
{
    $activity = ActivityLog::with('photos')
        ->with('user')
        ->findOrFail($activityId);

    return response()->json([
        'id' => $activity->id,
        'action' => $activity->action,
        'description' => $activity->description,
        'photos' => $activity->photos->map(fn($p) => [
            'id' => $p->id,
            'url' => $p->photo_url,
            'name' => $p->photo_name,
            'description' => $p->description,
            'created_at' => $p->created_at,
        ]),
        'photo_count' => $activity->photos->count(),
    ]);
}
```

---

## 📡 API Reference

### 1. Upload Photo
**Endpoint**: `POST /api/activities/{activity_id}/photos`

**Request:**
```bash
curl -X POST http://localhost/api/activities/1/photos \
  -H "Authorization: Bearer TOKEN" \
  -F "photo=@image.jpg" \
  -F "description=Foto bukti penyelesaian"
```

**Response (201):**
```json
{
  "success": true,
  "message": "Foto berhasil diunggah",
  "data": {
    "id": 1,
    "photo_url": "http://localhost/storage/activity-photos/1/123456_abc.jpg",
    "photo_name": "IMG_001.jpg",
    "description": "Foto bukti penyelesaian",
    "sequence": 1,
    "file_size": 1024000,
    "created_at": "2026-04-08T10:30:00Z"
  }
}
```

### 2. List Photos
**Endpoint**: `GET /api/activities/{activity_id}/photos`

**Response:**
```json
{
  "success": true,
  "data": {
    "photos": [
      {
        "id": 1,
        "photo_url": "...",
        "photo_name": "IMG_001.jpg",
        "description": "Foto 1",
        "sequence": 1,
        "file_size": 1024000,
        "created_at": "2026-04-08T10:30:00Z"
      },
      // ... more photos
    ],
    "total_photos": 3,
    "max_photos": 5,
    "can_add_more": true
  }
}
```

### 3. Check Status
**Endpoint**: `GET /api/activities/{activity_id}/photos/status`

**Response:**
```json
{
  "success": true,
  "data": {
    "total_photos": 3,
    "max_photos": 5,
    "can_add_more": true,
    "remaining_slots": 2
  }
}
```

### 4. Update Description
**Endpoint**: `PATCH /api/photos/{photo_id}`

**Request:**
```json
{
  "description": "Deskripsi foto yang diperbarui"
}
```

### 5. Delete Photo
**Endpoint**: `DELETE /api/photos/{photo_id}`

**Response:**
```json
{
  "success": true,
  "message": "Foto berhasil dihapus"
}
```

---

## 📂 File Structure

Semua file sudah terciptakan di struktur berikut:

```
damkar-dispatch/
├── app/
│   ├── Models/
│   │   ├── ActivityLog.php .................... ✅ Updated
│   │   └── ActivityPhoto.php ................. ✅ New
│   ├── Services/
│   │   └── ActivityPhotoService.php ........... ✅ New
│   └── Http/Controllers/
│       ├── Api/ActivityPhotoController.php .... ✅ New
│       └── Driver/DriverActivityController.php ✅ New
├── database/
│   ├── migrations/
│   │   └── 2026_04_08_create_activity_photos_table.php ✅ New
│   └── factories/
│       └── ActivityPhotoFactory.php ........... ✅ New
├── resources/js/components/
│   ├── ActivityPhotoUploader.vue ............. ✅ New (Mobile)
│   └── ActivityPhotoUploaderWeb.vue .......... ✅ New (Web)
├── routes/
│   └── web.php ............................. ✅ Updated
├── IMPLEMENTATION_SUMMARY.md ............... ✅ New
├── ACTIVITY_PHOTO_DOCUMENTATION.md ......... ✅ New
├── ACTIVITY_PHOTO_QUICKSTART.md ............ ✅ New
└── setup-activity-photos.sh ................ ✅ New (Executable)
```

---

## 🧪 Testing

### Test Upload Photo
```php
// tests/Feature/ActivityPhotoTest.php
test('can upload photo to activity', function () {
    $activity = ActivityLog::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->post(
        "/api/activities/{$activity->id}/photos",
        ['photo' => $file]
    );

    $response->assertStatus(201);
    $this->assertCount(1, $activity->refresh()->photos);
});
```

### Run Tests
```bash
# Run semua test
php artisan test

# Run test spesifik
php artisan test tests/Feature/ActivityPhotoTest.php

# Run dengan verbose
php artisan test --verbose
```

---

## 🔒 Keamanan

### Validasi File
- ✅ MIME Type: JPEG, PNG, GIF, WebP only
- ✅ File Size: Max 5MB
- ✅ Virus scan: (Optional, dapat ditambahkan)

### Validasi User
- ✅ Authentication required
- ✅ User ownership verification
- ✅ Activity log association check

### Database
- ✅ Foreign key constraints
- ✅ Cascade delete
- ✅ Indexed queries

---

## 🐛 Troubleshooting

### Problem: "Gagal menyimpan file"
**Solusi:**
```bash
# Check disk space
df -h

# Check permissions
chmod -R 775 storage/

# Check symlink
ls -la public/storage
```

### Problem: "Foto tidak ditampilkan"
**Solusi:**
```bash
# Recreate symlink
php artisan storage:link --force

# Clear cache
php artisan cache:clear

# Check APP_URL in .env
```

### Problem: "Camera tidak berfungsi (Mobile)"
**Solusi:**
1. Update `capacitor.config.ts`:
```json
{
  "plugins": {
    "Camera": {
      "photos": true,
      "saveToGallery": true
    }
  }
}
```

2. Update iOS permissions (`Info.plist`):
```xml
<key>NSCameraUsageDescription</key>
<string>App membutuhkan akses ke kamera untuk mengambil foto</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>App membutuhkan akses ke galeri untuk memilih foto</string>
```

3. Update Android permissions (`AndroidManifest.xml`):
```xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
```

### Problem: "Maksimal 5 foto sudah tercapai"
**Solusi:**
- Hapus foto lama terlebih dahulu
- Atau buat activity log baru

---

## 📊 Database Queries

### Get Activities with Photos
```php
$activities = ActivityLog::with('photos')
    ->where('user_id', auth()->id())
    ->get();
```

### Get Total Photos
```php
$totalPhotos = ActivityPhoto::where(
    'activity_log_id', 
    $activityId
)->count();
```

### Get Latest Photos
```php
$recentPhotos = ActivityPhoto::orderBy('created_at', 'desc')
    ->limit(20)
    ->get();
```

### Delete All Photos of Activity
```php
ActivityPhoto::where('activity_log_id', $activityId)
    ->delete();
```

---

## 📈 Performance

### Optimasi Query
```php
// ❌ N+1 Query (Slow)
$activities = ActivityLog::all();
foreach ($activities as $activity) {
    echo $activity->photos->count(); // Extra query!
}

// ✅ Eager Loading (Fast)
$activities = ActivityLog::with('photos')->get();
foreach ($activities as $activity) {
    echo $activity->photos->count(); // No extra query
}
```

### Optimasi Storage
- Foto Max: 5MB per file
- Format: JPEG (compressed), PNG
- Storage: SSD recommended untuk production

---

## 🎓 Dokumentasi Tambahan

- 📄 [IMPLEMENTATION_SUMMARY.md](../IMPLEMENTATION_SUMMARY.md) - Ringkasan implementasi
- 📄 [ACTIVITY_PHOTO_DOCUMENTATION.md](../ACTIVITY_PHOTO_DOCUMENTATION.md) - Dokumentasi lengkap
- 📄 [ACTIVITY_PHOTO_QUICKSTART.md](../ACTIVITY_PHOTO_QUICKSTART.md) - Panduan cepat

---

## ✅ Checklist Penggunaan

- [ ] Run migration: `php artisan migrate`
- [ ] Create symlink: `php artisan storage:link`
- [ ] Install Capacitor: `npm install @capacitor/camera`
- [ ] Add component ke Vue
- [ ] Create ActivityLog ketika ada aktivitas
- [ ] Dapatkan activity_id
- [ ] Pass ke component untuk upload foto
- [ ] Test functionality

---

## 🎯 Kesimpulan

Sistem laporan foto yang **complete, secure, dan production-ready** sudah diimplementasikan dengan:

✅ Complete backend dengan models, services, controllers  
✅ Complete frontend dengan components  
✅ Comprehensive documentation  
✅ Testing examples  
✅ Security best practices  
✅ Mobile & web support  

**Siap untuk digunakan!**

---

**Created**: 2026-04-08  
**Last Updated**: 2026-04-08  
**Status**: ✅ Production Ready
