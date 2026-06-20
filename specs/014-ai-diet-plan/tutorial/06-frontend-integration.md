# Tutorial 06: Frontend Integration

The backend returns 202 and processes the plan asynchronously. The frontend must know about this: it cannot just wait for a 200 response. This document explains the polling pattern and the React component structure.

---

## The polling pattern

Since the plan is generated asynchronously, the frontend must check the status periodically. The approach:

1. Doctor clicks "Generate Diet Plan" → POST to the API
2. API returns 202 immediately with `{ status: "pending", id: 42 }`
3. Frontend starts polling `GET /api/patients/1/diet-plans` every 3 seconds
4. When the response includes a plan with `status !== "pending"`, stop polling
5. If `status === "completed"`, render the plan
6. If `status === "failed"`, show an error with a retry button

This is the simplest reliable approach for async results. The alternatives — WebSockets (Reverb) and Server-Sent Events — add infrastructure complexity that isn't justified for a POC. Polling with a 3-second interval is unobtrusive and works everywhere.

**When to stop polling:**
- `status === "completed"` — success
- `status === "failed"` — failure
- Component unmounts — avoid memory leaks from dangling intervals

---

## Component structure

Three components, each with a single responsibility:

```
DietPlanSection/
├── DietPlanSection.jsx   ← container: owns data, polling, generate action
├── DietPlanCard.jsx      ← renders a completed plan (receives plan as prop)
└── DietPlanHistory.jsx   ← renders the list of past plans (receives array as prop)
```

The container (`DietPlanSection`) owns all the state and side effects. The child components are purely presentational — they receive data as props and render it. This follows the "smart/dumb" component pattern: one component that knows about the API, others that just know about HTML.

---

## DietPlanSection.jsx

```jsx
import { useState, useEffect, useRef } from 'react';
import DietPlanCard from './DietPlanCard';
import DietPlanHistory from './DietPlanHistory';

export default function DietPlanSection({ patientId }) {
    const [plans, setPlans] = useState([]);
    const [loading, setLoading] = useState(true);
    const [generating, setGenerating] = useState(false);
    const [error, setError] = useState(null);
    const pollingRef = useRef(null);

    const fetchPlans = async () => {
        const response = await fetch(`/api/patients/${patientId}/diet-plans`, {
            headers: { Authorization: `Bearer ${getToken()}` },
        });
        const json = await response.json();
        setPlans(json.data ?? []);
        return json.data ?? [];
    };

    const startPolling = () => {
        if (pollingRef.current) return;  // already polling
        pollingRef.current = setInterval(async () => {
            const latestPlans = await fetchPlans();
            const latest = latestPlans[0];
            if (!latest || latest.status !== 'pending') {
                stopPolling();
            }
        }, 3000);
    };

    const stopPolling = () => {
        clearInterval(pollingRef.current);
        pollingRef.current = null;
    };

    useEffect(() => {
        fetchPlans()
            .then(latestPlans => {
                setLoading(false);
                if (latestPlans[0]?.status === 'pending') {
                    startPolling();
                }
            });
        return () => stopPolling();  // cleanup on unmount
    }, [patientId]);

    const handleGenerate = async () => {
        setGenerating(true);
        setError(null);

        const response = await fetch(`/api/patients/${patientId}/diet-plans`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${getToken()}` },
        });

        if (response.status === 202) {
            await fetchPlans();  // refresh list to show the new pending entry
            startPolling();
        } else {
            setError('Could not start generation. Please try again.');
        }

        setGenerating(false);
    };

    if (loading) {
        return <div>Loading diet plans...</div>;
    }

    const latest = plans[0];
    const isPending = latest?.status === 'pending';
    const isCompleted = latest?.status === 'completed';
    const isFailed = latest?.status === 'failed';
    const history = plans.slice(1);

    return (
        <section>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <h2>Diet Plans</h2>
                <button
                    onClick={handleGenerate}
                    disabled={generating || isPending}
                >
                    {generating ? 'Starting...' : isPending ? 'Generating...' : 'Generate Diet Plan'}
                </button>
            </div>

            {error && <div className="error">{error}</div>}

            {isPending && (
                <div className="loading-state">
                    Generating your patient's diet plan... This takes about 10–30 seconds.
                </div>
            )}

            {isCompleted && <DietPlanCard plan={latest} onRegenerate={handleGenerate} />}

            {isFailed && (
                <div className="error-state">
                    <p>Plan generation failed: {latest.failure_reason}</p>
                    <button onClick={handleGenerate}>Try Again</button>
                </div>
            )}

            {history.length > 0 && <DietPlanHistory plans={history} />}
        </section>
    );
}
```

**Key design decisions:**

`pollingRef` is a `useRef`, not `useState`. This is deliberate — changing a ref does not trigger a re-render. The interval ID is implementation plumbing, not UI state. If you stored it in `useState`, updating it would cause an unnecessary re-render every 3 seconds.

The `startPolling()` guard `if (pollingRef.current) return` prevents double-polling if the function is called twice (e.g., generate button clicked while already polling).

The `useEffect` cleanup `return () => stopPolling()` runs when the component unmounts. Without this, the interval continues running after the user navigates away, causing memory leaks and potential errors on stale closures.

---

## DietPlanCard.jsx

```jsx
export default function DietPlanCard({ plan, onRegenerate }) {
    const days = plan.days ?? [];
    const goals = plan.nutritional_goals ?? {};
    const warnings = plan.warnings ?? [];

    return (
        <article>
            {/* Rationale */}
            {plan.rationale && (
                <blockquote>
                    <em>{plan.rationale}</em>
                </blockquote>
            )}

            {/* Macro summary */}
            <div className="macros">
                <strong>{plan.daily_calories} kcal/day</strong>
                {goals.protein_g && <span>Protein: {goals.protein_g}g</span>}
                {goals.carbs_g && <span>Carbs: {goals.carbs_g}g</span>}
                {goals.fat_g && <span>Fat: {goals.fat_g}g</span>}
            </div>

            {/* Warnings */}
            {warnings.length > 0 && (
                <div className="warnings">
                    {warnings.map((warning, i) => (
                        <span key={i} className="badge badge-warning">{warning}</span>
                    ))}
                </div>
            )}

            {/* 7-day meal grid */}
            <table>
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Breakfast</th>
                        <th>Lunch</th>
                        <th>Dinner</th>
                        <th>Snack</th>
                    </tr>
                </thead>
                <tbody>
                    {days.map((day, i) => (
                        <tr key={i}>
                            <td><strong>{day.day}</strong></td>
                            <td>{day.breakfast}</td>
                            <td>{day.lunch}</td>
                            <td>{day.dinner}</td>
                            <td>{day.snack}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Actions */}
            <div className="actions">
                <button onClick={onRegenerate}>Regenerate</button>
                <button disabled title="Coming soon">Export PDF</button>
            </div>
        </article>
    );
}
```

---

## DietPlanHistory.jsx

```jsx
export default function DietPlanHistory({ plans }) {
    if (plans.length === 0) return null;

    return (
        <section>
            <h3>Previous Generations</h3>
            <ul>
                {plans.map(plan => (
                    <li key={plan.id}>
                        <span>{new Date(plan.created_at).toLocaleDateString()}</span>
                        <span>{plan.generated_by?.name ?? 'Unknown doctor'}</span>
                        <span className={`badge badge-${plan.status}`}>{plan.status}</span>
                        {plan.status === 'failed' && (
                            <small title={plan.failure_reason}>⚠ Generation failed</small>
                        )}
                    </li>
                ))}
            </ul>
        </section>
    );
}
```

---

## Using the orval-generated API client

This project generates a type-safe API client from the backend's OpenAPI spec:

```bash
# From frontend/
pnpm run api:generate
```

If the API client has been regenerated after implementing the backend, use the generated functions instead of manual `fetch`:

```jsx
// Instead of:
const response = await fetch(`/api/patients/${patientId}/diet-plans`, {...});

// Use the generated client:
import { getDietPlans, storeDietPlan } from '@/api/generated';

const { data } = await getDietPlans(patientId);
```

Check the generated client at `frontend/src/api/generated/` to see if the diet plan endpoints were added after running `api:generate`.

---

## Integration into the patient profile page

Wire `DietPlanSection` into the patient detail page:

```jsx
// In PatientProfilePage.jsx (or equivalent)
import DietPlanSection from './components/DietPlanSection/DietPlanSection';

export default function PatientProfilePage({ params }) {
    const patientId = params.id;
    
    return (
        <main>
            {/* ... existing patient info, visits section, etc. */}
            
            <DietPlanSection patientId={patientId} />
        </main>
    );
}
```

---

## What the user sees

**No plans yet:**
- Button: "Generate Diet Plan" (enabled)
- Empty history

**After clicking Generate (pending):**
- Button: "Generating..." (disabled)
- Message: "Generating your patient's diet plan... This takes about 10–30 seconds."
- Poll indicator (spinner or animated dots)

**After generation completes:**
- Button: "Regenerate" (enabled)
- DietPlanCard showing: rationale, macros, 7-day table, warnings
- History list below (previous plans, if any)

**After generation fails:**
- Error message with failure reason
- "Try Again" button (enabled)
- History list still shows the failed entry

---

## Testing the frontend

Manual testing (open patient profile in browser):
1. Start backend: `composer run dev`
2. Start queue worker: `php artisan queue:work` (in a separate terminal)
3. Start frontend: `pnpm run dev`
4. Log in as a doctor
5. Open a patient profile
6. Click "Generate Diet Plan"
7. Watch the button become "Generating..."
8. Wait for the plan to appear (10–30 seconds)
9. Verify the 7-day grid, macros, and rationale render correctly

To test failure handling, temporarily modify `GenerateDietPlanJob` to always fail:
```php
// Temporary, for testing only
$this->plan->update(['status' => 'failed', 'failure_reason' => 'Test failure']);
return;
```

This lets you see the failure state without waiting for real AI calls.

---

## Summary of what you built

| Layer | What it does |
|---|---|
| `DietPlanAgent` | Sends patient data to Claude Haiku, returns structured JSON |
| `GenerateDietPlanJob` | Runs the agent asynchronously, validates output, retries once, saves result |
| `DietPlanController` | Accepts POST (202), returns paginated list, returns single plan |
| `DietPlanPolicy` | Restricts access to doctors and admins; blocks patients |
| `DietPlanSummaryResource` | List response — fast, omits heavy fields |
| `DietPlanResource` | Detail response — full plan data |
| `DietPlanSection` | React container — owns polling state, renders children |
| `DietPlanCard` | Renders a completed plan (table + macros + warnings) |
| `DietPlanHistory` | Renders list of previous generations |

The pattern you learned here — async job with structured output, status polling, and graceful failure handling — is the standard way to integrate AI generation into a web application. It applies directly to any future feature in this project that calls an LLM.

---

## Where to go next

- **Agent middleware**: Add a logging middleware (`php artisan make:agent-middleware LogDietPlanUsage`) to track token usage per generation. See `_docs/_Analysis/Laravel_Ai_Agent/sdk-index/08-production.md`.
- **Allergen scanning**: Post-generation scan of the `days` array for known allergens before saving. Fills the gap that prompt engineering cannot guarantee.
- **Token cost tracking**: Use `$response->usage->totalTokens` in a middleware to record cost per generation in a `ai_usages` table.
- **Export PDF**: Replace the placeholder button with actual PDF generation using `barryvdh/laravel-dompdf`.
