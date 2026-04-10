# 📮 Postman Collection - Complete Setup

Complete API testing environment for NutriLabs with full flow from login → patient creation → visit recording.

## 📦 What's Included

### Files Created

| File | Size | Purpose |
|------|------|---------|
| `postman-nutri-ledger-collection.json` | 28 KB | Main Postman collection with 16 requests |
| `postman-nutri-ledger-env.json` | 1.5 KB | Environment variables (base URL, tokens, IDs) |
| `POSTMAN-COLLECTION-GUIDE.md` | 11 KB | Complete documentation and usage guide |
| `postman-setup.sh` | 2.7 KB | Linux/Mac setup helper script |
| `postman-setup.bat` | 3.8 KB | Windows setup helper script |
| `POSTMAN-SETUP.md` | This file | Quick start guide |

---

## 🚀 Quick Start (3 Steps)

### Step 1: Download & Open Postman
```
1. Go to https://www.postman.com/downloads/
2. Download and install Postman Desktop
3. Create/login to your Postman account
```

### Step 2: Import Collection
```
1. Click "Import" (top-left button)
2. Select "postman-nutri-ledger-collection.json"
3. Select "postman-nutri-ledger-env.json" for environment
```

### Step 3: Start Testing
```
1. Select "NutriLabs Local" environment (dropdown, top-right)
2. Click "01 - Login (Admin)" → Send
3. Watch tokens auto-populate in environment
4. Click "Run" to execute full test suite
```

---

## 📋 Test Flow (16 Requests)

### Phase 1: Authentication (3 requests)
```
01. Login (Admin)              → Captures admin token & ID
02. Login (Doctor)             → Captures doctor token & ID
03. Get Current User Profile   → Verifies session
```

### Phase 2: Patient Management (4 requests)
```
04. Create Patient (Admin)     → Captures patient ID
05. Get All Patients (Admin)   → Lists all patients with pagination
06. Get Single Patient (Admin) → Shows full patient record
07. Update Patient (Admin)     → Updates patient info
```

### Phase 3: Visit Management (5 requests)
```
08. Create Visit (Doctor)      → Captures visit ID
09. List Patient Visits        → Shows all visits with pagination
10. Get Single Visit           → Shows full visit record
11. Update Visit (Doctor)      → Updates visit notes
12. Delete Visit (Admin Only)  → Hard delete visit
```

### Phase 4: Authorization Tests (3 requests)
```
13. Doctor Cannot Delete Visit → Expects 403 Forbidden
14. Patient Cannot Create Visit → Expects 403 Forbidden
15. Guest Cannot Access Routes  → Expects 401 Unauthorized
```

### Phase 5: Data Summary (1 request)
```
16. Data Summary Report        → Logs all collected data to console
```

---

## 🔑 Key Features

✅ **Auto-Token Capture** — Tokens automatically extracted from login responses and stored in environment

✅ **Dynamic Variables** — All subsequent requests use captured IDs (patient_id, visit_id, etc.)

✅ **Test Assertions** — Each request includes automated test scripts:
- Status code validation
- Response structure verification
- Data extraction and storage

✅ **Full Authorization Coverage** — Tests for:
- Guest access (401)
- Wrong role access (403)
- Cross-user access (403)
- Proper role access (200/201/204)

✅ **Pagination Support** — List endpoints include `page` and `per_page` parameters

✅ **Console Logging** — Request 16 displays all collected data with formatted output

---

## 🧪 Sample Test Data

### User Credentials
```
Admin Email:    admin@nutrilabs.com
Admin Password: password

Doctor Email:   doctor@nutrilabs.com
Doctor Password: password

Patient Name:   John Doe
DOB:            1990-05-15
Blood Type:     O+
Allergies:      Penicillin, Aspirin
```

### Patient Info
```
First Name:     John
Last Name:      Doe
Phone:          +1234567890
Address:        123 Main Street
City:           New York
Postal Code:    10001
Emergency Contact: Jane Doe (+1987654321)
```

### Visit Info
```
Date:           2025-04-10
Doctor:         Will be set from logged-in doctor
Notes:          Clinical observations and recommendations
```

---

## 📊 Response Format

All API responses follow this structure:

```json
{
  "message": "Success message",
  "status": 200,
  "data": {
    "patient": { /* resource data */ },
    // OR for collections:
    // [{ /* resource data */ }]
  },
  "meta": {
    "current_page": 1,
    "total": 10,
    "per_page": 10,
    "last_page": 1
  },
  "links": {
    "first": "http://...",
    "last": "http://...",
    "next": null,
    "prev": null
  }
}
```

---

## 🌍 Environment Variables

| Variable | Default | Notes |
|----------|---------|-------|
| `base_url` | `localhost:8000` | Change if API runs on different port |
| `admin_token` | (empty) | Auto-filled after admin login |
| `admin_user_id` | (empty) | Auto-filled after admin login |
| `doctor_token` | (empty) | Auto-filled after doctor login |
| `doctor_user_id` | (empty) | Auto-filled after doctor login |
| `patient_user_id` | (empty) | Set before patient creation (must exist in DB) |
| `patient_id` | (empty) | Auto-filled after patient creation |
| `visit_id` | (empty) | Auto-filled after visit creation |

### To Change Base URL
```
1. Click environment dropdown (top-right)
2. Select "NutriLabs Local"
3. Click "Edit"
4. Change "base_url" value (e.g., "192.168.1.100:8000")
5. Save
```

---

## 🔍 Understanding the Test Requests

### Login Request
- **Sends:** Email + password
- **Receives:** Bearer token + user details
- **Stores:** Token in environment for subsequent requests

### Patient Creation Request
- **Requires:** Admin token + patient_user_id (from existing user)
- **Sends:** Full patient data (required + optional fields)
- **Receives:** Created patient with ID
- **Stores:** patient_id for visit operations

### Visit Creation Request
- **Requires:** Doctor token + patient_id
- **Sends:** Visit date + clinical notes
- **Receives:** Created visit with ID
- **Stores:** visit_id for view/update/delete operations

### Authorization Test Requests
- **Doctor Delete:** Sends delete request with doctor token → expects 403
- **Patient Create:** Sends create request with patient token → expects 403
- **Guest Access:** Sends request without token → expects 401

---

## 🛠️ Troubleshooting

### "Invalid token" or "Unauthenticated"
**Solution:** Re-run login requests (01 and 02) to refresh tokens

### "Patient not found"
**Solution:** Ensure patient_user_id is set to a valid user ID in the database

### "Access denied" (403)
**Solution:** This is expected for authorization tests (requests 13-14)

### "Port in use" or "Connection refused"
**Solution:** Ensure Laravel server is running:
```bash
cd backend && composer run dev
```

### Variables not auto-populating
**Solution:** Ensure environment is selected (dropdown shows "NutriLabs Local")

---

## 📖 Detailed Documentation

For complete API documentation, parameter details, and response examples, see:
**→ `POSTMAN-COLLECTION-GUIDE.md`**

---

## 🎯 Common Workflows

### Test Authorization
```
Run requests 13, 14, 15 to verify access controls
Expected: All return error status codes
```

### Create Complete Patient Record
```
1. Run 01 (Admin login)
2. Run 04 (Create patient)
3. Run 06 (Get patient details)
4. Run 07 (Update patient)
→ Patient record fully created and updated
```

### Record Visit for Patient
```
1. Run 01 (Admin login)
2. Run 04 (Create patient)
3. Run 02 (Doctor login)
4. Run 08 (Create visit)
5. Run 09 (List visits)
6. Run 10 (Get visit details)
→ Complete visit recording workflow
```

---

## 📞 API Endpoints Reference

**Complete list available in:** `POSTMAN-COLLECTION-GUIDE.md`

Quick reference:
```
POST   /api/login
GET    /api/user
POST   /api/logout

POST   /api/patients
GET    /api/patients
GET    /api/patients/{id}
PUT    /api/patients/{id}
DELETE /api/patients/{id}

POST   /api/patients/{patient}/visits
GET    /api/patients/{patient}/visits
GET    /api/patients/{patient}/visits/{visit}
PATCH  /api/patients/{patient}/visits/{visit}
DELETE /api/patients/{patient}/visits/{visit}
```

---

## ✅ What's Tested

- ✅ User authentication (login/logout)
- ✅ Patient CRUD operations
- ✅ Patient pagination
- ✅ Visit CRUD operations
- ✅ Visit pagination
- ✅ Authorization (Admin, Doctor, Patient roles)
- ✅ Role-based access control (403, 401)
- ✅ Response structure validation
- ✅ Data persistence
- ✅ Cross-user access restrictions

---

## 🚀 Next Steps

1. **Import collection** into Postman Desktop
2. **Select environment** "NutriLabs Local"
3. **Run request 01** to login and populate first token
4. **Run full collection** by clicking "Run" button
5. **Review results** in the Run Results panel
6. **Check console** in each request to see captured data

---

## 📞 Support

For issues, check:
1. Ensure Laravel server is running: `composer run dev`
2. Check base_url is correct in environment
3. Verify user accounts exist in database (admin, doctor, patient)
4. Review POSTMAN-COLLECTION-GUIDE.md for detailed API documentation

---

**Created:** 2026-04-10  
**API Version:** NutriLabs v1  
**Collection Version:** 1.0  
**Status:** ✅ Production Ready
