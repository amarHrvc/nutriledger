@echo off
REM NutriLabs Postman Collection Setup Script (Windows)

cls
echo =========================================================
echo NutriLabs API - Postman Collection Setup
echo =========================================================
echo.
echo Available Options:
echo.
echo 1. Import collection into Postman Desktop
echo 2. Display API endpoints summary
echo 3. Show environment variables
echo 4. View test flow diagram
echo.

set /p choice="Enter choice (1-4): "

if "%choice%"=="1" (
    echo.
    echo Instructions to import into Postman:
    echo.
    echo 1. Open Postman Desktop application
    echo 2. Click "Import" button (top-left of window)
    echo 3. Click "Upload Files"
    echo 4. Select "postman-nutri-ledger-collection.json"
    echo 5. Repeat for environment: "postman-nutri-ledger-env.json"
    echo 6. Click the environment dropdown and select "NutriLabs Local"
    echo 7. Click "Run" to execute the collection
    echo.
    echo For detailed guide, see: POSTMAN-COLLECTION-GUIDE.md
    echo.
)

if "%choice%"=="2" (
    echo.
    echo API Endpoints Summary:
    echo.
    echo AUTHENTICATION:
    echo   POST   /api/login              - User login
    echo   POST   /api/logout             - User logout
    echo   GET    /api/user               - Get current user
    echo.
    echo PATIENTS:
    echo   POST   /api/patients           - Create patient (admin only)
    echo   GET    /api/patients           - List patients (with pagination)
    echo   GET    /api/patients/{id}      - Get single patient
    echo   PUT    /api/patients/{id}      - Update patient (admin only)
    echo   DELETE /api/patients/{id}      - Delete patient (admin only)
    echo.
    echo VISITS:
    echo   POST   /api/patients/{patient}/visits           - Create visit (admin/doctor only)
    echo   GET    /api/patients/{patient}/visits           - List visits (with pagination)
    echo   GET    /api/patients/{patient}/visits/{visit}   - Get single visit
    echo   PATCH  /api/patients/{patient}/visits/{visit}   - Update visit (admin/doctor only)
    echo   DELETE /api/patients/{patient}/visits/{visit}   - Delete visit (admin only)
    echo.
)

if "%choice%"=="3" (
    echo.
    echo Environment Variables:
    echo.
    echo base_url       = localhost:8000  (API base URL)
    echo admin_token    = (auto-filled after admin login)
    echo admin_user_id  = (auto-filled after admin login)
    echo doctor_token   = (auto-filled after doctor login)
    echo doctor_user_id = (auto-filled after doctor login)
    echo patient_id     = (auto-filled after patient creation)
    echo patient_user_id = (must be set before patient creation)
    echo visit_id       = (auto-filled after visit creation)
    echo.
)

if "%choice%"=="4" (
    echo.
    echo Test Flow Diagram:
    echo.
    echo Login Admin
    echo     ↓
    echo Get Profile
    echo     ↓
    echo Create Patient
    echo     ├→ List Patients
    echo     ├→ Get Patient Details
    echo     └→ Update Patient
    echo         ↓
    echo     Login Doctor
    echo         ↓
    echo     Create Visit
    echo         ├→ List Visits
    echo         ├→ Get Visit Details
    echo         ├→ Update Visit
    echo         └→ Delete Visit (Admin)
    echo             ↓
    echo         Authorization Tests
    echo             ├→ Doctor cannot delete
    echo             ├→ Patient cannot create
    echo             └→ Guest cannot access
    echo.
)

if "%choice%"=="5" (
    echo Invalid choice
    exit /b 1
)

echo.
echo =========================================================
echo For complete documentation, see: POSTMAN-COLLECTION-GUIDE.md
echo =========================================================
echo.
pause
