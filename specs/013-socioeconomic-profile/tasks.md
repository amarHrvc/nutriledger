# Tasks: Patient Socioeconomic Profile

**Branch**: `013-socioeconomic-profile`  
**Spec**: [spec.md](./spec.md) | **Plan**: [plan.md](./plan.md) | **Data model**: [data-model.md](./data-model.md)

## Legend

- `[ ]` — not started · `[x]` — done
- **[P]** — parallelisable (no dependency on an incomplete sibling task)
- **Epic dependency** — an epic cannot start until all epics listed under *Blocked by* are complete

---

## Epic 0 · Backend Bug Fixes

**Layer**: Backend  
**Blocked by**: —  
**Blocks**: Epic 2 (T001 depends on correct enum values), Epic 8 (T012 requires complete Form Requests to produce a correct schema)

**Context**: The API client was regenerated and revealed two bugs in the backend Form Requests.  
Both `StorePatientRequest` and `UpdatePatientRequest` validate only 10 of 17 socioeconomic fields and use a wrong `food_security_status` enum (`food_secure,food_insecure,unsure` instead of `food_secure,marginally_secure,food_insecure,severely_insecure`).  
Reference for correct validation: `backend/app/Http/Requests/StoreSocioeconomicRequest.php` (old Livewire-era request — correct and complete).

---

- [ ] T000b Fix socioeconomic validation in `backend/app/Http/Requests/StorePatientRequest.php` and `backend/app/Http/Requests/UpdatePatientRequest.php`

  **Files**:
  - `backend/app/Http/Requests/StorePatientRequest.php`
  - `backend/app/Http/Requests/UpdatePatientRequest.php`

  **Missing 7 fields** (in both): `living_arrangement`, `occupation`, `education_level`, `has_family_support`, `has_caregiver`, `transportation_access`, `dietary_restrictions_cultural`

  **StorePatientRequest.php** — replace the existing 10-field socioeconomic block with:
  ```php
  'socioeconomic' => ['nullable', 'array'],
  'socioeconomic.marital_status' => ['nullable', 'in:single,married,divorced,widowed,separated,other'],
  'socioeconomic.number_of_dependents' => ['nullable', 'integer', 'min:0'],
  'socioeconomic.living_arrangement' => ['nullable', 'in:alone,with_family,with_partner,shared_housing,care_facility,other'],
  'socioeconomic.employment_status' => ['nullable', 'in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other'],
  'socioeconomic.occupation' => ['nullable', 'string', 'max:255'],
  'socioeconomic.income_level' => ['nullable', 'in:low,lower_middle,middle,upper_middle,high'],
  'socioeconomic.has_health_insurance' => ['nullable', 'boolean'],
  'socioeconomic.education_level' => ['nullable', 'in:no_formal,primary,secondary,vocational,bachelors,masters,doctorate,other'],
  'socioeconomic.smoking_status' => ['nullable', 'in:never,former,current_light,current_heavy'],
  'socioeconomic.alcohol_consumption' => ['nullable', 'in:none,occasional,moderate,heavy'],
  'socioeconomic.physical_activity_level' => ['nullable', 'in:sedentary,lightly_active,moderately_active,very_active'],
  'socioeconomic.has_family_support' => ['nullable', 'boolean'],
  'socioeconomic.has_caregiver' => ['nullable', 'boolean'],
  'socioeconomic.transportation_access' => ['nullable', 'in:own_vehicle,public_transport,rideshare,walking,limited,none'],
  'socioeconomic.food_security_status' => ['nullable', 'in:food_secure,marginally_secure,food_insecure,severely_insecure'],
  'socioeconomic.dietary_restrictions_cultural' => ['nullable', 'string', 'max:500'],
  'socioeconomic.additional_notes' => ['nullable', 'string', 'max:2000'],
  ```

  **UpdatePatientRequest.php** — same 17-field block, each rule prefixed with `'sometimes'` (matching the file's existing update convention):
  ```php
  'socioeconomic' => ['sometimes', 'nullable', 'array'],
  'socioeconomic.marital_status' => ['sometimes', 'nullable', 'in:single,married,divorced,widowed,separated,other'],
  'socioeconomic.number_of_dependents' => ['sometimes', 'nullable', 'integer', 'min:0'],
  'socioeconomic.living_arrangement' => ['sometimes', 'nullable', 'in:alone,with_family,with_partner,shared_housing,care_facility,other'],
  'socioeconomic.employment_status' => ['sometimes', 'nullable', 'in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other'],
  'socioeconomic.occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
  'socioeconomic.income_level' => ['sometimes', 'nullable', 'in:low,lower_middle,middle,upper_middle,high'],
  'socioeconomic.has_health_insurance' => ['sometimes', 'nullable', 'boolean'],
  'socioeconomic.education_level' => ['sometimes', 'nullable', 'in:no_formal,primary,secondary,vocational,bachelors,masters,doctorate,other'],
  'socioeconomic.smoking_status' => ['sometimes', 'nullable', 'in:never,former,current_light,current_heavy'],
  'socioeconomic.alcohol_consumption' => ['sometimes', 'nullable', 'in:none,occasional,moderate,heavy'],
  'socioeconomic.physical_activity_level' => ['sometimes', 'nullable', 'in:sedentary,lightly_active,moderately_active,very_active'],
  'socioeconomic.has_family_support' => ['sometimes', 'nullable', 'boolean'],
  'socioeconomic.has_caregiver' => ['sometimes', 'nullable', 'boolean'],
  'socioeconomic.transportation_access' => ['sometimes', 'nullable', 'in:own_vehicle,public_transport,rideshare,walking,limited,none'],
  'socioeconomic.food_security_status' => ['sometimes', 'nullable', 'in:food_secure,marginally_secure,food_insecure,severely_insecure'],
  'socioeconomic.dietary_restrictions_cultural' => ['sometimes', 'nullable', 'string', 'max:500'],
  'socioeconomic.additional_notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
  ```

  **After both files are updated**:
  ```bash
  cd backend
  vendor/bin/pint --dirty
  composer run analyse
  # Verify existing PATCH tests still pass:
  php artisan test tests/Feature/Patient/PatientSocioeconomicTest.php
  php artisan test  # full suite
  ```

  **Exit check**: Full test suite green. Sending `living_arrangement: 'invalid'` to `PATCH /api/patients/{id}` returns 422.

---

## Epic 1 · Frontend Infrastructure

**Layer**: Frontend  
**Blocked by**: —  
**Blocks**: Epic 5 (T009 uses `client.patch`)

---

- [ ] T000 Add `patch` method to `frontend/src/api/client.ts`

  **File**: `frontend/src/api/client.ts`

  Add alongside the existing `put` entry:
  ```typescript
  patch: <T>(path: string, body: unknown) => request<T>(path, { method: 'PATCH', body: JSON.stringify(body) }),
  ```

  The existing `request()` function already handles Bearer token from `localStorage` and the absolute `BASE_URL` — no further changes needed.

  **Why**: `client.ts` exposes `get`, `post`, `put`, `delete` but no `patch`. T009 needs `client.patch()` for `PATCH /api/patients/{id}`.

  **Exit check**: `pnpm run build` (or IDE type check) accepts `client.patch(...)` without errors.

---

## Epic 2 · Backend API Layer

**Layer**: Backend  
**Blocked by**: Epic 0 (T000b must set correct enum values before the test in T001 asserts them)  
**Blocks**: Epic 4 (T005–T007 require `socioeconomicData` in API response), Epic 8 (T012 regenerates schema from live backend)

**Goal**: `GET /api/patients/{id}` embeds the full `PatientSocioeconomicResource` attributes inline under `data.patient.attributes.socioeconomicData`.

---

- [ ] T001 Write failing Pest test in `backend/tests/Feature/Api/PatientSocioeconomicTest.php`

  **File to create**: `backend/tests/Feature/Api/PatientSocioeconomicTest.php`  
  *(Different path from the existing `tests/Feature/Patient/PatientSocioeconomicTest.php` which tests PATCH — this new file tests the GET response shape.)*

  **Goal**: RED phase — both tests FAIL until T002 is implemented.

  ```php
  <?php

  use App\Models\Patient;
  use App\Models\User;
  use Illuminate\Foundation\Testing\RefreshDatabase;

  uses(RefreshDatabase::class);

  test('GET /api/patients/{id} embeds socioeconomicData attributes when loaded', function () {
      $admin = User::factory()->admin()->create();
      $patient = Patient::factory()->hasSocioeconomic(['marital_status' => 'married'])->create();

      $this->actingAs($admin)
          ->getJson("/api/patients/{$patient->id}")
          ->assertOk()
          ->assertJsonPath('data.patient.attributes.socioeconomicData.type', 'patient_socioeconomic')
          ->assertJsonPath('data.patient.attributes.socioeconomicData.attributes.maritalStatus', 'married');
  });

  test('GET /api/patients/{id} returns null socioeconomicData when patient has no record', function () {
      $admin = User::factory()->admin()->create();
      $patient = Patient::factory()->create();

      $this->actingAs($admin)
          ->getJson("/api/patients/{$patient->id}")
          ->assertOk()
          ->assertJsonPath('data.patient.attributes.socioeconomicData', null);
  });
  ```

  **Assertion path**: `PatientSocioeconomicResource::toArray()` returns `{type, id, attributes: {...}}`. Embedded via T002's `toArray($request)` call, the full path to a field is `data.patient.attributes.socioeconomicData.attributes.{camelCaseFieldName}`.

  ```bash
  php artisan test tests/Feature/Api/PatientSocioeconomicTest.php
  # Expected: FAIL (RED phase)
  ```

- [ ] T002 Embed `socioeconomicData` in `backend/app/Http/Resources/Api/PatientResource.php`

  **File**: `backend/app/Http/Resources/Api/PatientResource.php`

  **Goal**: GREEN phase — T001 tests pass.

  1. Add import:
     ```php
     use App\Http\Resources\Api\PatientSocioeconomicResource;
     ```
  2. Inside `toArray()`, in the `attributes` array after `'updatedAt'`:
     ```php
     'socioeconomicData' => $this->whenLoaded('socioeconomic',
         fn () => $this->socioeconomic
             ? (new PatientSocioeconomicResource($this->socioeconomic))->toArray($request)
             : null,
     ),
     ```
  3. Format and analyse:
     ```bash
     vendor/bin/pint --dirty
     composer run analyse
     ```

  **Why `whenLoaded`**: `PatientController::show()` already calls `$patient->load(['socioeconomic', 'user'])`, so the relation is always loaded on the show endpoint. `whenLoaded` ensures the key is absent (not null) when the relation is not loaded, preserving backward-compatibility for callers that don't eager-load.

  ```bash
  php artisan test tests/Feature/Api/PatientSocioeconomicTest.php
  # Expected: PASS (GREEN phase)
  php artisan test
  # Expected: full suite green
  ```

---

## Epic 3 · Frontend Foundation

**Layer**: Frontend  
**Blocked by**: —  *(can run in parallel with Epics 0, 1, 2)*  
**Blocks**: Epic 4 (T005, T006 import from these files), Epic 5 (T008 imports from these files), Epic 6 (T010 imports `SocioeconomicFormPayload`), Epic 7 (T011 imports `SocioeconomicData`, `SocioeconomicFormPayload`)

---

- [ ] T003 [P] Create `frontend/src/views/patients/socioeconomic/types.ts`

  **File to create**: `frontend/src/views/patients/socioeconomic/types.ts`  
  *(Also creates the `socioeconomic/` directory.)*

  > **Do not derive `SocioeconomicFormPayload` from the generated `UpdatePatientRequestSocioeconomic`** — that type only has 10 fields until T000b is applied and T012 runs. After Epic 8, the generated type can replace this custom definition.

  ```typescript
  // Mirrors PatientSocioeconomicResource attributes (camelCase) from GET /api/patients/{id}
  export interface SocioeconomicData {
    maritalStatus: string | null
    numberOfDependents: number | null
    livingArrangement: string | null
    employmentStatus: string | null
    occupation: string | null
    incomeLevel: string | null
    hasHealthInsurance: boolean
    educationLevel: string | null
    smokingStatus: string | null
    alcoholConsumption: string | null
    physicalActivityLevel: string | null
    hasFamilySupport: boolean
    hasCaregiver: boolean
    transportationAccess: string | null
    foodSecurityStatus: string | null
    dietaryRestrictionsCultural: string | null
    additionalNotes: string | null
  }

  // PATCH /api/patients/{id} payload (snake_case, all optional)
  // TODO: after Epic 8 (T012), replace with generated UpdatePatientRequestSocioeconomic
  export interface SocioeconomicFormPayload {
    marital_status?: string | null
    number_of_dependents?: number | null
    living_arrangement?: string | null
    employment_status?: string | null
    occupation?: string | null
    income_level?: string | null
    has_health_insurance?: boolean
    education_level?: string | null
    smoking_status?: string | null
    alcohol_consumption?: string | null
    physical_activity_level?: string | null
    has_family_support?: boolean
    has_caregiver?: boolean
    transportation_access?: string | null
    food_security_status?: string | null
    dietary_restrictions_cultural?: string | null
    additional_notes?: string | null
  }
  ```

- [ ] T004 [P] Create `frontend/src/views/patients/socioeconomic/labels.ts`

  **File to create**: `frontend/src/views/patients/socioeconomic/labels.ts`

  > **`food_security_status`**: Use migration values (`food_secure`, `marginally_secure`, `food_insecure`, `severely_insecure`). The generated schema currently has wrong values (`unsure`) — fixed by T000b. Labels here are correct.

  ```typescript
  export const MARITAL_STATUS_LABELS: Record<string, string> = {
    single: 'Single', married: 'Married', divorced: 'Divorced',
    widowed: 'Widowed', separated: 'Separated', other: 'Other',
  }
  export const LIVING_ARRANGEMENT_LABELS: Record<string, string> = {
    alone: 'Alone', with_family: 'With Family', with_partner: 'With Partner',
    shared_housing: 'Shared Housing', care_facility: 'Care Facility', other: 'Other',
  }
  export const EMPLOYMENT_STATUS_LABELS: Record<string, string> = {
    employed_full_time: 'Employed Full Time', employed_part_time: 'Employed Part Time',
    self_employed: 'Self Employed', unemployed: 'Unemployed', retired: 'Retired',
    student: 'Student', unable_to_work: 'Unable to Work', other: 'Other',
  }
  export const INCOME_LEVEL_LABELS: Record<string, string> = {
    low: 'Low', lower_middle: 'Lower Middle', middle: 'Middle',
    upper_middle: 'Upper Middle', high: 'High',
  }
  export const EDUCATION_LEVEL_LABELS: Record<string, string> = {
    no_formal: 'No Formal Education', primary: 'Primary', secondary: 'Secondary',
    vocational: 'Vocational', bachelors: "Bachelor's", masters: "Master's",
    doctorate: 'Doctorate', other: 'Other',
  }
  export const SMOKING_STATUS_LABELS: Record<string, string> = {
    never: 'Never', former: 'Former Smoker',
    current_light: 'Current (Light)', current_heavy: 'Current (Heavy)',
  }
  export const ALCOHOL_CONSUMPTION_LABELS: Record<string, string> = {
    none: 'None', occasional: 'Occasional', moderate: 'Moderate', heavy: 'Heavy',
  }
  export const PHYSICAL_ACTIVITY_LABELS: Record<string, string> = {
    sedentary: 'Sedentary', lightly_active: 'Lightly Active',
    moderately_active: 'Moderately Active', very_active: 'Very Active',
  }
  export const TRANSPORTATION_ACCESS_LABELS: Record<string, string> = {
    own_vehicle: 'Own Vehicle', public_transport: 'Public Transport',
    rideshare: 'Rideshare', walking: 'Walking', limited: 'Limited', none: 'None',
  }
  export const FOOD_SECURITY_LABELS: Record<string, string> = {
    food_secure: 'Food Secure',
    marginally_secure: 'Marginally Secure',
    food_insecure: 'Food Insecure',
    severely_insecure: 'Severely Insecure',
  }
  ```

  **Exit check**: All keys match `backend/database/migrations/2026_02_26_233216_create_patient_socioeconomic_table.php` and `StoreSocioeconomicRequest.php`.

---

## Epic 4 · View Socioeconomic Profile (US1 — P1) 🎯 MVP

**Layer**: Frontend  
**Blocked by**: Epic 2 (T002 — `socioeconomicData` in API response), Epic 3 (T003 + T004 — types and labels)  
**Blocks**: Epic 5 (T009 wires into T006's `SocioeconomicTab`)

**Goal**: Doctor/admin opens the Socioeconomic tab on any patient and sees all 17 fields in 5 sections. Nulls show "—", booleans show Yes/No chips, enums show human-readable labels.

**Independent test**: Open `/dashboard/patients/1` → click "Socioeconomic" tab → 5 section cards visible → enum field shows label not raw value → null field shows "—" → boolean field shows chip.

---

- [ ] T005 [US1] Create `frontend/src/views/patients/socioeconomic/SocioeconomicSection.tsx`

  **Imports**: `SocioeconomicData` from `./types` (Epic 3 — T003), MUI `Card`, `CardContent`, `CardHeader`, `Grid`, `Typography`, `Chip`

  ```typescript
  interface SectionRow {
    label: string
    value: string | number | boolean | null | undefined
    boolean?: true
  }
  interface Props { title: string; rows: SectionRow[] }
  ```

  Row rendering (order matters — check `boolean` first):
  1. `row.boolean === true` → `<Chip label={row.value ? 'Yes' : 'No'} size='small' color={row.value ? 'success' : 'default'} />`
  2. `row.value === null || row.value === undefined || row.value === ''` → `<Typography>—</Typography>`
  3. All other values including `0` → `<Typography variant='body2' fontWeight={500}>{String(row.value)}</Typography>`

  > `numberOfDependents = 0` is a valid clinical value (no dependents). It passes check 2 because `0 !== null`, `0 !== undefined`, `0 !== ''`, so it correctly renders as `"0"`.

  Layout: MUI `Grid` container `spacing={2}`, each row `Grid size={{ xs: 12, sm: 6 }}`. Label in `Typography variant='caption' color='text.secondary'`, value directly below.  
  Outer wrapper: MUI `Card` with `CardHeader title={title}` and `CardContent`.

- [ ] T006 [US1] Create `frontend/src/views/patients/socioeconomic/SocioeconomicTab.tsx`

  **Imports**:
  - `SocioeconomicSection` from `./SocioeconomicSection` (T005)
  - `SocioeconomicData`, `SocioeconomicFormPayload` from `./types` (T003)
  - All label maps from `./labels` (T004)
  - `PatientResource`, `PatientResourceAttributes` from `@/api/generated/nutriBaseAPI.schemas`
  - `useAuth` from `@/context/AuthContext`

  **Props**:
  ```typescript
  interface Props {
    patient: PatientResource
  }
  ```
  No refresh callback — the page at `app/(dashboard)/dashboard/patients/[id]/page.tsx` already listens for `'patients:changed'` and reloads. T009 fires that event directly.

  **Extract data** — `PatientResourceAttributes` does not yet include `socioeconomicData` (added after Epic 8 / T012). Use a precise intersection cast until then:
  ```typescript
  const attrs = patient.attributes as PatientResourceAttributes & {
    socioeconomicData?: { type: string; id: string; attributes: SocioeconomicData } | null
  }
  const data: SocioeconomicData | null = attrs.socioeconomicData?.attributes ?? null
  // TODO: remove intersection cast after Epic 8 (T012) regenerates schema
  ```

  **Label helper**:
  ```typescript
  const lbl = (map: Record<string, string>, val: string | null | undefined): string | null =>
    val ? (map[val] ?? val) : null
  // ?? val fallback: renders unknown/legacy enum values as-is rather than "—"
  ```

  **Five sections** (pass to `SocioeconomicSection`):
  - **Demographics & Social**: maritalStatus (MARITAL_STATUS_LABELS), numberOfDependents (number), livingArrangement (LIVING_ARRANGEMENT_LABELS)
  - **Economic**: employmentStatus (EMPLOYMENT_STATUS_LABELS), occupation (text), incomeLevel (INCOME_LEVEL_LABELS), hasHealthInsurance (`boolean: true`)
  - **Lifestyle**: educationLevel (EDUCATION_LEVEL_LABELS), smokingStatus (SMOKING_STATUS_LABELS), alcoholConsumption (ALCOHOL_CONSUMPTION_LABELS), physicalActivityLevel (PHYSICAL_ACTIVITY_LABELS)
  - **Support Systems**: hasFamilySupport (`boolean: true`), hasCaregiver (`boolean: true`), transportationAccess (TRANSPORTATION_ACCESS_LABELS)
  - **Food Security**: foodSecurityStatus (FOOD_SECURITY_LABELS), dietaryRestrictionsCultural (text), additionalNotes (text)

  When `data` is null, pass `null` as the value for all rows — `SocioeconomicSection` (T005) renders "—" for each.

  **Role gate**:
  ```typescript
  const { user } = useAuth()
  const canEdit = user?.role === 'admin' || user?.role === 'doktor'
  ```
  Render the "Edit" button (MUI `Button variant='outlined'`) only when `canEdit`. The `onClick` handler is wired in T009.

- [ ] T007 [US1] Add "Socioeconomic" tab to `frontend/src/views/patients/patient-right/index.tsx`

  **File**: `frontend/src/views/patients/patient-right/index.tsx`

  **Imports to add**:
  ```typescript
  import SocioeconomicTab from '../socioeconomic/SocioeconomicTab'
  ```

  **Changes**:
  1. Add to `TabList` after the Vitals tab:
     ```tsx
     <Tab value='socioeconomic' label='Socioeconomic' />
     ```
  2. Add `TabPanel` after the Vitals panel:
     ```tsx
     <TabPanel value='socioeconomic' sx={{ px: 0, pt: 4 }}>
       <SocioeconomicTab patient={patient} />
     </TabPanel>
     ```

  `PatientRightTabs` receives `patient: PatientResource` as a prop and passes it straight to `SocioeconomicTab`. No callback needed — page-level refresh is handled by the `'patients:changed'` event (see T009).

  **Exit check**: Open `/dashboard/patients/1` → "Socioeconomic" tab visible → click → 5 section cards with correct data.

---

## Epic 5 · Edit Socioeconomic Profile (US2 — P2)

**Layer**: Frontend  
**Blocked by**: Epic 1 (T000 — `client.patch` required), Epic 3 (T003 + T004 — types and labels), Epic 4 (T006 — dialog lives inside `SocioeconomicTab`)  
**Blocks**: Epic 6 (T010 reuses T008's `SocioeconomicFields`), Epic 7 (T011 reuses T008's `SocioeconomicFields`)

**Goal**: Doctor/admin clicks "Edit" → dialog opens pre-populated → changes fields → saves → tab reflects updated values without page reload.

**Independent test**: Open Socioeconomic tab → click "Edit" → change "Marital Status" to "Married" and toggle "Has Health Insurance" → save → dialog closes → tab shows "Married" and "Yes".

---

- [ ] T008 [US2] Create `frontend/src/views/patients/socioeconomic/SocioeconomicFields.tsx`

  **Imports**: `SocioeconomicFormPayload` from `./types` (T003), all label maps from `./labels` (T004), MUI `Select`, `MenuItem`, `FormControl`, `InputLabel`, `TextField`, `Switch`, `FormControlLabel`, `Grid`, `Typography`, `Divider`

  **Props**:
  ```typescript
  interface Props {
    value: SocioeconomicFormPayload
    onChange: (data: SocioeconomicFormPayload) => void
    errors?: Record<string, string[]>
  }
  ```

  **Update helper**:
  ```typescript
  const update = (key: keyof SocioeconomicFormPayload, val: unknown) =>
    onChange({ ...value, [key]: val })
  ```

  **Enum Select** (apply for all 10 enum fields):
  ```tsx
  <FormControl fullWidth size='small'>
    <InputLabel>{fieldLabel}</InputLabel>
    <Select
      value={value.field_name ?? ''}
      label={fieldLabel}
      onChange={e => update('field_name', e.target.value || null)}
    >
      <MenuItem value=''>— Not specified —</MenuItem>
      {Object.entries(LABELS_MAP).map(([k, v]) => <MenuItem key={k} value={k}>{v}</MenuItem>)}
    </Select>
  </FormControl>
  ```
  `e.target.value || null` converts the empty-string "Not specified" selection to `null`.

  **Boolean Switch** (for `has_health_insurance`, `has_family_support`, `has_caregiver`):
  ```tsx
  <FormControlLabel
    control={
      <Switch
        checked={!!value.has_health_insurance}
        onChange={e => update('has_health_insurance', e.target.checked)}
      />
    }
    label='Has Health Insurance'
  />
  ```

  **Integer field** (`number_of_dependents`):
  ```tsx
  <TextField
    type='number'
    label='Number of Dependents'
    size='small'
    fullWidth
    inputProps={{ min: 0 }}
    value={value.number_of_dependents ?? ''}
    onChange={e => update('number_of_dependents', e.target.value === '' ? null : parseInt(e.target.value, 10))}
  />
  ```

  **Free-text fields** (`occupation`, `dietary_restrictions_cultural`, `additional_notes`):
  ```tsx
  <TextField multiline rows={2} fullWidth size='small' label='...' value={value.field ?? ''} onChange={e => update('field', e.target.value || null)} />
  ```

  Group controls into 5 categories with `Typography variant='subtitle2'` headers and `Divider` separators. No submit button — parent owns submission.

  **Exit check**: Render with empty `value={}`. Selecting and then clearing a Select stores `null` not `''`. Integer field stores `number | null` not string.

- [ ] T009 [US2] Wire edit dialog into `frontend/src/views/patients/socioeconomic/SocioeconomicTab.tsx`

  **Imports to add** (to file created in T006):
  - `SocioeconomicFields` from `./SocioeconomicFields` (T008)
  - `client` from `@/api/client` (T000 — provides `client.patch`)
  - MUI `Dialog`, `DialogTitle`, `DialogContent`, `DialogActions`, `Button`, `CircularProgress`, `Alert`
  - `useState` from React

  **State**:
  ```typescript
  const [editOpen, setEditOpen] = useState(false)
  const [formData, setFormData] = useState<SocioeconomicFormPayload>({})
  const [saving, setSaving] = useState(false)
  const [saveError, setSaveError] = useState<string | null>(null)
  ```

  **Open handler** — map camelCase `data` → snake_case `formData`:
  ```typescript
  const handleEditOpen = () => {
    setFormData(data ? {
      marital_status: data.maritalStatus,
      number_of_dependents: data.numberOfDependents,
      living_arrangement: data.livingArrangement,
      employment_status: data.employmentStatus,
      occupation: data.occupation,
      income_level: data.incomeLevel,
      has_health_insurance: data.hasHealthInsurance,
      education_level: data.educationLevel,
      smoking_status: data.smokingStatus,
      alcohol_consumption: data.alcoholConsumption,
      physical_activity_level: data.physicalActivityLevel,
      has_family_support: data.hasFamilySupport,
      has_caregiver: data.hasCaregiver,
      transportation_access: data.transportationAccess,
      food_security_status: data.foodSecurityStatus,
      dietary_restrictions_cultural: data.dietaryRestrictionsCultural,
      additional_notes: data.additionalNotes,
    } : {})
    setSaveError(null)
    setEditOpen(true)
  }
  ```

  **Save handler** — uses `client.patch` from T000:
  ```typescript
  const handleSave = async () => {
    setSaving(true)
    setSaveError(null)
    try {
      await client.patch(`api/patients/${patient.id}`, { socioeconomic: formData })
      setEditOpen(false)
      window.dispatchEvent(new CustomEvent('patients:changed'))
    } catch (err: unknown) {
      setSaveError(err instanceof Error ? err.message : 'Failed to save. Please try again.')
    } finally {
      setSaving(false)
    }
  }
  ```

  **Refresh mechanism**: `window.dispatchEvent(new CustomEvent('patients:changed'))` is the established pattern in this app. The page at `app/(dashboard)/dashboard/patients/[id]/page.tsx:37` listens for this event and calls `loadPatient()`, which re-fetches the patient and passes updated props down through `PatientDetail` → `PatientRightTabs` → `SocioeconomicTab`. No callback prop needed.

  **Wire Edit button**: `onClick={handleEditOpen}`

  **Dialog**:
  ```tsx
  <Dialog open={editOpen} onClose={() => !saving && setEditOpen(false)} maxWidth='md' fullWidth>
    <DialogTitle>Edit Socioeconomic Profile</DialogTitle>
    <DialogContent dividers>
      {saveError && <Alert severity='error' sx={{ mb: 2 }}>{saveError}</Alert>}
      <SocioeconomicFields value={formData} onChange={setFormData} />
    </DialogContent>
    <DialogActions>
      <Button onClick={() => setEditOpen(false)} disabled={saving}>Cancel</Button>
      <Button variant='contained' onClick={handleSave} disabled={saving}>
        {saving ? <CircularProgress size={20} /> : 'Save'}
      </Button>
    </DialogActions>
  </Dialog>
  ```

  **Exit check**: Click Edit → dialog opens pre-populated → change one field → Save → dialog closes → tab shows updated value (via `'patients:changed'` event reload).

---

## Epic 6 · Patient Creation with Socioeconomic (US3 — P3)

**Layer**: Frontend  
**Blocked by**: Epic 5 — T008 (`SocioeconomicFields` component)  
**Blocks**: —

**Goal**: The Add New Patient form has a collapsed "Socioeconomic Information" accordion. Submitting without expanding creates the patient with no socioeconomic record. Expanding, filling, and submitting sends both in one request.

**Independent test**: Open Add New Patient → fill required fields → submit without expanding → patient created (no `patient_socioeconomic` row). Open again → expand → set marital_status → submit → open patient's Socioeconomic tab → marital_status is set.

---

- [ ] T010 [US3] Add socioeconomic accordion to `frontend/src/views/patients/PatientForm.tsx`

  **Imports to add**:
  ```typescript
  import Accordion from '@mui/material/Accordion'
  import AccordionSummary from '@mui/material/AccordionSummary'
  import AccordionDetails from '@mui/material/AccordionDetails'
  import { IconChevronDown } from '@tabler/icons-react'
  import SocioeconomicFields from './socioeconomic/SocioeconomicFields'
  import type { SocioeconomicFormPayload } from './socioeconomic/types'
  ```

  **State**: `const [socioData, setSocioData] = useState<SocioeconomicFormPayload>({})`

  **In submit handler**, conditionally attach socioeconomic payload:
  ```typescript
  // Send only when the user has interacted with at least one field.
  // Object.keys check is used (not value check) because booleans set to false
  // are valid and meaningful data — e.g. "no health insurance" must not be dropped.
  if (Object.keys(socioData).length > 0) {
    payload.socioeconomic = socioData
  }
  ```

  **Accordion** (after main patient fields, before the submit button):
  ```tsx
  <Accordion
    disableGutters
    elevation={0}
    sx={{ border: '1px solid', borderColor: 'divider', borderRadius: 1, mt: 2 }}
  >
    <AccordionSummary expandIcon={<IconChevronDown size={20} />}>
      <Typography variant='subtitle2'>
        Socioeconomic Information{' '}
        <Typography component='span' variant='caption' color='text.secondary'>
          (Optional)
        </Typography>
      </Typography>
    </AccordionSummary>
    <AccordionDetails>
      <SocioeconomicFields value={socioData} onChange={setSocioData} />
    </AccordionDetails>
  </Accordion>
  ```

  MUI `Accordion` is collapsed by default — no additional prop needed.

  **Design note**: Conditional send at creation is intentional — omitting the key avoids creating an empty `patient_socioeconomic` row. Contrast with T011 (edit) which always sends to allow clearing existing values.

  **Exit check**: Submit without expanding → no `patient_socioeconomic` row. Expand → set `marital_status` → submit → row created with correct value. Submitting with only booleans explicitly toggled to `false` → row still created (key-count check preserves this).

---

## Epic 7 · Patient Edit with Socioeconomic (US4 — P4)

**Layer**: Frontend  
**Blocked by**: Epic 5 — T008 (`SocioeconomicFields` component)  
**Blocks**: —  
*(Can run in parallel with Epic 6 — different file.)*

**Goal**: The Edit Patient form has the same optional accordion, pre-populated with existing socioeconomic data when available.

**Independent test**: Open a patient's Edit form → expand accordion → fields pre-populated → change one → save → Socioeconomic tab reflects change.

---

- [ ] T011 [US4] Add socioeconomic accordion to `frontend/src/views/patients/PatientEditForm.tsx`

  **Imports to add** — same as T010 (`Accordion`, `AccordionSummary`, `AccordionDetails`, `IconChevronDown`, `SocioeconomicFields`, `SocioeconomicFormPayload`)

  **State**, initialised from existing patient data:
  ```typescript
  const [socioData, setSocioData] = useState<SocioeconomicFormPayload>(() => {
    // PatientResourceAttributes does not yet include socioeconomicData.
    // Using a precise intersection cast until Epic 8 (T012) regenerates the schema.
    // TODO: remove intersection cast after T012 confirms socioeconomicData is in generated types.
    const attrs = patient.attributes as PatientResourceAttributes & {
      socioeconomicData?: { attributes: SocioeconomicData } | null
    }
    const s = attrs.socioeconomicData?.attributes
    if (!s) return {}
    return {
      marital_status: s.maritalStatus ?? null,
      number_of_dependents: s.numberOfDependents ?? null,
      living_arrangement: s.livingArrangement ?? null,
      employment_status: s.employmentStatus ?? null,
      occupation: s.occupation ?? null,
      income_level: s.incomeLevel ?? null,
      has_health_insurance: s.hasHealthInsurance ?? false,
      education_level: s.educationLevel ?? null,
      smoking_status: s.smokingStatus ?? null,
      alcohol_consumption: s.alcoholConsumption ?? null,
      physical_activity_level: s.physicalActivityLevel ?? null,
      has_family_support: s.hasFamilySupport ?? false,
      has_caregiver: s.hasCaregiver ?? false,
      transportation_access: s.transportationAccess ?? null,
      food_security_status: s.foodSecurityStatus ?? null,
      dietary_restrictions_cultural: s.dietaryRestrictionsCultural ?? null,
      additional_notes: s.additionalNotes ?? null,
    }
  })
  ```

  **In submit handler** — always send:
  ```typescript
  payload.socioeconomic = socioData
  ```
  **Design note**: Unlike T010 (create — conditional), always sending on edit allows clearing existing field values. If the key were omitted, the backend would leave current values unchanged.

  **Accordion** — identical markup to T010.

  **Exit check**: Patient with existing data → expand → fields pre-populated → change one → save → Socioeconomic tab reflects change. Patient with no data → expand → all fields empty.

---

## Epic 8 · Quality Gates & Schema Sync

**Layer**: Both  
**Blocked by**: All epics (Epics 0–7 must be complete)  
**Blocks**: —

---

- [ ] T012 Regenerate API client: `cd frontend && pnpm run api:generate`

  **Prerequisite**: Backend must be running with T000b (Form Request fix) and T002 (`PatientResource` change) both in place. The first regeneration (before this feature) captured the pre-T000b / pre-T002 state and is stale.

  **After generation, verify**:
  1. `PatientResourceAttributes` now includes `socioeconomicData` field
  2. `UpdatePatientRequestSocioeconomic` now has all 17 fields (not 10)
  3. `UpdatePatientRequestSocioeconomicFoodSecurityStatus` has `marginally_secure` and `severely_insecure` (not `unsure`)

  **If all three pass**, remove the intersection casts added in T006 and T011:
  - T006 (`SocioeconomicTab.tsx`): remove `PatientResourceAttributes & { socioeconomicData?: ... }`, use the generated `PatientResourceAttributes` directly
  - T011 (`PatientEditForm.tsx`): same removal
  - T003 (`types.ts`): optionally replace `SocioeconomicFormPayload` with the generated `UpdatePatientRequestSocioeconomic`

- [ ] T013 [P] Run full backend test suite: `cd backend && php artisan test`

- [ ] T014 [P] Lint and static analysis on all changed backend files: `cd backend && vendor/bin/pint --dirty && composer run analyse`

- [ ] T015 End-to-end browser verification following `quickstart.md` — all 4 user story scenarios in sequence.

---

## Execution Map

```
Epic 0 (T000b)  ─────────────────────────────────────────────────────────┐
Epic 1 (T000)   ─────────────────────────────────────────────────────────┤
                                                                          │
Epic 3 (T003 ∥ T004)  ─────────────────────────┐                        │
                                                 │                        │
Epic 2 (T001 → T002)  ─────────────────────────┤                        │
                                                 ▼                        │
                                         Epic 4 (T005 → T006 → T007)     │
                                                 │                        │
                                         Epic 5 (T008 → T009) ◄──────────┘
                                                 │
                              ┌──────────────────┴─────────────────┐
                              ▼                                     ▼
                       Epic 6 (T010)                        Epic 7 (T011)
                              │                                     │
                              └──────────────────┬─────────────────┘
                                                 ▼
                                         Epic 8 (T012 → T013 ∥ T014 → T015)
```

---

## Notes

- **Spec inconsistency**: `spec.md` Assumptions says "No backend or API changes are required." Epics 0 and 2 are backend changes. The plan.md is correct; the spec assumption should be updated.
- **Refresh pattern**: This app uses `window.dispatchEvent(new CustomEvent('patients:changed'))` to trigger patient data reloads (`page.tsx:37`). T009 fires this event directly — no prop callback needed.
- **T010 vs T011 boolean handling**: T010 uses `Object.keys(socioData).length > 0` (not a value check) because `false` is a valid and meaningful value for boolean fields. The previous value-based check (`v !== false`) would silently drop an explicit "no health insurance" entry.
- **T010 vs T011 send logic**: Create (T010) sends conditionally — avoids empty row. Edit (T011) always sends — allows clearing values.
- **T000b prerequisite for T012**: The first orval regeneration captured the incomplete schema. T012 must run after T000b + T002 to produce the correct 17-field types.
- **Icon library**: This project uses `@tabler/icons-react` (confirmed from `PatientDetailsCard.tsx:21`). Use `IconChevronDown` as the accordion expand icon in T010 and T011 — not `ExpandMoreIcon` from `@mui/icons-material`.
- **T001 vs existing `PatientSocioeconomicTest.php`**: The new file created in T001 lives at `tests/Feature/Api/PatientSocioeconomicTest.php`. The existing file at `tests/Feature/Patient/PatientSocioeconomicTest.php` tests PATCH behaviour — both files coexist, different scopes.
- **Constitution compliance**: T001 must FAIL before T002 — Principle III (Test-First) requires RED before GREEN.
