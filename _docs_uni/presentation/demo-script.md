# NutriBase — Demo Script
## 10-minute live walkthrough for bachelor defense

---

## Credentials (keep this tab open)

| Role | Email | Password |
|---|---|---|
| Admin | `hajrovica@gmail.com` | `Test12345` |
| Doctor | `doctor@nutribase.test` | `password` |
| Patient (Fatima) | `fatima.hadzic@nutribase.test` | `password` |

---

## Overview of what you will show

| Segment | What | Time |
|---|---|---|
| 1 | Admin login → dashboard → user management | ~2 min |
| 2 | Doctor login → add new patient → Fatima's full story | ~5 min |
| 3 | Create new visit + vitals for Fatima live | ~1.5 min |
| 4 | Stefan's profile — clinical flags in action | ~1 min |
| 5 | Patient login — restricted view | ~30 sec |

---

## SEGMENT 1 — Admin View (2 min)

### Step 1 — Login as Admin

Navigate to the login page.

| Field | Value |
|---|---|
| Email | `hajrovica@gmail.com` |
| Password | `Test12345` |

**What to say:** "Each role sees a different entry point on login. The admin lands on a system overview dashboard showing total registered users and patients."

---

### Step 2 — Admin Dashboard

Point out the stat cards (total users, total patients) and the three quick-action cards: User Management, Patient Records, Visits.

---

### Step 3 — User Management

Navigate to **Users** in the sidebar.

**What to show:**
- Full list — all accounts including their role badges (admin, doctor, patient)
- Search field — type `amina` → filters to Dr. Amina Halilovic
- Clear search

**Click "Add User"** and fill in the form (you do not need to save):

| Field | Value |
|---|---|
| Name | `Dr. Haris Mujanović` |
| Email | `haris.mujanovic@nutribase.test` |
| Password | `password` |
| Role | `Doctor` |

**What to say:** "Only admins can create accounts and assign roles. If I assign a wrong role or a duplicate email, the form returns inline validation errors."

Close/cancel the form without saving.

---

## SEGMENT 2 — Doctor Workflow (4 min)

### Step 4 — Switch to Doctor

Logout. Login with:

| Field | Value |
|---|---|
| Email | `doctor@nutribase.test` |
| Password | `password` |

**What to say:** "Doctors land on a different dashboard — they see their own visit schedule, not a system-wide user count."

---

### Step 5 — Patient List

Navigate to **Patients**.

- Show the paginated list of 5 patients
- Type `fat` in the search box → filters to **Fatima Hadžić**
- Click on **Fatima Hadžić**

---

### Step 6 — Add New Patient (live, 3-step wizard)

Go back to **Patients** and click **Add New Patient**.

**What to say:** "Doctors can onboard a brand-new patient end to end — this creates their login account and clinical record together in one transaction."

Walk through the three steps (do not need to save):

| Step | Fields |
|---|---|
| 1. Account | Name `Test Patient`, Email `test.patient@nutribase.test`, Password/Confirm `password123` |
| 2. Patient details | First/Last name, DOB, gender, phone, emergency contact |
| 3. Socioeconomic (optional) | Skip or fill in |

Click **Create Patient** on the final step — the new patient appears in the list immediately.

**What to say:** "Account creation is normally admin-only — but this is a purpose-built endpoint that can only ever create a patient-role account, so doctors get a safe, one-step onboarding flow without weakening that boundary anywhere else in the system."

---

### Step 7 — Fatima Hadžić — Full Profile

**What to say:** "Fatima is a 53-year-old teacher from Mostar, referred for Type 2 diabetes management and weight loss. This is the kind of record NutriBase is built around — clinical and social context in one place."

Walk through the tabs:

**Medical tab:**
- Blood type: O+
- Allergies: Shellfish, tree nuts
- Medical notes: *Type 2 diabetes, HbA1c 8.1% at referral. On Metformin 1000mg twice daily.*

**Socioeconomic tab:**
- Employment: Employed part-time (Teacher)
- Health insurance: Yes
- Food security: Secure
- Physical activity: Light

**What to say:** "Social determinants matter in nutrition — knowing she has insurance, stable food access, and family support directly shapes the dietary plan we'd recommend."

---

### Step 8 — Fatima's Visit History + Vital Signs Trend

Click the **Visits tab**.

**What to say:** "Fatima has 5 visits over 10 weeks. Watch what the data shows over time."

**Click the OLDEST visit** (10 weeks ago):

Show vital signs — point out the **red flags**:
- Systolic BP: **152** → flagged (normal: 90–140 mmHg)
- Diastolic BP: **94** → flagged (normal: 60–90 mmHg)
- Weight: 89.2 kg, BMI: **34.0** → flagged as Obese

**What to say:** "The system automatically evaluates each measurement against clinical reference ranges and flags anything outside bounds. At intake — high blood pressure, obese BMI."

Go back and **click the MOST RECENT visit** (2 weeks ago):

Show vital signs — all improving, flags gone or reduced:
- Systolic BP: **134** (was 152) — no longer flagged
- Diastolic BP: **84** (was 94)
- Weight: **81.6 kg** (was 89.2 kg) — **7.6 kg lost**
- HbA1c: improved from 8.1% to 7.4% (mentioned in notes)

**What to say:** "Eight weeks later — blood pressure normalised, 7.6 kg lost, HbA1c improving. The visit history is a clinical timeline, not just notes."

Point to the **Weight & BMI Trend chart** on the patient profile page.

---

### Step 9 — AI Diet Plan

Still on Fatima's profile, navigate to the **Diet Plans tab**.

Click **"Generate Diet Plan"**.

**What to say:** "The system accepts the request immediately with HTTP 202 and queues the generation. A background worker calls the OpenAI API with a structured prompt built from Fatima's clinical and socioeconomic profile — her diabetes, her allergies to shellfish and tree nuts, her activity level, her food security status."

- Show the **pending status badge** while it processes
- Once completed, click into the plan and show:
  - Clinical rationale
  - Daily calorie target and nutritional goals (protein / carbs / fat in grams)
  - Day-by-day meal schedule (breakfast, lunch, dinner, snack)
  - Clinical warnings

**What to say:** "The plan surfaces as a draft. The doctor reviews it, can edit any part of it, and only then sends it to the patient. The AI proposes — the clinician decides."

---

## SEGMENT 3 — Create New Visit + Vitals Live (1.5 min)

Still as Dr. Amina Halilovic, go back to **Fatima's Visits tab**.

Click **"Add Visit"** and fill in:

| Field | Value |
|---|---|
| Date | `2026-06-30` |
| Notes | `Ten-week programme review. Total weight loss 7.6 kg achieved and maintained. BP 132/83, now within normal range. HbA1c improved to 7.4%. Patient managing dietary plan independently. Transitioning to monthly follow-ups.` |

Save the visit.

Now **add vital signs** to this visit:

| Field | Value |
|---|---|
| Weight | `81.2` kg |
| Height | `162` cm |
| Systolic BP | `132` |
| Diastolic BP | `83` |
| Heart rate | `80` bpm |
| Temperature | `36.6` °C |

Save. Show that BMI is auto-computed and **no flags fire** — all values in normal range.

**What to say:** "That is the full create flow — a new clinical encounter documented and immediately part of Fatima's longitudinal record."

---

## SEGMENT 4 — Stefan Jovanović — Clinical Flags (1 min)

Navigate back to **Patients** and click **Stefan Jovanović**.

**What to say:** "Stefan is a 31-year-old student in an anorexia nervosa recovery programme. This is the opposite clinical picture."

Click the **Visits tab** and open the **first visit** (14 weeks ago — "Programme intake"):

Point out all the flags:
- Weight: **42.5 kg**, BMI: **13.4** → flagged as severely underweight
- Systolic BP: **86** → flagged (below 90)
- Diastolic BP: **52** → flagged (below 60)
- Heart rate: **54 bpm** → flagged (below 50–100, bradycardia)
- Temperature: **35.6°C** → flagged (below 36.0, hypothermia)

**What to say:** "Every measurement flagged at intake. Now look at the most recent visit."

Click the **latest visit** (2 weeks ago):
- Weight: **50.2 kg** (+7.7 kg gained)
- All vitals approaching normal — flags clearing

**What to say:** "Seven visits, 14 weeks, 7.7 kg gained. The trend chart tells the full recovery story — including the relapse at week 8 when academic stress triggered restriction episodes. That is the clinical value of longitudinal tracking."

---

## SEGMENT 5 — Patient View (30 sec)

Logout. Login with:

| Field | Value |
|---|---|
| Email | `fatima.hadzic@nutribase.test` |
| Password | `password` |

**What to show:**
- Patient lands directly on **their own profile** — no dashboard, no user list
- They can see their own visits and vital signs (read-only)
- Try clicking to the Patients list in the sidebar → **access denied / 403**

**What to say:** "Patients log in and see exactly one record — their own. They cannot navigate to any other patient's data. Role enforcement is not just a UI decision — the API returns 403 for any out-of-scope request, regardless of what the frontend shows."

---

## End

Hand off to questions.

**Things that may come up as Q&A:**

- *How does the queue worker know what job to run?* — Jobs are stored in the PostgreSQL `jobs` table. The worker polls it and dispatches by class name.
- *What happens if the OpenAI call fails?* — The job retries once. After two failures the plan status is set to `failed` and the failure reason is stored. The doctor sees a failed badge, not a broken UI.
- *How is cross-patient access prevented?* — PatientPolicy checks `$user->patient->id === $patient->id` at the controller level. A patient guessing another patient's ID gets HTTP 403 — the API never returns the data.
- *Why Railway and not something else?* — Nixpacks auto-detects PHP, so there is no Dockerfile to maintain. The queue worker is a second service running the same codebase with a different start command — no infrastructure overhead.
