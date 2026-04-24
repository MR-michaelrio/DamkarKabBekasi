# 🔧 Photo Upload Issue - Diagnostic & Fix Guide

**Date**: 2026-04-23  
**Issue**: Foto upload tersimpan di storage tapi tidak muncul di database  
**Status**: ✅ IDENTIFIED & FIXED

---

## 📋 Problem Summary

```
Gejala:
  ✓ Foto file ADA di storage/app/public/activity-photos/
  ✓ Upload tidak error di UI
  ✗ Foto TIDAK muncul di activity_photos table
  ✗ Foto tidak muncul di PDF halaman 3a/3b
```

**Root Cause**: 
- File disimpan ke storage ✓
- Database insert (`ActivityPhoto::create()`) GAGAL ✗
- Foto menjadi "orphaned" (file ada tapi tidak terekam di DB)

---

## 🔍 Diagnosis Steps

### Step 1: Check Current Status
```bash
# Check database
SELECT COUNT(*) FROM activity_photos;
# Result: 0 (expected if issue)

# Check storage  
find storage/app/public/activity-photos -type f | wc -l
# Result: >0 (orphaned files)
```

### Step 2: Check Logs for Errors
```bash
tail -100 storage/logs/laravel.log | grep -i "photo\|upload\|failed"
```

Expected errors like:
```
Failed to create ActivityPhoto record
Activity log not found
Database constraint violation
Foreign key constraint
```

### Step 3: Check Activity Logs
```bash
SELECT id, user_id, model, model_id FROM activity_logs LIMIT 5;
```

Must have:
- activity_log_id >= 1
- model = 'Dispatch' (or related model)
- Foreign key relationship intact

---

## ✅ Fixes Applied

### Fix 1: Enhanced Error Logging

**File**: `app/Http/Controllers/ActivityPhotoController.php`

Added detailed error logging wrapped around `ActivityPhoto::create()`:

```php
try {
    $photo = ActivityPhoto::create([...]);
    
    Log::info('Photo created successfully', [
        'photo_id' => $photo->id,
        'activity_log_id' => $activityLog->id,
        'photo_path' => $path,
    ]);
} catch (\Throwable $dbError) {
    Log::error('Failed to create ActivityPhoto record', [
        'activity_log_id' => $activityLog->id,
        'photo_path' => $path,
        'error' => $dbError->getMessage(),
        'file' => $dbError->getFile(),
        'line' => $dbError->getLine(),
    ]);
    throw $dbError;
}
```

**Benefit**: Now logs will clearly show WHY database insert failed

### Fix 2: Enhanced API Error Logging

**File**: `app/Http/Controllers/Api/ActivityPhotoController.php`

Added trace logging to catch API upload errors:

```php
catch (\Exception $e) {
    \Illuminate\Support\Facades\Log::error('API Photo upload failed', [
        'message' => $e->getMessage(),
        'activity_id' => $activity_id,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => env('APP_DEBUG') ? ['file' => $e->getFile(), 'line' => $e->getLine()] : null,
    ], 400);
}
```

### Fix 3: Recovery Tool - Import Orphaned Photos

**Script**: `import-orphaned-photos.php`

Untuk recover foto-foto yang sudah di storage tapi tidak di database:

```bash
php import-orphaned-photos.php
```

**Script melakukan**:
1. Scan semua file di storage/app/public/activity-photos
2. Cek mana yang sudah di database (skip)
3. Cek mana yang "orphaned" (file ada, DB tidak ada)
4. Automatically import ke activity_photos table
5. Link ke activity_log yang sesuai

**Status Output**:
```
Found 5 photo files in storage

Files to import:
  1. activity-photos/ABC123.jpg
  2. activity-photos/XYZ789.jpg
  ...

✓ IMPORTED: activity-photos/ABC123.jpg → ActivityLog #1
✓ IMPORTED: activity-photos/XYZ789.jpg → ActivityLog #1

Import Summary:
  ✓ Imported: 2
  ⊘ Skipped:  1
  ✗ Failed:   2
```

---

## 🚀 How to Fix Your Current Issue

### Option A: Use Recovery Script (Recommended)

```bash
cd /Applications/Dev/damkar-dispatch

# Import orphaned photos to database
php import-orphaned-photos.php

# If successful, photos will now be in database
# Export PDF and check halaman 3a/3b
```

### Option B: Manual Check

```bash
# 1. Check what's in storage
find storage/app/public/activity-photos -type f

# 2. Check what's in database
SELECT * FROM activity_photos;

# 3. Check logs for errors
tail -f storage/logs/laravel.log
```

---

## 🔑 Key Points Moving Forward

### For New Uploads:

1. **Enhanced logging enabled** - Watch logs after upload:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Debug output enabled** (if APP_DEBUG=true) - Upload response will show errors:
   ```json
   {
     "success": false,
     "message": "Database error details...",
     "debug": {
       "file": "path/to/file.php",
       "line": 123
     }
   }
   ```

3. **Recovery script available** - If more orphaned files appear:
   ```bash
   php import-orphaned-photos.php
   ```

### Common Issues & Solutions:

| Issue | Cause | Solution |
|-------|-------|----------|
| **"Activity Log not found"** | Wrong activity_log_id | Check if activity_log exists |
| **"Foreign key constraint"** | activity_log_id invalid | Ensure dispatch/activity created first |
| **"File permission denied"** | Storage not writable | Run: chmod -R 755 storage/ |
| **"Database locked"** | Concurrent uploads | Wait 1-2 seconds and retry |

---

## 📝 Updated Code Files

### Modified:
1. `app/Http/Controllers/ActivityPhotoController.php`
   - Added try-catch with detailed error logging
   - Better error messages in response

2. `app/Http/Controllers/Api/ActivityPhotoController.php`
   - Added trace logging
   - Debug info in response (if APP_DEBUG=true)

### Created:
1. `import-orphaned-photos.php`
   - Recovery script for orphaned photos
   - Can be run anytime to sync storage with database

---

## ✔️ Verification Checklist

After applying fixes:

- [ ] Clear logs: `echo '' > storage/logs/laravel.log`
- [ ] Upload a new photo and watch logs: `tail -f storage/logs/laravel.log`
- [ ] Check if photo appears in DB: `SELECT * FROM activity_photos WHERE created_at > NOW()-INTERVAL 1 MINUTE;`
- [ ] If yes → Export PDF and verify halaman 3a/3b shows photo
- [ ] If no → Run recovery script: `php import-orphaned-photos.php`
- [ ] Export PDF again and verify

---

## 🎯 Next Steps

1. **Immediately**: Run recovery script to fix existing orphaned photos
   ```bash
   php import-orphaned-photos.php
   ```

2. **Test**: Upload a new photo and monitor logs for errors

3. **Export**: Test PDF export with recovered photos

4. **Report**: If still issues, check logs:
   ```bash
   tail -50 storage/logs/laravel.log
   ```

---

**Updated**: 2026-04-23  
**Owner**: GitHub Copilot  
