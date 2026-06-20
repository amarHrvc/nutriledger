# 3. APPLICATION DESIGN

This chapter presents the structural and behavioural models of the NutriBase system through UML diagrams. The diagrams cover the use case model, key activity flows, the domain class structure, and the sequence of interactions between system components for the primary API operations.

## 3.1. Use Case Diagram

The NutriBase system has three primary actors: **Admin**, **Doctor (Doktor)**, and **Patient (Pacijent)**. The Admin actor has the broadest access, managing user accounts, patient records, and visit data. The Doctor actor manages patient records and owns visit records they create. The Patient actor has read-only access restricted to their own profile and visit history. Figure 3.1 illustrates the complete use case model for the MVP scope.

[INSERT FIGURE 3.1 — render from _docs_uni/diagrams/sd-mvp/use-case/system.mmd]

*Figure 3.1. Use Case Diagram — NutriBase MVP*

## 3.2. Activity Diagrams

### 3.2.1. User Login

Figure 3.2.1 illustrates the user login flow from credential submission through to role-based redirection. The diagram covers client-side validation, server-side credential and account status checks, Sanctum token issuance stored in an httpOnly cookie, and the role-aware redirect that sends admin and doctor users to the dashboard home while patients are intercepted by the PatientRedirectGuard and redirected to their own patient profile page.

[INSERT FIGURE 3.2.1 — render from _docs_uni/diagrams/activity/login.mmd]

*Figure 3.2.1. Activity Diagram — User Login*

### 3.2.2. Register New Patient

Figure 3.2.2 illustrates the patient registration flow, from form submission by an admin or doctor through to the atomic creation of both the patient record and the associated socioeconomic profile, or the return of validation errors if the input is invalid.

[INSERT FIGURE 3.2.2 — render from _docs_uni/diagrams/activity/create-patient.mmd]

*Figure 3.2.2. Activity Diagram — Register New Patient*

### 3.2.3. Create Visit Record

Figure 3.2.3 illustrates the visit creation flow, from a doctor submitting visit details for a specific patient through to the persisted visit record being returned as a JSON response, or the rejection of the request if the authenticated user is not authorised to create visits for that patient.

[INSERT FIGURE 3.2.3 — render from _docs_uni/diagrams/activity/create-visit.mmd]

*Figure 3.2.3. Activity Diagram — Create Visit Record*

### 3.2.4. Generate AI Diet Plan

Figure 3.2.4 illustrates the AI diet plan generation flow. When an authorised user triggers generation, the system immediately dispatches a `GenerateDietPlanJob` to the queue and returns a 202 response. The frontend begins polling for status while the queued job invokes the `DietPlanAgent`, which calls the Claude API to produce a structured diet plan. Once the job completes, the plan record is updated to `completed` status and the generated content is displayed to the user. The asynchronous design ensures the HTTP request cycle is not blocked by the AI inference time.

[INSERT FIGURE 3.2.4 — render from _docs_uni/diagrams/sd-full/activity/generate-diet-plan.mmd]

*Figure 3.2.4. Activity Diagram — Generate AI Diet Plan*

### 3.2.5. Edit Diet Plan

Figure 3.2.5 illustrates the diet plan editing flow. After a plan has been generated, authorised users (admin or doctor) may open an edit form pre-populated with the existing content, modify it, and submit a PATCH request. The diagram covers the authorisation gate that hides the edit action from patients, inline validation errors on submission, and the updated plan card rendered on success.

[INSERT FIGURE 3.2.5 — render from _docs_uni/diagrams/sd-full/activity/edit-diet-plan.mmd]

*Figure 3.2.5. Activity Diagram — Edit Diet Plan*

### 3.2.6. Send Diet Plan Email

Figure 3.2.6 illustrates the diet plan email delivery flow. Once a completed plan exists, an authorised user may trigger delivery via a send action. The system creates a `DietPlanDelivery` record with status `pending` and dispatches a `SendDietPlanEmailJob` to the queue, returning a 202 immediately. The frontend reflects the pending state via a delivery badge. The queued job sends the email through Laravel Mail and updates the delivery record to `sent` or `failed` depending on the outcome, with the badge updating accordingly.

[INSERT FIGURE 3.2.6 — render from _docs_uni/diagrams/sd-full/activity/send-diet-plan-email.mmd]

*Figure 3.2.6. Activity Diagram — Send Diet Plan Email*

### 3.2.7. Clinical Workflow

Figure 3.2.7 illustrates the end-to-end clinical workflow that spans all three core entities of the system. An admin begins by creating a user account with an assigned role, after which an admin or doctor registers the patient along with their socioeconomic profile. The doctor then navigates to the patient profile and creates a visit record. The diagram concludes with the optional step of recording vital signs for that visit, which triggers BMI calculation and persists the measurements. This diagram represents the primary usage path of the system in a clinical session.

[INSERT FIGURE 3.2.7 — render from _docs_uni/diagrams/sd-full/activity/clinical-workflow.mmd]

*Figure 3.2.7. Activity Diagram — Clinical Workflow*

## 3.3. Class Diagram

The domain model consists of four entities and their relationships. `User` has a one-to-one relationship with `Patient`; `Patient` has a one-to-one relationship with `PatientSocioeconomic`; and `Patient` has a one-to-many relationship with `Visit`, where each `Visit` also references the `User` (doctor) who created it. Figure 3.3 shows all four model classes with their attributes, key methods, and associations.

[INSERT FIGURE 3.3 — render from _docs_uni/diagrams/class/domain.mmd]

*Figure 3.3. Class Diagram — Domain Model*

## 3.4. Sequence Diagrams

### 3.4.1. API Login

Figure 3.4.1 illustrates the interactions between the React SPA client and the Laravel API server components during the login flow. It shows the request path through the routing layer, the `LoginRequest` form request, the `AuthController`, the `User` model, and the Sanctum token issuance, culminating in the token being returned inside a `UserResource` envelope.

[INSERT FIGURE 3.4.1 — render from _docs_uni/diagrams/sd-mvp/sequence/api-login.mmd]

*Figure 3.4.1. Sequence Diagram — API Login*

### 3.4.2. Create Patient via API

Figure 3.4.2 illustrates the interactions between the client and the API server during patient creation. It shows the request passing through `auth:sanctum` middleware, the `StorePatientRequest` authorization and validation, the `PatientController`, the `PatientService` which atomically creates both the `Patient` and `PatientSocioeconomic` records within a database transaction, and the `PatientResource` that formats the response.

[INSERT FIGURE 3.4.2 — render from _docs_uni/diagrams/sd-mvp/sequence/api-create-patient.mmd]

*Figure 3.4.2. Sequence Diagram — Create Patient via API*

### 3.4.3. Create Visit via API

Figure 3.4.3 illustrates the interactions between the client and the API server during visit creation. It shows the nested route resolution that binds both the parent `Patient` and the `Visit` scope, the `StoreVisitRequest` authorization check that verifies the authenticated user is a doctor, the auto-assignment of `doctor_id` from the authenticated user rather than from the request body, and the `VisitResource` response.

[INSERT FIGURE 3.4.3 — render from _docs_uni/diagrams/sd-mvp/sequence/api-create-visit.mmd]

*Figure 3.4.3. Sequence Diagram — Create Visit via API*
