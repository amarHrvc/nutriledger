# NutriLabs API - Postman Collection Guide

Complete API testing workflow for NutriLabs from authentication through patient and visit management.

## 📋 Collection Overview

**File:** `postman-nutri-ledger-collection.json`

This collection contains 16 requests organized in 5 categories:

1. **🔐 Authentication** (3 requests)
   - Admin login
   - Doctor login
   - Get current user profile

2. **👥 Patient Management** (4 requests)
   - Create patient
   - List all patients
   - Get single patient
   - Update patient

3. **🏥 Visit Management** (5 requests)
   - Create visit
   - List patient visits
   - Get single visit
   - Update visit
   - Delete visit (admin only)

4. **🔍 Authorization Tests** (3 requests)
   - Test doctor cannot delete visit
   - Test patient cannot create visit
   - Test guest cannot access protected routes

5. **📊 Data Collection** (1 request)
   - Display all collected test data

---

## 🚀 Quick Start

### 1. Import Collection into Postman

**Option A: Via UI**
```
1. Open Postman
2. Click "Import" (top-left)
3. Select "postman-nutri-ledger-collection.json"
4. Collection appears in sidebar
```

**Option B: Via Command Line**
```bash
# Using Postman CLI
postman collection run postman-nutri-ledger-collection.json \
  --environment postman-nutri-ledger-env.json \
  --reporters cli,json \
  --reporter-json-export test-results.json
```

### 2. Create Environment

**Option A: Manual Setup**
```
1. In Postman, click "Environments" (left sidebar)
2. Click "Create New"
3. Name: "NutriLabs Local"
4. Add these variables:
   - base_url = localhost:8000
   - admin_token = (auto-filled after login)
   - doctor_token = (auto-filled after login)
   - patient_id = (auto-filled after patient creation)
   - visit_id = (auto-filled after visit creation)
```

**Option B: Download Environment File**
See `postman-nutri-ledger-env.json` (if provided)

### 3. Run Full Test Flow

```
1. Open the collection in Postman
2. Click "▶ Run" (blue button, right side)
3. Select environment: "NutriLabs Local"
4. Click "Run NutriLabs API Testing"
5. Watch requests execute in order
6. View results in "Run Results" panel
```

---

## 🔑 Environment Variables

| Variable | Purpose | Auto-Filled |
|----------|---------|-------------|
| `base_url` | API base URL (default: `localhost:8000`) | Manual |
| `admin_token` | Bearer token for admin user | ✅ After login |
| `admin_user_id` | Admin user database ID | ✅ After login |
| `admin_email` | Admin email (stored for reference) | ✅ After login |
| `doctor_token` | Bearer token for doctor user | ✅ After login |
| `doctor_user_id` | Doctor user database ID | ✅ After login |
| `patient_token` | Bearer token for patient user | Manual (if needed) |
| `patient_user_id` | Patient user database ID | Manual (before patient create) |
| `patient_id` | Patient record ID | ✅ After patient create |
| `visit_id` | Visit record ID | ✅ After visit create |

---

## 📝 Request Details

### 01 - Login (Admin)

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "admin@nutrilabs.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "message": "Authenticated",
  "status": 200,
  "data": {
    "token": "eyJhbGci...",
    "user": {
      "id": 1,
      "email": "admin@nutrilabs.com",
      "name": "Admin User",
      "role": "admin"
    }
  }
}
```

**Auto-Captures:**
- `admin_token` → Used for admin operations
- `admin_user_id` → Used in patient creation
- `admin_email` → Stored for reference

---

### 04 - Create Patient (Admin)

**Endpoint:** `POST /api/patients`

**Required Variables:**
- `admin_token` (from login)
- `patient_user_id` (must exist in users table with role='pacijent')

**Request Body:**
```json
{
  "user_id": "{{patient_user_id}}",
  "first_name": "John",
  "last_name": "Doe",
  "date_of_birth": "1990-05-15",
  "gender": "M",
  "phone": "+1234567890",
  "address": "123 Main Street",
  "city": "New York",
  "postal_code": "10001",
  "emergency_contact_name": "Jane Doe",
  "emergency_contact_phone": "+1987654321",
  "blood_type": "O+",
  "allergies": "Penicillin",
  "medical_notes": "Mild hypertension, managed with medication",
  "socioeconomic": {
    "marital_status": "married",
    "number_of_dependents": 2,
    "employment_status": "employed_full_time",
    "income_level": "middle",
    "has_health_insurance": true,
    "smoking_status": "never",
    "alcohol_consumption": "occasional",
    "physical_activity_level": "moderately_active",
    "food_security_status": "food_secure"
  }
}
```

**Response (201):**
```json
{
  "message": "Patient created successfully.",
  "status": 201,
  "data": {
    "patient": {
      "type": "patient",
      "id": 3,
      "attributes": {
        "user_id": 5,
        "first_name": "John",
        "last_name": "Doe",
        "full_name": "John Doe",
        "date_of_birth": "1990-05-15",
        "gender": "M",
        "blood_type": "O+",
        "allergies": "Penicillin",
        "medical_notes": "Mild hypertension...",
        "phone": "+1234567890",
        "address": "123 Main Street",
        "city": "New York",
        "postal_code": "10001",
        "created_at": "2025-04-10T14:30:00Z",
        "updated_at": "2025-04-10T14:30:00Z"
      }
    }
  }
}
```

**Auto-Captures:**
- `patient_id` → Used for visit endpoints
- `patient_user_id` → Already set, used for reference

---

### 08 - Create Visit (Doctor)

**Endpoint:** `POST /api/patients/{{patient_id}}/visits`

**Required Variables:**
- `doctor_token` (from login)
- `patient_id` (from patient creation)

**Request Body:**
```json
{
  "date": "2025-04-10",
  "notes": "Patient presented with complaints of fatigue and mild headaches. Blood pressure slightly elevated at 140/90. Recommended increase in physical activity and dietary adjustments. Follow-up in 2 weeks."
}
```

**Response (201):**
```json
{
  "message": "Visit created successfully.",
  "status": 201,
  "data": {
    "visit": {
      "type": "visit",
      "id": 7,
      "attributes": {
        "patient_id": 3,
        "doctor_id": 2,
        "date": "2025-04-10",
        "notes": "Patient presented with complaints...",
        "doctorName": "Dr. Smith",
        "createdAt": "2025-04-10T14:35:00Z",
        "updatedAt": "2025-04-10T14:35:00Z"
      }
    }
  }
}
```

**Auto-Captures:**
- `visit_id` → Used for single visit operations

---

### 09 - List Patient Visits (Doctor)

**Endpoint:** `GET /api/patients/{{patient_id}}/visits?page=1&per_page=10`

**Response (200):**
```json
{
  "message": "Visits retrieved successfully.",
  "status": 200,
  "data": [
    {
      "type": "visit",
      "id": 7,
      "attributes": {
        "patient_id": 3,
        "doctor_id": 2,
        "date": "2025-04-10",
        "notes": "Patient presented with complaints...",
        "doctorName": "Dr. Smith",
        "createdAt": "2025-04-10T14:35:00Z",
        "updatedAt": "2025-04-10T14:35:00Z"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 1,
    "total": 1
  },
  "links": {
    "first": "http://localhost:8000/api/patients/3/visits?page=1",
    "last": "http://localhost:8000/api/patients/3/visits?page=1",
    "next": null,
    "prev": null
  }
}
```

---

## 🔐 Authorization Rules

### Access Control Matrix

| Action | Admin | Doctor | Patient | Guest |
|--------|-------|--------|---------|-------|
| Create Patient | ✅ | ❌ | ❌ | ❌ |
| View Patients | ✅ (all) | ❌ | ✅ (own) | ❌ |
| Update Patient | ✅ | ❌ | ❌ | ❌ |
| Delete Patient | ✅ | ❌ | ❌ | ❌ |
| Create Visit | ✅ | ✅ | ❌ | ❌ |
| View Visits | ✅ (all) | ✅ (all) | ✅ (own) | ❌ |
| Update Visit | ✅ (all) | ✅ (own) | ❌ | ❌ |
| Delete Visit | ✅ | ❌ | ❌ | ❌ |

### Test Cases in Collection

**Request 13:** Doctor attempts to delete visit → `403 Forbidden`
**Request 14:** Patient attempts to create visit → `403 Forbidden`
**Request 15:** Guest accesses protected endpoint → `401 Unauthorized`

---

## 🧪 Test Assertions

Each request includes automated tests that verify:

✅ **Status Codes**
- Login: 200 OK
- Create: 201 Created
- Delete: 204 No Content
- Auth errors: 401/403

✅ **Response Structure**
- All responses wrapped with `message`, `status`, `data`
- Collections include `meta` (pagination) and `links`
- Single resources wrapped with type/id/attributes

✅ **Data Captured**
- Tokens extracted for use in subsequent requests
- IDs stored for reference in related operations
- All data logged to Postman console

---

## 🛠️ Common Issues & Fixes

### Issue: "Request invalid" on patient creation
**Cause:** `patient_user_id` not set
**Fix:** Manually set in environment before request:
```
Environment → NutriLabs Local → patient_user_id = 5
```

### Issue: "Doctor not found" on visit creation
**Cause:** Doctor account doesn't exist
**Fix:** Ensure `doctor@nutrilabs.com` with role `'doktor'` exists in DB

### Issue: "Patient not found" on visit creation
**Cause:** Wrong `patient_id` or patient was deleted
**Fix:** Re-run patient creation request (will auto-update `patient_id`)

### Issue: Tokens expire between requests
**Cause:** Sanctum token has 60-minute expiration
**Fix:** Re-run login requests to refresh tokens

---

## 📊 Running Full Test Suite

**In Postman UI:**
```
1. Click "Runner" (top left)
2. Drag collection to left panel
3. Select "NutriLabs Local" environment
4. Set "Delay between requests" to 500ms
5. Click "Run NutriLabs API Testing"
```

**Expected Results:**
- ✅ All 16 requests execute in order
- ✅ Authorization tests show expected status codes
- ✅ Data Summary logs all captured variables
- ✅ 0 failures

---

## 📈 Data Flow Diagram

```
Login (Admin)
    ↓ [admin_token, admin_user_id]
    ↓
Create Patient
    ↓ [patient_id, patient_user_id]
    ↓
List Patients ← ← ← Get Single Patient → Update Patient
    ↓
Login (Doctor)
    ↓ [doctor_token]
    ↓
Create Visit
    ↓ [visit_id]
    ↓
List Visits ← ← ← Get Single Visit → Update Visit
    ↓
Delete Visit
```

---

## 🔗 Related Documentation

- **API Routes:** `/backend/routes/api.php`
- **Controllers:** `/backend/app/Http/Controllers/Api/`
- **Requests:** `/backend/app/Http/Requests/`
- **Resources:** `/backend/app/Http/Resources/Api/`
- **Tests:** `/backend/tests/Feature/`

---

## 📞 Support

For issues with:
- **API endpoints:** Check `/backend/routes/api.php`
- **Validation:** Check request classes in `/backend/app/Http/Requests/`
- **Authorization:** Check policies in `/backend/app/Policies/`
- **Response format:** Check traits in `/backend/app/Http/Controllers/Api/`

---

**Last Updated:** 2026-04-10
**Collection Version:** 1.0
**API Version:** NutriLabs v1
