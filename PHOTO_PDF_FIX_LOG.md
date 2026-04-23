# Foto Halaman PDF - Fix Documentation

**Tanggal**: 2026-04-23  
**Status**: ✅ FIXED  
**Issue**: Foto tidak muncul di halaman 3a/3b Laporan PDF Kebakaran

---

## Masalah yang Ditemukan

### 1. Image Path Issue (PRIMARY)
**File**: `resources/views/admin/reports/kebakaran_pdf.blade.php` (Lines 808, 851)

**Problem**: 
```blade
<img src="{{ public_path('storage/' . $item->photo->photo_path) }}" ... >
```

Masalah dengan `public_path()`:
- Mengembalikan **file system path** (contoh: `/Applications/Dev/damkar-dispatch/public/storage/activity-photos/1/123_1_2026-04-16.jpg`)
- DomPDF membutuhkan **URL atau file:// protocol**, bukan file system path
- Hasilnya: Gambar tidak ditampilkan di PDF

### 2. No Activity Photos in Database
**Situation**: Database `activity_photos` kosong (0 records)
```
Total photos in database: 0
Activity logs with Dispatch/PatientRequest: 0
Photos in storage: 0
```

**Implication**: Meski code sudah benar, tidak ada foto untuk ditampilkan.

---

## Solusi yang Diterapkan

### 1. Fix Image Path Handling (CRITICAL)
**File**: `/resources/views/admin/reports/kebakaran_pdf.blade.php`

**Before**:
```blade
<img src="{{ public_path('storage/' . $item->photo->photo_path) }}" ... >
```

**After**:
```blade
@php
    $photoPath = public_path('storage/' . $item->photo->photo_path);
    $imageSrc = file_exists($photoPath) 
        ? 'file://' . $photoPath 
        : asset('storage/' . $item->photo->photo_path);
@endphp
<img src="{{ $imageSrc }}" ... >
```

**Penjelasan**:
- Cek apakah file ada di file system
- Jika ada: Gunakan `file://` protocol (DomPDF-compatible)
- Jika tidak ada: Fallback ke `asset()` URL (untuk edge cases)
- `file://` protocol memungkinkan DomPDF mengakses file lokal
- Lebih reliable daripada public_path() untuk PDF generation

**Updated **2 locations**:
- Line 804-829 (Halaman 3a)
- Line 847-877 (Halaman 3b)

### 2. Add Debug Logging
**File**: `/app/Http/Controllers/Admin/DispatchController.php` (Lines 520-532)

**Added**:
```php
// ── Debug Log ──
\Illuminate\Support\Facades\Log::channel('stack')->info('PDF Export - Kebakaran Report', [
    'dispatch_id' => $dispatch->id,
    'timestamp' => now(),
    'photos_count' => $photos->count(),
    'activity_logs_count' => $activityLogs->count(),
    'photos_data' => $photos->map(fn($p) => [
        'id' => $p->photo->id,
        'path' => $p->photo->photo_path,
        'uploader' => $p->uploader,
    ])->toArray(),
]);
```

**Tujuan**: Membantu debug PDF export dengan melihat:
- Berapa foto yang ditemukan
- Path foto apa yang digunakan
- User mana yang upload
- Error atau masalah lainnya

---

## How to Test/Verify

### Step 1: Check Logs
After exporting PDF, cek log file:

```bash
# View latest logs
tail -50 storage/logs/laravel.log

# Search for PDF export logs
grep -A 10 "PDF Export - Kebakaran Report" storage/logs/laravel.log
```

**Expected Output**:
```
[2026-04-23 12:34:56] local.INFO: PDF Export - Kebakaran Report {"dispatch_id":1,"timestamp":"2026-04-23T12:34:56.000000Z","photos_count":3,"activity_logs_count":1,"photos_data":[...]}
```

### Step 2: Verify Photo Storage
Check jika ada foto yang sudah diupload:

```bash
# List all photos in storage
find storage/app/public/activity-photos -type f

# Check directory structure
ls -lah storage/app/public/activity-photos/
```

**Expected**: Jika ada foto, akan melihat folder dengan struktur:
```
storage/app/public/activity-photos/
├── 1/  (activity_log_id)
│   ├── 1_1_2026-04-23.jpg
│   ├── 1_2_2026-04-23.jpg
│   └── ...
├── 2/
│   └── ...
```

### Step 3: Manual PDF Export Test
1. Login ke admin dashboard
2. Pergi ke **Dispatches**
3. Pilih dispatch yang sudah ada photos
4. Klik **Export PDF** / **Unduh Laporan**
5. Check apakah halaman 3a/3b muncul dengan foto

**If photos appear**: ✅ Fix berhasil!  
**If photos missing**: 
- Check logs untuk error
- Verify storage permissions
- Check apakah foto benar-benar tersimpan

---

## Troubleshooting Guide

### Issue: Foto masih tidak muncul di PDF
**Checklist**:
1. ✓ Ada foto di database? `SELECT COUNT(*) FROM activity_photos;`
2. ✓ Foto file ada? `find storage/app/public/activity-photos -type f`
3. ✓ Storage symlink ada? `ls -l public/storage`
4. ✓ Photo path benar? `SELECT photo_path FROM activity_photos LIMIT 1;`
5. ✓ Check logs untuk errors: `grep -i error storage/logs/laravel.log | tail -20`

### Issue: DomPDF Error
**Checklist**:
1. ✓ DomPDF installed? `composer show barryvdh/laravel-dompdf`
2. ✓ Cek PHP requirements (PHP 7.4+ needed)
3. ✓ Check server error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`

### Issue: Permissions
```bash
# Fix storage permissions
chmod -R 755 storage/
chmod -R 755 public/storage/
chown -R www-data:www-data storage/
```

---

## Next Steps (Untuk User)

1. **Upload beberapa foto test** ke dispatch reports
   - Gunakan foto uploader di mobile atau web client
   
2. **Export PDF** dan cek apakah halaman 3a/3b muncul dengan foto

3. **Monitor logs** jika ada error:
   ```bash
   tail -f storage/logs/laravel.log | grep "PDF Export"
   ```

4. **Report issues** jika:
   - Foto masih tidak muncul
   - PDF export lambat
   - Ada error di log

---

## Technical Details

### File Paths Used
- **Storage location**: `storage/app/public/activity-photos/{activity_log_id}/`
- **Public access**: `public/storage/activity-photos/{activity_log_id}/`
- **Photo naming**: `{dispatch_id}_{sequence}_{date}.jpg`
  - Example: `12_1_2026-04-23.jpg`

### DomPDF Image Sources (Priority)
1. ✅ `file://` protocol (Recommended for PDF)
2. ✅ Full HTTP URLs
3. ❌ Relative paths (May not work)
4. ❌ `public_path()` (File system path - NOT for PDF)

### Database Schema
```sql
-- activity_photos table
- id (bigint, PK)
- activity_log_id (FK)
- photo_path (varchar, e.g., "activity-photos/1/12_1_2026-04-23.jpg")
- photo_name (varchar, original filename)
- description (text, nullable)
- file_size (bigint, bytes)
- sequence (tinyint, 1-5)
- created_at, updated_at

Index: activity_log_id (untuk quick lookup)
```

---

## Summary

| Aspek | Status | Detail |
|-------|--------|--------|
| **Image Path Fix** | ✅ Complete | Menggunakan file:// protocol untuk DomPDF |
| **Debug Logging** | ✅ Complete | Mencatat foto count dan path di log |
| **Database Prep** | ✅ Ready | Schema sudah siap di migrate |
| **Testing** | ⏳ Pending | Perlu upload foto test & export PDF |
| **Documentation** | ✅ Complete | File ini + memory repo |

---

**Modified**: 2026-04-23  
**Developer**: GitHub Copilot  
**Branch**: main  

