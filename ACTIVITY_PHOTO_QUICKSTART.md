# Activity Photo System - Quick Start Guide

Panduan cepat implementasi sistem laporan foto untuk aktivitas driver.

## 📋 Checklist Implementasi

### Backend Setup
- [x] Model: `ActivityLog.php` (updated)
- [x] Model: `ActivityPhoto.php` (new)
- [x] Service: `ActivityPhotoService.php`
- [x] Controller: `Api/ActivityPhotoController.php`
- [x] Migration: `create_activity_photos_table.php`
- [x] Routes: Updated `routes/web.php`
- [x] Factory: `ActivityPhotoFactory.php`

### Frontend Setup
- [ ] Component: `ActivityPhotoUploader.vue` (mobile)
- [ ] Component: `ActivityPhotoUploaderWeb.vue` (web)
- [ ] Install: `@capacitor/camera` package

### Configuration
- [ ] Run migration: `php artisan migrate`
- [ ] Create storage link: `php artisan storage:link`
- [ ] Setup Capacitor permissions (iOS/Android)

---

## 🚀 Quick Start

### 1. Setup Backend (5 minutes)

```bash
# Run migrations
php artisan migrate

# Create storage symlink
php artisan storage:link

# Verify routes
php artisan route:list | grep activity-photos
```

### 2. Setup Frontend (Mobile)

```bash
# Install Capacitor Camera
npm install @capacitor/camera

# Sync with native platforms
npx cap sync

# For development
npm run dev
```

### 3. Add Component to View

**Option A: Mobile (Capacitor)**
```vue
<template>
  <div>
    <ActivityPhotoUploader 
      :activity-id="activityId"
      @updated="handleUpdate"
    />
  </div>
</template>

<script setup>
import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'
import { ref } from 'vue'

const activityId = ref(1)

const handleUpdate = (photos) => {
  console.log('Photos updated:', photos)
}
</script>
```

**Option B: Web (Drag & Drop)**
```vue
<template>
  <div>
    <ActivityPhotoUploaderWeb 
      :activity-id="activityId"
      @updated="handleUpdate"
    />
  </div>
</template>

<script setup>
import ActivityPhotoUploaderWeb from '@/components/ActivityPhotoUploaderWeb.vue'
import { ref } from 'vue'

const activityId = ref(1)

const handleUpdate = (photos) => {
  console.log('Photos updated:', photos)
}
</script>
```

---

## 💡 Usage Examples

### Example 1: Save Activity with Photos (Driver Completes Task)

```vue
<template>
  <div class="driver-dashboard">
    <div v-if="!taskCompleted">
      <button @click="completeTask">Selesaikan Tugas</button>
    </div>
    
    <div v-else>
      <h3>Laporan Tugas Selesai</h3>
      <ActivityPhotoUploader 
        :activity-id="activityId"
        @updated="onPhotosUpdated"
      />
      <button @click="submitReport" :disabled="!hasPhotos">
        Kirim Laporan
      </button>
    </div>
  </div>
</template>

<script setup>
import ActivityPhotoUploader from '@/components/ActivityPhotoUploader.vue'
import { ref } from 'vue'
import axios from 'axios'

const taskCompleted = ref(false)
const activityId = ref(null)
const photos = ref([])

const completeTask = async () => {
  try {
    const response = await axios.post('/api/driver/activities/log', {
      dispatch_id: 123,
      notes: 'Task completed successfully'
    })
    
    activityId.value = response.data.activity_id
    taskCompleted.value = true
  } catch (error) {
    console.error('Failed to complete task:', error)
  }
}

const onPhotosUpdated = (updatedPhotos) => {
  photos.value = updatedPhotos
}

const submitReport = async () => {
  try {
    await axios.post('/api/driver/reports', {
      activity_id: activityId.value,
      status: 'submitted'
    })
    console.log('Report submitted successfully')
  } catch (error) {
    console.error('Failed to submit report:', error)
  }
}
</script>
```

### Example 2: View Activity History with Photos

```vue
<template>
  <div class="activity-history">
    <h2>Riwayat Aktivitas</h2>
    
    <div v-for="activity in activities" :key="activity.id" class="activity-card">
      <h4>{{ activity.action }}</h4>
      <p>{{ activity.description }}</p>
      
      <!-- Photos section -->
      <div v-if="activity.photo_count > 0" class="photos-grid">
        <img 
          v-for="photo in activity.photos" 
          :key="photo.id"
          :src="photo.photo_url"
          :alt="photo.photo_name"
          class="photo-thumbnail"
        />
      </div>
      
      <!-- No photos -->
      <p v-else class="text-gray-500">Tidak ada foto</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const activities = ref([])

onMounted(async () => {
  try {
    const response = await axios.get('/api/driver/activities')
    activities.value = response.data.data
  } catch (error) {
    console.error('Failed to load activities:', error)
  }
})
</script>
```

### Example 3: Create Activity Manually (Admin)

```php
// In Controller
public function storeActivity(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users',
        'action' => 'required|string',
        'description' => 'nullable|string'
    ]);

    $activity = ActivityLog::create($validated);

    return response()->json([
        'success' => true,
        'activity_id' => $activity->id,
        'message' => 'Aktivitas berhasil dicatat'
    ], 201);
}
```

---

## 🧪 Testing

### Unit Test Example

```php
// tests/Feature/ActivityPhotoTest.php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ActivityPhotoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_upload_photo_to_activity()
    {
        $activity = ActivityLog::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg', 640, 480);

        $response = $this->post(
            "/api/activities/{$activity->id}/photos",
            ['photo' => $file]
        );

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $this->assertCount(1, $activity->photos);
    }

    /** @test */
    public function cannot_exceed_max_photos()
    {
        $activity = ActivityLog::factory()->create();
        
        // Create 5 photos
        for ($i = 0; $i < 5; $i++) {
            ActivityPhoto::factory()
                ->for($activity, 'activityLog')
                ->withSequence($i + 1)
                ->create();
        }

        $file = UploadedFile::fake()->image('test.jpg');
        $response = $this->post(
            "/api/activities/{$activity->id}/photos",
            ['photo' => $file]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function can_delete_photo()
    {
        $photo = ActivityPhoto::factory()->create();

        $response = $this->delete("/api/photos/{$photo->id}");

        $response->assertStatus(200);
        $this->assertNull(ActivityPhoto::find($photo->id));
    }

    /** @test */
    public function can_update_photo_description()
    {
        $photo = ActivityPhoto::factory()->create();
        $newDescription = 'Updated description';

        $response = $this->patch("/api/photos/{$photo->id}", [
            'description' => $newDescription
        ]);

        $response->assertStatus(200);
        $this->assertEquals(
            $newDescription,
            ActivityPhoto::find($photo->id)->description
        );
    }
}
```

Run tests:
```bash
php artisan test tests/Feature/ActivityPhotoTest.php
```

---

## 📁 File Permissions

Pastikan permission folder storage sudah benar:

```bash
# Set folder permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Or with Docker
docker exec app-container chmod -R 775 storage/
```

---

## 🔒 Security Notes

✅ **Implemented**
- MIME type validation
- File size limit (5MB)
- User ownership verification
- Foreign key constraints

⚠️ **Additional Considerations**
- Setup rate limiting for uploads
- Monitor disk usage
- Regular backup of storage
- Consider S3/Cloud storage for production

---

## 📊 Database Queries

### Get Activities with Photo Count
```php
$activities = ActivityLog::withCount('photos')
    ->where('user_id', auth()->id())
    ->get();
```

### Get Activities with Photos
```php
$activities = ActivityLog::with('photos')
    ->where('user_id', auth()->id())
    ->get();
```

### Get Recent Photos
```php
$recentPhotos = ActivityPhoto::orderBy('created_at', 'desc')
    ->limit(20)
    ->get();
```

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Photos not saving | Check disk space & permissions |
| Images not displaying | Verify storage symlink exists |
| Camera not working on mobile | Check app permissions in iOS/Android settings |
| Upload too slow | Consider image compression |
| Database full | Archive old photos or migrate to cloud |

---

## 📈 Next Steps

1. ✅ Create activity logs for each driver action
2. ✅ Add photo upload to dispatch completion
3. ✅ Create activity history view
4. ✅ Generate PDF reports with photos
5. ✅ Add activity notifications
6. ✅ Implement activity filtering & search

---

## 📚 Documentation

Full documentation: [ACTIVITY_PHOTO_DOCUMENTATION.md](ACTIVITY_PHOTO_DOCUMENTATION.md)

API Reference: See routes in `routes/web.php`
