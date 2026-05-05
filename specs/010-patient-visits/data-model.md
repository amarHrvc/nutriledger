# Data Model: Patient Visits Feature (010)

## Database Changes

### New Migration: `add_time_to_visits`

Adds `time` column to the existing `visits` table.

```
visits
├── id               integer PK
├── patient_id       FK → patients.id
├── doctor_id        FK → users.id (role = doktor)
├── date             DATE  — scheduled calendar date (existing)
├── time             TIME  — scheduled time of day (NEW, nullable)
├── notes            text nullable
├── created_at       timestamp
└── updated_at       timestamp
```

`time` is nullable in the migration to avoid breaking existing seeded/test rows. New visit creation requires both `date` and `time`.

---

## Model: `Visit` (updated)

```php
protected $fillable = ['patient_id', 'doctor_id', 'date', 'time', 'notes'];

protected function casts(): array {
    return [
        'date' => 'date:Y-m-d',
        'time' => 'string',   // stored as HH:mm:ss, returned as-is
    ];
}
```

---

## Edit Lock Rule

A visit is editable when:

```
$visit->date->toDateString() >= now()->subDay()->toDateString()
```

Plain language: the visit's date is today or yesterday. Visits from the day before yesterday or earlier are locked.

Enforced in: `VisitPolicy::update()`.

---

## VisitResource Shape (updated)

```json
{
  "type": "visit",
  "id": "42",
  "attributes": {
    "date": "2026-05-20",
    "time": "14:30:00",
    "notes": "Follow-up on blood pressure.",
    "doctorName": "Dr. Amina Kovač",
    "patientName": "Ivan Horvat",
    "patientId": "7",
    "isEditable": true,
    "createdAt": "2026-05-05T10:00:00+00:00",
    "updatedAt": "2026-05-05T10:00:00+00:00"
  },
  "relationships": {
    "patient": { "data": { "type": "patient", "id": "7" } },
    "doctor":  { "data": { "type": "user",    "id": "3" } }
  }
}
```

**New fields vs current**:
- `time` — new
- `patientName` — new (eager loaded via `patient.user.name`)
- `patientId` — new (for linking to patient profile)
- `isEditable` — new (computed: `date >= yesterday`)

---

## Frontend TypeScript Types (manual patch to nutriBaseAPI.schemas.ts)

```ts
export interface VisitResourceAttributes {
  date: string;
  time: string | null;
  notes: string | null;
  doctorName: string | null;
  patientName: string | null;
  patientId: string | null;
  isEditable: boolean;
  createdAt: string | null;
  updatedAt: string | null;
}

export interface VisitResource {
  type: 'visit';
  id: string;
  attributes: VisitResourceAttributes;
  relationships: {
    patient: { data?: { type: 'patient'; id: string } };
    doctor:  { data?: { type: 'user';    id: string } };
  };
}

export type VisitsGlobalIndex200 = {
  message: string;
  status: 200;
  data: VisitResource[];
  meta: PatientsIndex200Meta;
  links: PatientsIndex200Links;
};

export type PatientVisitsIndex200 = {
  message: string;
  status: 200;
  data: VisitResource[];
  meta: PatientsIndex200Meta;
  links: PatientsIndex200Links;
};
```

---

## Relationships (unchanged)

```
User (doktor) ──< Visit >── Patient
                              │
                         PatientRightTabs
                              │
                          VisitsTab
```

- `Visit.doctor` → `User` (via `doctor_id`)
- `Visit.patient` → `Patient`
- `Patient.visits` → `hasMany(Visit)`
- `User.visitsAsDoctor` → `hasMany(Visit, 'doctor_id')` *(add to User model for globalIndex query)*
