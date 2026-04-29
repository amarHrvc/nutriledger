# POST /api/patients

## Notes

- Request body uses **flat JSON** (not JSON:API-wrapped). Responses use JSON:API envelope.
- `userId` is required: the Admin must specify which existing User account to link. That User must have `role = pacijent`.
- `socioeconomic` is an optional nested object. All its fields are optional on create.

## Request

```http
POST /api/patients
Content-Type: application/json
Authorization: Bearer {sanctum_token}
```

```json
{
  "userId": 42,
  "firstName": "John",
  "lastName": "Doe",
  "dateOfBirth": "1990-01-15",
  "gender": "M",
  "phone": "+387 61 234 567",
  "emergencyContactName": "Jane Doe",
  "emergencyContactPhone": "+387 61 345 678",
  "bloodType": "A+",
  "allergies": "Penicillin",
  "medicalNotes": "No known conditions.",
  "socioeconomic": {
    "maritalStatus": "married",
    "numberOfDependents": 2,
    "employmentStatus": "employed_full_time",
    "incomeLevel": "middle",
    "hasHealthInsurance": true,
    "smokingStatus": "never",
    "alcoholConsumption": "occasional",
    "physicalActivityLevel": "moderately_active",
    "foodSecurityStatus": "food_secure"
  }
}
```

## Response — 201 Created

```json
{
  "data": {
    "type": "patient",
    "id": "1",
    "attributes": {
      "firstName": "John",
      "lastName": "Doe",
      "fullName": "John Doe",
      "dateOfBirth": "1990-01-15",
      "gender": "M",
      "phone": "+387 61 234 567",
      "address": null,
      "city": null,
      "postalCode": null,
      "emergencyContactName": "Jane Doe",
      "emergencyContactPhone": "+387 61 345 678",
      "bloodType": "A+",
      "allergies": "Penicillin",
      "medicalNotes": "No known conditions.",
      "createdAt": "2026-04-01T12:00:00+00:00",
      "updatedAt": "2026-04-01T12:00:00+00:00"
    },
    "relationships": {
      "user": {
        "data": { "type": "user", "id": "42" }
      },
      "socioeconomic": {
        "data": { "type": "patient_socioeconomic", "id": "1" }
      }
    }
  },
  "included": [
    {
      "type": "patient_socioeconomic",
      "id": "1",
      "attributes": {
        "maritalStatus": "married",
        "numberOfDependents": 2,
        "employmentStatus": "employed_full_time",
        "incomeLevel": "middle",
        "hasHealthInsurance": true,
        "smokingStatus": "never",
        "alcoholConsumption": "occasional",
        "physicalActivityLevel": "moderately_active",
        "foodSecurityStatus": "food_secure",
        "createdAt": "2026-04-01T12:00:00+00:00",
        "updatedAt": "2026-04-01T12:00:00+00:00"
      }
    }
  ]
}
```

## Error — 422 Unprocessable Entity

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "userId": ["The user id field is required."],
    "firstName": ["The first name field is required."],
    "socioeconomic.maritalStatus": ["The selected socioeconomic.marital status is invalid."]
  }
}
```

## Error — 401 Unauthorized

```json
{ "message": "Unauthenticated." }
```

## Error — 403 Forbidden

```json
{ "message": "This action is unauthorized." }
```
