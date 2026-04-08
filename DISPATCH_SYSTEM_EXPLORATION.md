# Damkar Dispatch System - Complete Exploration Report

**Date**: April 8, 2026  
**Project**: Damkar Dispatch System  
**Focus**: Database schema, models, status flows, and history tracking

---

## EXECUTIVE SUMMARY

The Damkar dispatch system is a Laravel-based emergency response coordination platform with comprehensive request tracking, real-time GPS monitoring, and multi-layer history documentation. The system maintains complete audit trails through **DispatchLog** (status history), **DispatchLocationHistory** (GPS tracking), **ActivityLog** (driver activity), and timestamp fields on the Dispatch model itself.

---

## 1. CORE MODEL RELATIONSHIPS

### Model Hierarchy

```
PatientRequest (incoming public requests)
    ↓
Dispatch (unit assignments)
    ├── Driver
    ├── Ambulance
    ├── EventRequest (for event/disaster response)
    ├── DispatchLog (status history)
    └── DispatchLocationHistory (GPS tracking)

ActivityLog (driver session tracking)
    └── ActivityPhoto (up to 5 photos per activity)
```

### Key Models

#### **Dispatch** (Core tracking model)
- **Primary Key**: id
- **Status**: enum (pending → on_the_way_scene → on_scene → on_the_way_kantor_pos → completed)
- **Timestamps**: assigned_at, otw_scene_at, pickup_at, hospital_at, completed_at
- **Relations**: Driver, Ambulance, PatientRequest, EventRequest, DispatchLog, DispatchLocationHistory
- **Soft Delete**: Yes (deleted_at)

#### **PatientRequest** (Initial request from public)
- **Status**: pending → dispatched → completed → rejected
- **Service Type**: ambulance, jenazah (corpse transport)
- **Patient Condition**: emergency, kontrol
- **TTS URL**: Text-to-Speech audio link for alert broadcast
- **Linked to Dispatch**: Via dispatch_id foreign key

#### **DispatchLog** (Status history tracking)
- **Purpose**: Records every status change with timestamp and note
- **Created**: Automatically on each status transition
- **Key Fields**: dispatch_id, status, note, created_at

#### **DispatchLocationHistory** (GPS tracking)
- **Purpose**: Records GPS coordinates during active dispatch
- **Trigger**: API endpoint receives location update
- **Active Only**: Saved only when dispatch status in [pending, on_the_way_scene, on_scene, on_the_way_kantor_pos]
- **Key Fields**: dispatch_id, latitude, longitude, created_at

#### **ActivityLog** (Driver session tracking)
- **Actions**: dispatch_in_progress, driver_login
- **Linked To**: Driver (user_id), Dispatch (model_id)
- **Photos**: Has many ActivityPhoto (max 5 per log)

#### **Driver**
- **Status**: available, on_duty
- **Location**: latitude, longitude, last_seen
- **Region**: pleton (sub-unit assignment)

#### **Ambulance**
- **Status**: ready, on_duty
- **Location**: latitude, longitude (real-time during dispatch)
- **Auth**: Username/password for ambulance login
- **Plate Number**: Vehicle identifier

---

## 2. DISPATCH STATUS FLOW

### Complete Status Progression

```
┌─────────────┐
│   pending   │  ← Dispatch created, ambulance assigned
└──────┬──────┘
       │ Driver clicks "🚀 OTW ke TKP"
       │ otw_scene_at = now()
       ↓
┌─────────────────────┐
│ on_the_way_scene   │  ← En route to incident scene
└──────┬──────────────┘
       │ Driver clicks "📍 Sampai di TKP"
       │ pickup_at = now()
       ↓
┌──────────┐
│ on_scene │  ← At scene, performing response
└──────┬───┘
       │ Driver clicks "🚒 OTW MAKO / POS"
       │ hospital_at = now()
       ↓
┌──────────────────────────┐
│ on_the_way_kantor_pos   │  ← Returning to base
└──────┬───────────────────┘
       │ Driver clicks "🏁 Selesai"
       │ completed_at = now()
       ↓
┌──────────────┐
│  completed   │  ← Dispatch finished
└──────────────┘    - Ambulance set to "ready"
                    - Driver set to "available"
                    - Location cleared
                    - PatientRequest status → "completed"
```

### Status Values in Database

```sql
-- MySQL ENUM
ENUM('pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos', 'completed', 'cancelled')
```

### Additional Statuses

- **on_duty**: For event/disaster deployments (different flow)
- **cancelled**: Manual cancellation by admin

### Driver Pause/Resume

- **Route**: POST /driver/dispatches/{dispatch}/toggle-pause
- **Effect**: Disables status update buttons visually
- **DispatchLog Entry**: Created with reason ("Driver sedang istirahat" or "Driver melanjutkan perjalanan")
- **Not Final**: Pause doesn't affect dispatch status, just workflow control

---

## 3. DATABASE SCHEMA

### Dispatches Table

```sql
CREATE TABLE dispatches (
    id BIGINT PRIMARY KEY,
    patient_name VARCHAR(255),
    request_date DATE,
    pickup_time VARCHAR(255),
    patient_condition ENUM('kebakaran', 'rescue', 'jenazah', 'kontrol', 'emergency'),
    patient_phone VARCHAR(20),
    pickup_address TEXT,
    destination TEXT,
    driver_id BIGINT FOREIGN KEY,
    ambulance_id BIGINT FOREIGN KEY,
    status ENUM(...),
    is_paused BOOLEAN DEFAULT 0,
    assigned_at TIMESTAMP,
    pickup_at TIMESTAMP NULL,
    hospital_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    trip_type VARCHAR(255) DEFAULT 'one_way',
    return_address TEXT NULL,
    event_request_id BIGINT FOREIGN KEY NULL,
    is_replacement BOOLEAN DEFAULT 0,
    replaced_dispatch_id BIGINT NULL,
    otw_scene_at TIMESTAMP NULL,
    patient_request_id BIGINT FOREIGN KEY NULL,
    
    -- Region fields
    blok VARCHAR(255), rt VARCHAR(10), rw VARCHAR(10),
    kelurahan VARCHAR(255), kecamatan VARCHAR(255), nomor VARCHAR(10),
    
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

### Dispatch_Logs Table

```sql
CREATE TABLE dispatch_logs (
    id BIGINT PRIMARY KEY,
    dispatch_id BIGINT FOREIGN KEY CASCADE DELETE,
    status VARCHAR(255),
    note TEXT NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

### Dispatch_Location_Histories Table

```sql
CREATE TABLE dispatch_location_histories (
    id BIGINT PRIMARY KEY,
    dispatch_id BIGINT FOREIGN KEY CASCADE DELETE,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

### Patient_Requests Table

```sql
CREATE TABLE patient_requests (
    id BIGINT PRIMARY KEY,
    patient_name VARCHAR(255),
    service_type ENUM('ambulance', 'jenazah'),
    request_date DATE,
    pickup_time VARCHAR(255),
    phone VARCHAR(20),
    pickup_address TEXT,
    destination TEXT NULL,
    patient_condition ENUM('emergency', 'kontrol'),
    status ENUM('pending', 'dispatched', 'rejected', 'completed'),
    dispatch_id BIGINT FOREIGN KEY NULL ON DELETE SET NULL,
    trip_type VARCHAR(255) DEFAULT 'one_way',
    return_address TEXT NULL,
    tts_url VARCHAR(255) NULL,
    
    -- Region fields
    blok, rt, rw, kelurahan, kecamatan, nomor VARCHAR(255),
    
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

### Activity_Logs Table

```sql
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY,
    action VARCHAR(255),
    model VARCHAR(255),
    model_id BIGINT,
    description TEXT,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

### Activity_Photos Table

```sql
CREATE TABLE activity_photos (
    id BIGINT PRIMARY KEY,
    activity_log_id BIGINT FOREIGN KEY CASCADE DELETE,
    photo_path VARCHAR(255),
    description TEXT NULL,
    sequence INT,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

---

## 4. COMPLETION FLOW IMPLEMENTATION

### When Dispatch Marked Complete

**File**: [app/Http/Controllers/Driver/DriverDashboardController.php](app/Http/Controllers/Driver/DriverDashboardController.php#L88)

```php
if ($newStatus === 'completed') {
    $updateData['completed_at'] = now();

    // 1. Free up ambulance
    $dispatch->ambulance->update([
        'status' => 'ready',           // ← Back to available
        'latitude' => null,            // ← Clear location
        'longitude' => null,
        'last_location_update' => null,
    ]);

    // 2. Free up driver
    $dispatch->driver->update(['status' => 'available']);

    // 3. Sync PatientRequest if exists
    PatientRequest::where('dispatch_id', $dispatch->id)
        ->update(['status' => 'completed']);
}
```

### Automatic Logging

```php
DispatchLog::create([
    'dispatch_id' => $dispatch->id,
    'status' => 'completed',
    'note' => 'Status diupdate oleh driver',
]);
```

### Admin Flow

**File**: [app/Http/Controllers/Admin/DispatchController.php](app/Http/Controllers/Admin/DispatchController.php#L180)

- **Route**: POST /admin/dispatches/{dispatch}/next
- **Same logic** as driver flow
- **Can force status transitions** from admin panel

---

## 5. REQUEST HISTORY TRACKING MECHANISMS

### ✅ MECHANISM 1: DispatchLog (Status History)

**Purpose**: Audit trail of all status changes

**When Created**: 
- Every status transition (pending → on_the_way_scene, etc.)
- Pause/resume events
- Manual admin actions

**Data Stored**:
```sql
dispatch_logs:
- dispatch_id (links to dispatch)
- status (value at time of change)
- note (contextual reason)
- created_at (timestamp of change)
```

**Query Example**:
```php
$dispatch->load('logs');  // Gets all status history with timestamps
$logs = DispatchLog::where('dispatch_id', $dispatchId)
    ->orderBy('created_at')
    ->get();
```

**Migration**: `2026_02_06_145700_add_columns_to_dispatch_logs_table.php`

---

### ✅ MECHANISM 2: DispatchLocationHistory (GPS Tracking)

**Purpose**: Track vehicle route during dispatch

**When Saved**:
- API receives location update: POST /api/driver/location
- Only if active dispatch exists (status in active statuses)
- Automatically linked to current dispatch

**Data Stored**:
```sql
dispatch_location_histories:
- dispatch_id
- latitude (decimal 10,8)
- longitude (decimal 11,8)
- created_at (timestamp of GPS point)
```

**Controller**: [app/Http/Controllers/Api/DriverLocationController.php](app/Http/Controllers/Api/DriverLocationController.php#L14)

```php
public function updateLocation(Request $request) {
    // Get active dispatch for ambulance
    $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
        ->whereIn('status', ['pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos'])
        ->first();

    // Save to location history if dispatch active
    if ($activeDispatch) {
        DispatchLocationHistory::create([
            'dispatch_id' => $activeDispatch->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);
    }
}
```

**Migration**: `2026_03_05_091432_create_dispatch_location_histories_table.php`

---

### ✅ MECHANISM 3: ActivityLog + ActivityPhoto (Driver Activity)

**Purpose**: Document driver activities with photographic evidence

**When Created**:
- On dispatch start: action = 'dispatch_in_progress'
- On driver login (no active dispatch): action = 'driver_login'

**Data Stored**:
```sql
activity_logs:
- user_id (ambulance/driver ID)
- action (dispatch_in_progress, driver_login)
- model (Dispatch, Ambulance)
- model_id (dispatch/ambulance ID)
- description (human-readable)

activity_photos:
- activity_log_id
- photo_path
- description
- sequence (1-5, enforced max)
```

**File**: [app/Models/ActivityLog.php](app/Models/ActivityLog.php#L26)

`public function photos(): HasMany` - One-to-many with enforced limit

**Photos API**:
- POST /api/activities/{activity_id}/photos - Upload
- GET /api/activities/{activity_id}/photos - List
- DELETE /api/photos/{photo_id} - Delete
- PATCH /photos/{photo_id} - Update description

**Migrations**: 
- `2025_12_28_205318_create_activity_logs_table.php`
- `2026_04_08_create_activity_photos_table.php`

---

### ✅ MECHANISM 4: Dispatch Timestamps (State Milestones)

**Purpose**: Record exact moment of each state transition

**Fields**:
```sql
dispatches:
- assigned_at    → When ambulance assigned (dispatch created)
- otw_scene_at   → When "on_the_way_scene" status set
- pickup_at      → When "on_scene" status set
- hospital_at    → When "on_the_way_kantor_pos" status set
- completed_at   → When "completed" status set
```

**Automatically Set**: In DriverDashboardController::updateStatus()

```php
if ($newStatus === 'on_the_way_scene') {
    $updateData['otw_scene_at'] = now();
} elseif ($newStatus === 'on_scene') {
    $updateData['pickup_at'] = now();
} elseif ($newStatus === 'on_the_way_kantor_pos') {
    $updateData['hospital_at'] = now();
} elseif ($newStatus === 'completed') {
    $updateData['completed_at'] = now();
}
```

**Query Time Duration**:
```php
$totalTime = $dispatch->completed_at->diffInMinutes($dispatch->assigned_at);
$sceneTimeMinutes = $dispatch->hospital_at->diffInMinutes($dispatch->pickup_at);
```

---

### ✅ MECHANISM 5: PatientRequest Status Sync

**Purpose**: Track request completion end-to-end

**Status Progression**:
```
pending (initial)
  ↓ (when dispatch created)
dispatched
  ↓ (when dispatch.completed = true)
completed
  ↓ (or manual)
rejected
```

**Sync Point**:
```php
// In DriverDashboardController::updateStatus()
if ($newStatus === 'completed') {
    PatientRequest::where('dispatch_id', $dispatch->id)
        ->update(['status' => 'completed']);
}
```

---

## 6. DRIVER DASHBOARD IMPLEMENTATION

### Routes

```
GET  /driver/dashboard                          → Show dashboard
POST /driver/dispatches/{dispatch}/status       → Update status via AJAX
POST /driver/dispatches/{dispatch}/toggle-pause → Pause/resume
POST /api/driver/location                       → GPS location update
GET  /driver/dispatching                        → View pending requests
POST /driver/dispatching/{patientRequest}       → Self-create dispatch
```

### View File

**Path**: [resources/views/driver/dashboard.blade.php](resources/views/driver/dashboard.blade.php)

**Key Sections**:
1. **Header**: Ambulance plate number, logout button
2. **Active Dispatch Card**: Patient info, addresses, trip type
3. **Status Display**: Current status with color coding
4. **Journey Control**:
   - 4 status buttons (each enabled only for current status)
   - Disabled during pause
   - Color progression: Green → Blue → Orange → Red
5. **Pause/Resume Button**: Toggle with visual feedback
6. **GPS Tracking**: "Tracking belum dimulai" indicator
7. **Location Info**: Current lat/long or "Menunggu GPS..."
8. **Activity Photo Section**: Upload up to 5 photos
9. **No Active Dispatch**: Message + link to pending requests

### JavaScript Flow

```javascript
// 1. Journey button click
journeyBtn.addEventListener('click', async () => {
    const response = await fetch(`/driver/dispatches/${dispatchId}/status`, {
        method: 'POST',
        body: JSON.stringify({ status: currentStatus }),
        headers: {'Content-Type': 'application/json'}
    });
    // Updates UI and refreshes status
});

// 2. GPS tracking (Capacitor)
Geolocation.getCurrentPosition().then(pos => {
    fetch('/api/driver/location', {
        method: 'POST',
        body: { latitude, longitude }
    });
});

// 3. Pause toggle
pauseBtn.addEventListener('click', async () => {
    const response = await fetch(`/driver/dispatches/${dispatchId}/toggle-pause`, {
        method: 'POST'
    });
    // Disables/enables journey button
});
```

---

## 7. ACTIVE DISPATCH STATUS DEFINITIONS

These statuses indicate a dispatch is currently in progress and tracking:

```php
$activeDispatchStatuses = ['pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos'];
```

**Implications**:
- ✅ GPS location tracked (DispatchLocationHistory)
- ✅ Driver can pause/resume
- ✅ Driver can update status
- ✅ Ambulance/driver marked "on_duty"
- ✅ Blocks new dispatch assignment for this unit
- ❌ Cannot transition to another dispatch

**Inactive Status**:
- `completed` - All tracking stops, unit released

---

## 8. TRIP TYPE SUPPORT (Round Trip)

### One Way vs Round Trip

```
trip_type: 'one_way' (default)
  - Patient pickup at A
  - Transport to B
  - Complete

trip_type: 'round_trip'
  - Patient pickup at A
  - Transport to B (pickup_at timestamp)
  - Transport from B back to C (return_address)
  - Complete
```

**Fields**:
```sql
trip_type VARCHAR(255) DEFAULT 'one_way'
return_address TEXT NULL
```

**Status Flow**: Same regardless of trip type - flow is still pending → on_the_way_scene → on_scene → on_the_way_kantor_pos → completed

**Display**: Dashboard shows trip type icon (➡️ 1 Way vs 🔄 Balik)

---

## 9. PUSH NOTIFICATIONS & TEXT-TO-SPEECH

### TTS (Text-to-Speech) Audio

**Storage**: PatientRequest.tts_url field

**When Generated**: On dispatch creation

**Content**: Auto-generated audio like:
> "Dispatch baru. Unit A-001. Untuk Kebakaran. Di Jl. Sudirman No.5"

**Broadcast**:
- Sent via FCM Multicast to:
  - Damkar project (all ambulances)
  - PMI project (if service type includes PMI)
  - GMCI project (if disaster/kebakaran)

**File**: [app/Http/Controllers/Admin/DispatchController.php](app/Http/Controllers/Admin/DispatchController.php#L102) (TTS Service integration)

### FCM Device Tokens

**Models**: DeviceToken

**Token Routes**:
```
POST /public-fcm-token              → Public token save
POST /driver/fcm-token              → Driver token save
POST /api/driver/location           → Also validates ambulance auth
```

**Payload Sent**:
```json
{
    "title": "Dispatch Baru",
    "body": "A-001\nPleton X\nJl. Sudirman No.5\nKebakaran",
    "type": "dispatch",
    "tts_url": "https://...",
    "priority": "high",
    "sound": "emergency"
}
```

---

## 10. KEY IMPLEMENTATION FILES

| File | Purpose | Key Method |
|------|---------|-----------|
| [Dispatch.php](app/Models/Dispatch.php) | Core model with relations | driver(), ambulance(), logs() |
| [DispatchLog.php](app/Models/DispatchLog.php) | Status history | dispatch() |
| [DispatchLocationHistory.php](app/Models/DispatchLocationHistory.php) | GPS tracking | dispatch() |
| [PatientRequest.php](app/Models/PatientRequest.php) | Public request | dispatches() |
| [ActivityLog.php](app/Models/ActivityLog.php) | Driver activity | photos(), user() |
| [DriverDashboardController.php](app/Http/Controllers/Driver/DriverDashboardController.php) | Driver flows | updateStatus(), togglePause() |
| [DispatchController.php](app/Http/Controllers/Admin/DispatchController.php) | Admin flows | next(), store() |
| [DriverLocationController.php](app/Http/Controllers/Api/DriverLocationController.php) | GPS API | updateLocation() |
| [driver/dashboard.blade.php](resources/views/driver/dashboard.blade.php) | Driver UI | - |

---

## 11. RELEVANT MIGRATIONS (EXECUTION ORDER)

1. **2026_02_06_173900_create_patient_requests_table.php**
   - Creates patient_requests table with initial status enum

2. **2025_12_24_123456_create_ambulances_table.php** (implied)
   - Ambulance entities

3. **2025_12_24_231108_create_drivers_table.php** (implied)
   - Driver entities

4. **2026_02_06_150000_update_status_enum_in_dispatches_table_v2.php**
   - Updates dispatches status to: pending, assigned, enroute_pickup, on_scene, enroute_hospital, completed

5. **2026_02_06_145700_add_columns_to_dispatch_logs_table.php**
   - Adds dispatch_id, status, note to dispatch_logs

6. **2026_03_05_091432_create_dispatch_location_histories_table.php**
   - Creates GPS tracking table

7. **2026_03_24_144321_update_dispatch_statuses_to_damkar.php**
   - Final status mapping: pending, on_the_way_scene, on_scene, on_the_way_kantor_pos, completed, cancelled

8. **2026_02_13_130541_add_round_trip_fields_to_tables.php**
   - Adds trip_type and return_address fields

9. **2026_04_01_153500_add_otw_scene_at_to_dispatches_table.php**
   - Adds otw_scene_at timestamp field

10. **2025_12_28_205318_create_activity_logs_table.php**
    - Creates activity_logs for driver tracking

11. **2026_04_08_create_activity_photos_table.php**
    - Creates activity_photos for photo evidence

---

## 12. QUERY EXAMPLES

### Get Full Dispatch History

```php
$dispatch = Dispatch::with([
    'driver',
    'ambulance', 
    'logs' => fn($q) => $q->orderBy('created_at'),
    'locationHistory' => fn($q) => $q->orderBy('created_at')
])->findOrFail($id);

// Timeline
foreach ($dispatch->logs as $log) {
    echo $log->created_at . " -> " . $log->status . ": " . $log->note;
}
```

### Get Request Duration

```php
$travelTime = $dispatch->hospital_at->diffInMinutes($dispatch->otw_scene_at);
$sceneTime = $dispatch->hospital_at->diffInMinutes($dispatch->pickup_at);
$totalTime = $dispatch->completed_at->diffInMinutes($dispatch->assigned_at);
```

### Track Response Sequence

```php
$logs = $dispatch->logs()->orderBy('created_at')->get();
// Shows: pending → on_the_way_scene (time X) → on_scene (time Y) etc.
```

### Get GPS Route

```php
$route = $dispatch->locationHistory()
    ->orderBy('created_at')
    ->get()
    ->map(fn($loc) => [$loc->latitude, $loc->longitude]);
// Can be plotted on map
```

---

## 13. KEY FINDINGS SUMMARY

✅ **Complete Request Tracking**: PatientRequest → Dispatch → Completion with full history

✅ **Multi-Layer History**:
- DispatchLog (status changes)
- DispatchLocationHistory (GPS route)
- ActivityLog (driver session)
- ActivityPhoto (evidence)
- Timestamps (milestone markers)

✅ **Status Flow**: Linear progression (pending → completed) with optional pause

✅ **Resource Management**: Automatic release of ambulance/driver on completion

✅ **Audit Trail**: Every state change logged with timestamp and context

✅ **Real-time Tracking**: GPS coordinates saved during active dispatch

✅ **Mobile First**: Capacitor integration for camera/GPS on mobile

✅ **Event Support**: Can link dispatch to EventRequest for disasters

✅ **Document Evidence**: Up to 5 photos per activity with descriptions

✅ **Push Notifications**: TTS audio + FCM alerts to multiple projects

---

## 14. INTEGRATION POINTS FOR NEW FEATURES

If you need to add request history reporting or tracking features:

### For Historical Reports
- Query `dispatch_logs` with joins to dispatches/drivers/ambulances
- Use timestamps to identify slow responses or bottlenecks
- Calculate KPIs: avg response time, on-scene duration, etc.

### For Route Optimization
- Analyze `dispatch_location_histories` for common routes
- Identify traffic patterns or frequent incident locations
- Optimize ambulance positioning

### For Driver Performance
- Cross-reference `activity_logs` with completion times
- Identify photos from specific incidents
- Track pause/resume patterns

### For Compliance
- Export `dispatch_logs` for incident reports
- Verify timelines against regulatory requirements
- Audit photo evidence collection

---

**Document Generated**: 2026-04-08  
**Last Updated**: Session Memory at `/memories/session/dispatch_system_exploration.md`
