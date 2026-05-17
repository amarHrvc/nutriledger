# Contract: PatientResource — socioeconomicData addition

**Affected endpoint**: `GET /api/patients/{id}`  
**Change type**: Additive (backward-compatible)  
**File**: `backend/app/Http/Resources/Api/PatientResource.php`

---

## Change

Add `socioeconomicData` to the `attributes` block of the `PatientResource` response. The value is the full `PatientSocioeconomicResource` attributes object when the relation is loaded, or `null` when the patient has no socioeconomic record.

## Before (existing response shape)

```json
{
  "message": "Patient retrieved successfully.",
  "status": 200,
  "data": {
    "patient": {
      "type": "patient",
      "id": "1",
      "attributes": {
        "firstName": "Marko",
        "lastName": "Petrović",
        "fullName": "Marko Petrović",
        "dateOfBirth": "1985-04-12",
        "gender": "M",
        "phone": "+387 61 123 456",
        "address": "...",
        "city": "Sarajevo",
        "postalCode": "71000",
        "emergencyContactName": "Ana Petrović",
        "emergencyContactPhone": "+387 61 654 321",
        "bloodType": "A+",
        "allergies": "Gluten, lactose intolerance",
        "medicalNotes": "...",
        "createdAt": "2026-05-17T00:00:00+00:00",
        "updatedAt": "2026-05-17T00:00:00+00:00"
      },
      "relationships": {
        "user": { "data": { "type": "user", "id": "3" } },
        "socioeconomic": { "data": { "type": "patient_socioeconomic", "id": "1" } }
      }
    }
  }
}
```

## After (updated response shape)

```json
{
  "message": "Patient retrieved successfully.",
  "status": 200,
  "data": {
    "patient": {
      "type": "patient",
      "id": "1",
      "attributes": {
        "firstName": "Marko",
        "lastName": "Petrović",
        "fullName": "Marko Petrović",
        "dateOfBirth": "1985-04-12",
        "gender": "M",
        "phone": "+387 61 123 456",
        "address": "...",
        "city": "Sarajevo",
        "postalCode": "71000",
        "emergencyContactName": "Ana Petrović",
        "emergencyContactPhone": "+387 61 654 321",
        "bloodType": "A+",
        "allergies": "Gluten, lactose intolerance",
        "medicalNotes": "...",
        "createdAt": "2026-05-17T00:00:00+00:00",
        "updatedAt": "2026-05-17T00:00:00+00:00",
        "socioeconomicData": {
          "type": "patient_socioeconomic",
          "id": "1",
          "attributes": {
            "maritalStatus": "married",
            "numberOfDependents": 2,
            "livingArrangement": "with_family",
            "employmentStatus": "employed_full_time",
            "occupation": "Teacher",
            "incomeLevel": "middle",
            "hasHealthInsurance": true,
            "educationLevel": "bachelors",
            "smokingStatus": "never",
            "alcoholConsumption": "occasional",
            "physicalActivityLevel": "lightly_active",
            "hasFamilySupport": true,
            "hasCaregiver": false,
            "transportationAccess": "own_vehicle",
            "foodSecurityStatus": "food_secure",
            "dietaryRestrictionsCultural": null,
            "additionalNotes": null,
            "createdAt": "2026-05-17T00:00:00+00:00",
            "updatedAt": "2026-05-17T00:00:00+00:00"
          }
        }
      },
      "relationships": {
        "user": { "data": { "type": "user", "id": "3" } },
        "socioeconomic": { "data": { "type": "patient_socioeconomic", "id": "1" } }
      }
    }
  }
}
```

When the patient has **no** socioeconomic record:
```json
"socioeconomicData": null
```

---

## PATCH /api/patients/{id} — socioeconomic update payload

No change to the endpoint. Payload shape (all keys optional):

```json
{
  "socioeconomic": {
    "marital_status": "married",
    "number_of_dependents": 2,
    "living_arrangement": "with_family",
    "employment_status": "employed_full_time",
    "occupation": "Teacher",
    "income_level": "middle",
    "has_health_insurance": true,
    "education_level": "bachelors",
    "smoking_status": "never",
    "alcohol_consumption": "occasional",
    "physical_activity_level": "lightly_active",
    "has_family_support": true,
    "has_caregiver": false,
    "transportation_access": "own_vehicle",
    "food_security_status": "food_secure",
    "dietary_restrictions_cultural": null,
    "additional_notes": null
  }
}
```

Response: standard `PatientResource` (200 OK), now including `socioeconomicData`.
