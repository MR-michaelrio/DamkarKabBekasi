# Activity Photo Reports Documentation

Dokumentasi lengkap untuk sistem laporan foto aktivitas dengan fitur capture/galeri

## Fitur Utama

✅ **Maximum 5 Photos Per Activity** - Batasan maksimal 5 foto per laporan aktivitas
✅ **Camera & Gallery Support** - Ambil foto langsung atau pilih dari galeri
✅ **Photo Management** - Upload, delete, dan edit deskripsi foto
✅ **Real-time Progress** - Indikator progress saat upload
✅ **Responsive Design** - Bekerja di mobile dan desktop
✅ **Capacitor Integration** - Support untuk aplikasi Capacitor

---

## Arsitektur Sistem

### Database Schema

#### activity_photos table
```sql
CREATE TABLE activity_photos (
  id bigint PRIMARY KEY AUTO_INCREMENT,
  activity_log_id bigint NOT NULL (FK),
  photo_path varchar(255) NOT NULL,
  photo_name varchar(255) NOT NULL,
  mime_type varchar(50) DEFAULT 'image/jpeg',
  file_size bigint DEFAULT 0,
  description text NULL,
  sequence tinyint DEFAULT 1,
  created_at timestamp,
  updated_at timestamp,
  INDEX (activity_log_id),
  INDEX (created_at)
)
```

### Model Structure

#### ActivityLog Model
```php
class ActivityLog extends Model {
    // Relationships
    public function photos(): HasMany { ... }
    public function user(): BelongsTo { ... }
    
    // Helper methods
    public function hasMaxPhotos(): bool { ... }
    public function getPhotoCountAttribute(): int { ... }
}
```

#### ActivityPhoto Model
```php
class ActivityPhoto extends Model {
    // Relationships
    public function activityLog(): BelongsTo { ... }
    
    // Accessors
    public function getPhotoUrlAttribute(): string { ... }
}
```

---

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Install Capacitor Plugin (untuk mobile)

```bash
npm install @capacitor/camera
npx cap sync
```

### 3. Configure Storage

Pastikan symbolic link sudah dibuat:
```bash
php artisan storage:link
```

Update `config/filesystems.php` jika diperlukan:
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'path' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## API Endpoints

### 1. Upload Photo
**POST** `/api/activities/{activity_id}/photos`

**Request:**
```multipart
photo: File (max 5MB)
description: string (optional, max 255 chars)
```

**Response (201):**
```json
{
  "success": true,
  "message": "Foto berhasil diunggah",
  "data": {
    "id": 1,
    "photo_url": "http://app.com/storage/activity-photos/1/123456.jpg",
    "photo_name": "IMG_001.jpg",
    "description": "Deskripsi foto",
    "sequence": 1,
    "file_size": 1024000,
    "created_at": "2026-04-08 10:30:00"
  }
}
```

### 2. Get All Photos
**GET** `/api/activities/{activity_id}/photos`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "photos": [...],
    "total_photos": 3,
    "max_photos": 5,
    "can_add_more": true
  }
}
```

### 3. Get Upload Status
**GET** `/api/activities/{activity_id}/photos/status`

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

### 4. Update Photo Description
**PATCH** `/api/photos/{photo_id}`

**Request:**
```json
{
  "description": "Deskripsi baru"
}
```

### 5. Delete Photo
**DELETE** `/api/photos/{photo_id}`

---

## Vue Component Usage

### For Mobile (Capacitor)

```vue
<template>
  <div class="container">
    <ActivityPhotoUploader 
      :activity-id="activityId"
      :max-photos="5"
      @updated="onPhotosUpdated"
      @error="onPhotoError"
    />
  </div>
</template>

<script setup>
import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'

const activityId = ref(1)

const onPhotosUpdated = (photos) => {
  console.log('Photos updated:', photos)
}

const onPhotoError = (error) => {
  console.error('Photo error:', error)
}
</script>
```

### For Web

```vue
<template>
  <div class="container">
    <ActivityPhotoUploaderWeb 
      :activity-id="activityId"
      :max-photos="5"
      @updated="onPhotosUpdated"
      @error="onPhotoError"
    />
  </div>
</template>

<script setup>
import ActivityPhotoUploaderWeb from '@/components/ActivityPhotoUploaderWeb.vue'

const activityId = ref(1)

const onPhotosUpdated = (photos) => {
  console.log('Photos updated:', photos)
}

const onPhotoError = (error) => {
  console.error('Photo error:', error)
}
</script>
```

### Auto-detect (Mobile or Web)

```vue
<template>
  <div class="container">
    <component 
      :is="isMobile ? ActivityPhotoUploader : ActivityPhotoUploaderWeb"
      :activity-id="activityId"
      :max-photos="5"
      @updated="onPhotosUpdated"
      @error="onPhotoError"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'
import ActivityPhotoUploaderWeb from '@/components/ActivityPhotoUploaderWeb.vue'

const activityId = ref(1)
const isMobile = computed(() => {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
    navigator.userAgent
  )
})

const onPhotosUpdated = (photos) => {
  console.log('Photos updated:', photos)
}

const onPhotoError = (error) => {
  console.error('Photo error:', error)
}
</script>
```

---

## Integration with Driver Dashboard

### Example: Adding to Driver Activity

**File:** `resources/views/driver/dashboard.blade.php`

```blade
<div class="mt-6">
  <h3 class="text-lg font-semibold mb-4">Laporan Foto Aktivitas</h3>
  
  <!-- Vue Component akan di-mount di sini -->
  <div id="photo-uploader"></div>
</div>

@push('scripts')
<script>
  // Component akan di-import dan mounted di Vue app
  import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'
  
  // Activity ID bisa didapat dari dispatch ID atau user ID
  const activityId = {{ $activity->id ?? 1 }};
</script>
@endpush
```

---

## Usage in Activity Logging

### Creating an Activity with Photos

**File:** `app/Http/Controllers/Driver/DriverDashboardController.php`

```php
public function updateStatus(Request $request, Dispatch $dispatch)
{
    $validated = $request->validate([
        'status' => 'required|string',
        'notes' => 'nullable|string'
    ]);

    // Update dispatch status
    $dispatch->update([
        'status' => $validated['status'],
        // ... other fields
    ]);

    // Create activity log
    $activity = ActivityLog::create([
        'user_id' => auth('ambulance')->id(),
        'action' => 'dispatch_status_change',
        'model' => 'Dispatch',
        'model_id' => $dispatch->id,
        'description' => "Status changed to: {$validated['status']}"
    ]);

    // Return activity ID for photo upload
    return response()->json([
        'success' => true,
        'activity_id' => $activity->id,
        'message' => 'Status updated successfully'
    ]);
}
```

### Getting Activity with Photos

```php
// Get activity with all photos
$activity = ActivityLog::with('photos')->find($activityId);

// Access photos
foreach ($activity->photos as $photo) {
    echo $photo->photo_url; // Full URL
    echo $photo->description;
    echo $photo->sequence;
}

// Check photo count
$photoCount = $activity->photo_count;
$hasMaxPhotos = $activity->hasMaxPhotos();
```

---

## File Structure

```
app/
├── Models/
│   ├── ActivityLog.php (updated)
│   └── ActivityPhoto.php (new)
├── Services/
│   └── ActivityPhotoService.php (new)
├── Http/Controllers/Api/
│   └── ActivityPhotoController.php (new)
database/
├── migrations/
│   └── 2026_04_08_create_activity_photos_table.php (new)
resources/
└── js/components/
    ├── ActivityPhotoUploader.vue (new - mobile)
    └── ActivityPhotoUploaderWeb.vue (new - web)
routes/
└── web.php (updated)
```

---

## Security Considerations

### File Upload Validation
- ✅ MIME type validation (JPEG, PNG, GIF, WebP)
- ✅ File size limit (5MB max)
- ✅ User ownership verification
- ✅ Storage isolation by activity ID

### Authorization
- ✅ User ownership check before upload/delete
- ✅ Guard for both `auth:web,ambulance`
- ✅ Activity log ownership validation

### Database
- ✅ Foreign key constraints (cascade on delete)
- ✅ Soft deletes (via activity_log deletion)
- ✅ Indexed queries for performance

---

## Error Handling

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| "Maksimal X foto sudah tercapai" | User exceeded 5 photos | Delete old photos first |
| "Ukuran file terlalu besar" | File > 5MB | Compress image or reduce size |
| "Tipe file tidak didukung" | Wrong format | Use JPG, PNG, GIF, or WebP |
| "Gagal menyimpan file" | Disk space issue | Check server storage |
| "Tidak terautentikasi" | User not logged in | Login first |

---

## Testing

### Unit Tests

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
    $this->assertCount(1, $activity->photos);
});

test('cannot exceed max photos', function () {
    $activity = ActivityLog::factory()->create();
    
    // Create 5 photos
    for ($i = 0; $i < 5; $i++) {
        ActivityPhoto::factory()->create(['activity_log_id' => $activity->id]);
    }

    $file = UploadedFile::fake()->image('test.jpg');
    $response = $this->post(
        "/api/activities/{$activity->id}/photos",
        ['photo' => $file]
    );

    $response->assertStatus(400);
});
```

---

## Troubleshooting

### Photos not saving?
1. Check disk space: `df -h`
2. Verify storage permissions: `chmod -R 775 storage/`
3. Check storage link: `php artisan storage:link`

### Images not displaying?
1. Verify storage symlink: `ls -la public/storage`
2. Check APP_URL in `.env`
3. Clear app cache: `php artisan cache:clear`

### Capacitor camera not working?
1. Add to `capacitor.config.ts`:
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
2. Update permissions in `ios/App/App/Info.plist` and `android/app/src/main/AndroidManifest.xml`

---

## Performance Optimization

### Database Queries

```php
// Efficient: Load with photos
$activities = ActivityLog::with('photos')->get();

// Inefficient: N+1 queries
$activities = ActivityLog::all();
foreach ($activities as $activity) {
    $photos = $activity->photos; // Extra query!
}
```

### Image Optimization

```php
// In service class
$image = Image::make($file)->resize(1920, 1080, function ($constraint) {
    $constraint->aspectRatio();
    $constraint->upsize();
})->encode('jpg', 85); // Quality 85%
```

---

## Future Enhancements

- [ ] Image compression/optimization
- [ ] Bulk photo upload
- [ ] Photo cropping tool
- [ ] OCR for document scanning
- [ ] Photo filtering by date range
- [ ] Export photos as ZIP
- [ ] Integration with cloud storage (S3, Azure)
- [ ] WebP format support
- [ ] Photo thumbnails cache

---

## Support & Contact

Untuk pertanyaan atau issues, silakan hubungi developer atau cek dokumentasi internal.
