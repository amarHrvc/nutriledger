# When Was camelCase Introduced? - Timeline Analysis

## 📅 **Timeline of Events**

### March 20, 2026 - **JSON:API Decision Made**
**Commit:** `dd68b48` - "feat(auth): AUTH-06 Create UserResource with JSON:API shape"

**What happened:**
- First JSON:API resource created: `UserResource.php`
- **camelCase attributes introduced** for the first time:
  ```php
  'attributes' => [
      'name' => $this->name,
      'email' => $this->email,
      'role' => $this->role,
      'createdAt' => $this->created_at,  // ← camelCase!
      'updatedAt' => $this->updated_at,  // ← camelCase!
      'deletedAt' => $this->deleted_at,  // ← camelCase!
  ]
  ```

**Design Decision:** 
- API responses would use **JSON:API v1 standard**
- Attributes follow **JavaScript conventions** (camelCase)
- Database still uses **PHP/Laravel conventions** (snake_case)

---

### April 1, 2026 - **Patient API Spec Created with camelCase**
**Issue:** `nutri-ledger-0rk` - "T021: Test response conforms to JSON:API envelope"

**Description:**
> "Verify all patient responses conform to **JSON:API v1 structure with camelCase keys**. 
> Assert data/type/id/attributes structure, camelCase attribute names, relationships structure."

**This task explicitly requires:**
- ✅ JSON:API v1 structure
- ✅ **camelCase attribute names**
- ✅ Relationships structure

---

### April 1, 2026 - **T008 Created with camelCase→snake_case Conversion**
**Issue:** `nutri-ledger-1u3` - "T008: Create PatientController with CRUD methods"

**Key Design Note in Task Description:**
> "camelCase-to-snake_case mapping is **required** for all request fields before passing to Eloquent. 'userId' maps to 'user_id'."

**Explicit Implementation in Task:**
```php
public function store(StorePatientRequest $request): JsonResponse
{
    // Map camelCase userId to snake_case user_id for Eloquent
    $patientData = collect($request->validated())
        ->except('socioeconomic')
        ->mapWithKeys(fn($v, $k) => [$k === 'userId' ? 'user_id' : \Str::snake($k) => $v])
        ->toArray();
    // ...
}
```

**Acceptance Criteria:**
- [x] store() maps camelCase keys to snake_case (including userId -> user_id)
- [x] update() maps camelCase keys to snake_case (excluding user_id)

---

### April 3, 2026 - **StorePatientRequest Created with camelCase**
**Commit:** `b59e346` - "T006: Update StorePatientRequest"

**What was created:**
```php
'userId' => ['required', 'integer', 'exists:users,id'],  // ← camelCase!
'firstName' => ['required', 'string', 'max:50'],         // ← camelCase!
'lastName' => ['required', 'string', 'max:50'],          // ← camelCase!
'dateOfBirth' => ['required', 'date', 'before:today'],   // ← camelCase!
```

**This matched the existing tests** that were already sending camelCase:
```php
$response = $this->actingAs($doctor)
    ->postJson('/api/patients', [
        'userId' => $patientUser->id,        // ← camelCase from day 1
        'firstName' => 'Jane',               // ← camelCase from day 1
        'lastName' => 'Smith',               // ← camelCase from day 1
    ]);
```

---

## 🎯 **Answer to Your Question**

> "This sec introduced change to camelCase?"

**NO - camelCase was NOT introduced in this section (T008).**

### The Architecture Flow:

1. **March 20** → JSON:API standard adopted for **responses** (UserResource)
2. **April 1** → Patient API specification explicitly required **camelCase for both requests AND responses**
3. **April 1** → T008 task **explicitly documented** the camelCase→snake_case conversion requirement
4. **April 3** → StorePatientRequest implemented with camelCase (following the spec)
5. **Today** → You're implementing T008 controller which must do the conversion

---

## 📋 **Why This Architecture?**

### JSON:API Standard (https://jsonapi.org/)
> "Member names **SHOULD** start and end with the characters "a-z" (lowercase)"

**Common Practice:**
- JavaScript/TypeScript APIs → camelCase
- JSON:API examples → camelCase
- REST API conventions → camelCase

### Laravel/PHP Standard
- Database columns → snake_case
- Model properties → snake_case
- Internal code → snake_case

### The Bridge
**Controllers must convert:**
```
API (camelCase) → Controller (conversion) → Eloquent (snake_case) → Database
                       ↓
                PatientController
                    ↓
              mapWithKeys()
```

---

## 📌 **Key Takeaway**

The **camelCase design was intentional from March 20, 2026** when JSON:API was adopted.

**T008 did NOT introduce camelCase** - it **documented the required conversion** to bridge:
- External API contract (camelCase - JavaScript world)
- Internal database schema (snake_case - PHP/Laravel world)

**This is NOT a bug or oversight** - it's a deliberate architectural decision to:
1. ✅ Follow JSON:API standards
2. ✅ Be JavaScript-friendly
3. ✅ Follow Laravel conventions internally
4. ✅ Keep database portable and standard

The conversion in controllers is the **necessary glue** between these two worlds.

---

## 🔍 **Evidence Summary**

| Date | Event | Evidence |
|------|-------|----------|
| Mar 20 | JSON:API adopted | `dd68b48` commit, UserResource with camelCase |
| Apr 1 | Patient spec requires camelCase | T021 description: "camelCase keys" |
| Apr 1 | T008 documents conversion | Task description: "mapping is required" |
| Apr 3 | Form Request uses camelCase | StorePatientRequest: `userId`, `firstName` |
| Today | Tests expect camelCase | PatientApiTest sends camelCase |

**Conclusion:** camelCase was a **project-wide decision made in March 2026**, not something introduced during T008 implementation.
