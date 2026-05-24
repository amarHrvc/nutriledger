# API Contracts: Diet Plan Edit and Email Delivery (015)

All endpoints follow the existing `ApiResponses` trait format:
`{ "message": "...", "status": <code>, "data": {...} }`

All endpoints require `auth:sanctum`. Patients receive 403. Only completed plans are editable or sendable.

---

## PATCH /api/patients/{patient}/diet-plans/{dietPlan}

**Action**: Update (edit) a completed diet plan  
**Auth**: Doctor or admin  
**Constraint**: Only `completed` plans — returns 422 if plan is `pending` or `failed`  
**Request body**: Any subset of editable fields (partial update supported)

```json
{
  "rationale": "Revised rationale after clinical review.",
  "daily_calories": 1750,
  "nutritional_goals": {
    "protein_g": 85,
    "carbs_g": 210,
    "fat_g": 58
  },
  "days": [
    { "day": "Monday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Tuesday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Wednesday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Thursday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Friday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Saturday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
    { "day": "Sunday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." }
  ],
  "warnings": ["Revised warning."]
}
```

**Response**: 200 OK

```json
{
  "message": "Diet plan updated successfully.",
  "status": 200,
  "data": {
    "diet_plan": {
      "id": 6,
      "status": "completed",
      "isEdited": true,
      "editedAt": "2026-05-24T10:00:00Z",
      "editedBy": {
        "id": 3,
        "name": "Dr. Amira Halilović"
      },
      "generatedBy": {
        "id": 3,
        "name": "Dr. Amira Halilović"
      },
      "rationale": "Revised rationale after clinical review.",
      "dailyCalories": 1750,
      "nutritionalGoals": { "protein_g": 85, "carbs_g": 210, "fat_g": 58 },
      "days": [ "..." ],
      "warnings": ["Revised warning."],
      "latestDelivery": null,
      "createdAt": "2026-05-14T09:30:00Z"
    }
  }
}
```

**Error responses**:
- `401` — unauthenticated
- `403` — patient role
- `404` — patient or dietPlan not found, or dietPlan does not belong to patient
- `422` — plan is not in `completed` status, or validation failure on submitted fields

---

## POST /api/patients/{patient}/diet-plans/{dietPlan}/send

**Action**: Send a completed diet plan to the patient via email  
**Auth**: Doctor or admin  
**Request body**: none  
**Constraint**: Only `completed` plans — returns 422 if plan is `pending` or `failed`  
**Constraint**: Patient must have a registered email — returns 422 if no email on file  
**Response**: 202 Accepted

```json
{
  "message": "Diet plan is being sent to the patient.",
  "status": 202,
  "data": {
    "delivery": {
      "id": 1,
      "status": "pending",
      "recipientEmail": "patient@example.com",
      "createdAt": "2026-05-24T10:05:00Z"
    }
  }
}
```

**Error responses**:
- `401` — unauthenticated
- `403` — patient role
- `404` — patient or dietPlan not found, or dietPlan does not belong to patient
- `422` — plan is not `completed`, or patient has no email address

---

## Updated: GET /api/patients/{patient}/diet-plans/{dietPlan}

The `show` response is extended to include edit metadata and the latest delivery record:

```json
{
  "data": {
    "diet_plan": {
      "id": 6,
      "status": "completed",
      "isEdited": true,
      "editedAt": "2026-05-24T10:00:00Z",
      "editedBy": { "id": 3, "name": "Dr. Amira Halilović" },
      "generatedBy": { "id": 3, "name": "Dr. Amira Halilović" },
      "rationale": "...",
      "dailyCalories": 1800,
      "nutritionalGoals": { "protein_g": 90, "carbs_g": 220, "fat_g": 60 },
      "days": [ "..." ],
      "warnings": [],
      "latestDelivery": {
        "id": 1,
        "status": "sent",
        "recipientEmail": "patient@example.com",
        "createdAt": "2026-05-24T10:05:00Z"
      },
      "createdAt": "2026-05-14T09:30:00Z"
    }
  }
}
```

---

## Resource Field Map (DietPlanResource — extended)

| Field | Index (list) | Show (detail) | Update response |
|---|---|---|---|
| `id` | ✓ | ✓ | ✓ |
| `status` | ✓ | ✓ | ✓ |
| `isEdited` | ✓ | ✓ | ✓ |
| `editedAt` | — | ✓ | ✓ |
| `editedBy` (name) | — | ✓ | ✓ |
| `generatedBy` (name) | ✓ | ✓ | ✓ |
| `dailyCalories` | ✓ | ✓ | ✓ |
| `nutritionalGoals` | ✓ | ✓ | ✓ |
| `failureReason` | ✓ when failed | ✓ when failed | — |
| `rationale` | — | ✓ | ✓ |
| `days` | — | ✓ | ✓ |
| `warnings` | — | ✓ | ✓ |
| `latestDelivery` | — | ✓ | ✓ |
| `createdAt` | ✓ | ✓ | ✓ |
