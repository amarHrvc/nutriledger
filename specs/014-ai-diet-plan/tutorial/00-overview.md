# Tutorial: AI Diet Plan Generator — Overview

This tutorial teaches you to implement Feature 014 (AI Diet Plan Generator) step by step. By the end you will have built a real asynchronous AI feature using the Laravel AI SDK and understand *why* every design decision was made.

---

## What you are building

A doctor clicks "Generate Diet Plan" on a patient's profile page. The system:

1. Creates a database record immediately (status: `pending`)
2. Returns HTTP 202 to the frontend instantly — the doctor does not wait
3. Dispatches a background queue job
4. The job builds an AI agent, injects all of the patient's clinical and socioeconomic data into a system prompt, and asks Claude Haiku to generate a structured 7-day meal plan
5. The agent response is validated, then the record is updated to `completed` (with the plan data) or `failed` (with a failure reason)
6. The frontend polls the list endpoint every 3 seconds until the status changes

The result is stored in a `patient_diet_plans` history table — every generation attempt is preserved.

---

## Tutorial documents

Read them in order. Each one builds on the last.

| # | Document | What you learn |
|---|---|---|
| 01 | [Laravel AI SDK Fundamentals](01-laravel-ai-sdk-fundamentals.md) | How the SDK works conceptually: agents, attributes, structured output, testing with fakes |
| 02 | [Building the Diet Plan Agent](02-the-diet-plan-agent.md) | Creating `DietPlanAgent` with structured output, writing the system prompt, defining the schema |
| 03 | [The Queue Job & Validation](03-the-queue-job.md) | Why we use a queued job, manual retry logic, why you must validate structured output yourself |
| 04 | [The API Layer](04-the-api-layer.md) | Controller, FormRequest, Policy, routes, the 202 response pattern |
| 05 | [Testing Strategy](05-testing-strategy.md) | How to test AI features without hitting real APIs — `Agent::fake()`, `Queue::fake()`, failure scenario testing |
| 06 | [Frontend Integration](06-frontend-integration.md) | The React polling pattern, DietPlanSection component, displaying 7-day meal grids |

---

## Prerequisites

Before starting, make sure you have:

- PHP 8.3 and Composer
- Laravel 12 project running (`php artisan serve` works)
- MySQL database connected and migrated
- Queue driver set to `database` (`QUEUE_CONNECTION=database` in `.env`)
- An Anthropic API key (free tier works for development)
- Basic familiarity with Laravel controllers, models, and Eloquent
- Basic familiarity with Laravel Queues (you know what `ShouldQueue` means)

You do **not** need prior experience with AI SDKs or LLM APIs. That is what this tutorial teaches.

---

## The pattern you are learning

The specific feature is a diet plan generator, but the pattern you are learning is reusable for any AI feature that:

- Takes structured input (patient data, document, form)
- Calls an AI model to produce structured output (JSON with a defined shape)
- Needs to run asynchronously (the response takes seconds, the request must return in milliseconds)
- Needs to survive AI unreliability (the model can return malformed output — you must validate and retry)

This same pattern applies to: clinical note summarization, risk assessment scoring, automated report generation, document classification. Once you understand it here, you can apply it anywhere.

---

## The tech stack

| Layer | Technology | Why |
|---|---|---|
| AI SDK | `laravel/ai` | Official Laravel package; works with Anthropic, OpenAI, and others via a unified API |
| AI Model | Claude Haiku (`claude-haiku-4-5-20251001`) | Fast and cheap for structured output tasks; ideal for high-frequency clinical features |
| Queue | Laravel Database Queue | Already configured; no Redis required for this project |
| Storage | MySQL `patient_diet_plans` table with JSON columns | Plan content is opaque to the relational layer — JSON avoids unnecessary normalization |
| Testing | Pest + `Agent::fake()` + `Queue::fake()` | No real API calls in tests — deterministic, free, fast |

---

## What the final code looks like

Here is the full user journey with the actual HTTP exchanges:

```
# 1. Doctor triggers generation
POST /api/patients/1/diet-plans
Authorization: Bearer <token>

→ 202 Accepted
{
  "message": "Diet plan generation started.",
  "status": 202,
  "data": {
    "diet_plan": { "id": 42, "status": "pending", "created_at": "..." }
  }
}

# 2. Frontend polls (plan still generating)
GET /api/patients/1/diet-plans
→ 200 OK
{ "data": [{ "id": 42, "status": "pending", ... }] }

# 3. Frontend polls again (job finished)
GET /api/patients/1/diet-plans
→ 200 OK
{
  "data": [{
    "id": 42,
    "status": "completed",
    "daily_calories": 1800,
    "nutritional_goals": { "protein_g": 90, "carbs_g": 220, "fat_g": 60 },
    "generated_by": { "id": 3, "name": "Dr. Amira Halilović" },
    ...
  }]
}

# 4. Doctor opens full plan
GET /api/patients/1/diet-plans/42
→ 200 OK  (includes rationale, full 7-day meals, warnings)
```

---

## A note on cost

Claude Haiku is extremely cheap for structured output:
- ~$0.25 per million input tokens, ~$1.25 per million output tokens
- A single diet plan generation uses approximately 1,000–1,500 tokens total
- **Cost per plan: ~$0.0003 (three hundredths of a cent)**

At this price, 1,000 plan generations cost about $0.30. There is no meaningful cost concern for a clinical application with a few dozen doctors.

Continue to: [Laravel AI SDK Fundamentals →](01-laravel-ai-sdk-fundamentals.md)
