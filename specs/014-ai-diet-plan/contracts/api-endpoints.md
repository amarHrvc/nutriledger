# API Contracts: AI Diet Plan Generator (014)

All endpoints follow the existing `ApiResponses` trait format:  
`{ "message": "...", "status": <code>, "data": {...} }`

All endpoints require `auth:sanctum`. Patients receive 403.

---

## POST /api/patients/{patient}/diet-plans

**Action**: Trigger diet plan generation  
**Auth**: Doctor or admin  
**Request body**: none  
**Response**: 202 Accepted

```json
{
  "message": "Diet plan generation started.",
  "status": 202,
  "data": {
    "diet_plan": {
      "id": 7,
      "status": "pending",
      "generated_by": null,
      "created_at": "2026-05-17T14:00:00Z"
    }
  }
}
```

**Error responses**:
- `401` — unauthenticated
- `403` — patient role
- `404` — patient not found

---

## GET /api/patients/{patient}/diet-plans

**Action**: List all diet plans for a patient (paginated, newest first)  
**Auth**: Doctor or admin  
**Query params**: `page` (default 1)  
**Response**: 200 OK

```json
{
  "message": "Diet plans retrieved successfully.",
  "status": 200,
  "data": [
    {
      "id": 7,
      "status": "pending",
      "generated_by": null,
      "created_at": "2026-05-17T14:00:00Z"
    },
    {
      "id": 6,
      "status": "completed",
      "generated_by": {
        "id": 3,
        "name": "Dr. Amira Halilović"
      },
      "daily_calories": 1800,
      "nutritional_goals": { "protein_g": 90, "carbs_g": 220, "fat_g": 60 },
      "created_at": "2026-05-14T09:30:00Z"
    },
    {
      "id": 5,
      "status": "failed",
      "generated_by": {
        "id": 3,
        "name": "Dr. Amira Halilović"
      },
      "failure_reason": "AI response failed validation after 2 attempts.",
      "created_at": "2026-05-10T11:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 3
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  }
}
```

**Note**: List items omit `rationale`, `days`, and `warnings` for performance. Use the show endpoint for full detail.

**Error responses**:
- `401` — unauthenticated
- `403` — patient role
- `404` — patient not found

---

## GET /api/patients/{patient}/diet-plans/{dietPlan}

**Action**: Retrieve full detail of a specific diet plan  
**Auth**: Doctor or admin  
**Response**: 200 OK

```json
{
  "message": "Diet plan retrieved successfully.",
  "status": 200,
  "data": {
    "diet_plan": {
      "id": 6,
      "status": "completed",
      "generated_by": {
        "id": 3,
        "name": "Dr. Amira Halilović"
      },
      "rationale": "Given the patient's food-insecure status and sedentary lifestyle, this plan prioritises affordable, high-fibre staples to support weight management and stable energy levels.",
      "daily_calories": 1800,
      "nutritional_goals": {
        "protein_g": 90,
        "carbs_g": 220,
        "fat_g": 60
      },
      "days": [
        {
          "day": "Monday",
          "breakfast": "Oatmeal with banana and honey",
          "lunch": "Lentil soup with bread",
          "dinner": "Grilled chicken with roasted vegetables",
          "snack": "Apple"
        },
        { "day": "Tuesday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
        { "day": "Wednesday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
        { "day": "Thursday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
        { "day": "Friday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
        { "day": "Saturday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." },
        { "day": "Sunday", "breakfast": "...", "lunch": "...", "dinner": "...", "snack": "..." }
      ],
      "warnings": [
        "Patient is food-insecure — plan uses affordable staples only.",
        "Allergic to shellfish — excluded from all meals."
      ],
      "created_at": "2026-05-14T09:30:00Z"
    }
  }
}
```

**Error responses**:
- `401` — unauthenticated
- `403` — patient role
- `404` — patient or dietPlan not found, or dietPlan does not belong to patient

---

## Resource: DietPlanResource (Summary vs Detail)

`DietPlanResource` has two modes controlled by `$this->when()`:

| Field | Index (list) | Show (detail) |
|---|---|---|
| `id` | ✓ | ✓ |
| `status` | ✓ | ✓ |
| `generated_by` (name) | ✓ | ✓ |
| `daily_calories` | ✓ | ✓ |
| `nutritional_goals` | ✓ | ✓ |
| `failure_reason` | ✓ when failed | ✓ when failed |
| `rationale` | — | ✓ |
| `days` | — | ✓ |
| `warnings` | — | ✓ |
| `created_at` | ✓ | ✓ |

Alternatively, use a `DietPlanSummaryResource` for index and `DietPlanResource` for show — follow existing project convention (see `PatientSummaryResource` vs `PatientResource`).
