# Tutorial 01: Laravel AI SDK Fundamentals

Before writing any feature code, you need to understand how the SDK is organized and why it works the way it does. This document explains the core concepts. If you understand them fully, the rest of the implementation will feel obvious.

---

## What is the Laravel AI SDK?

The `laravel/ai` package is the official Laravel SDK for interacting with large language model (LLM) providers. It supports Anthropic, OpenAI, Gemini, and others through a unified interface.

The key idea: **you write a PHP class, the SDK turns it into an API call, and gives you back the response**. You never write `curl` or `guzzle` requests. You never serialize JSON by hand. You never read HTTP headers. The SDK handles the protocol so you can focus on what the AI should do.

---

## Installation

From inside the `backend/` directory:

```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate
```

What this does:
1. Installs the package
2. Publishes `config/ai.php` (provider configuration)
3. Creates two migrations: `agent_conversations` and `agent_conversation_messages` — used for the conversation memory feature (not needed for this project, but created as part of setup)

Then add your key to `.env`:

```env
ANTHROPIC_API_KEY=sk-ant-...
AI_DEFAULT_PROVIDER=anthropic
```

---

## Core Concept 1: The Agent

Every AI interaction in the SDK is modelled as an **Agent**. An agent is a PHP class that implements the `Agent` contract and uses the `Promptable` trait.

Here is the absolute minimum:

```php
namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

class GreetingAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return 'You are a friendly assistant. Respond in one sentence.';
    }
}
```

Usage:

```php
$response = (new GreetingAgent)->prompt('Say hello to the doctor.');
echo (string) $response;  // "Hello, Doctor! How can I assist you today?"
```

**Why a class instead of a function call?**

Because each agent has *configuration* (which model to use, at what temperature, which provider), *context* (the system prompt), and potentially *injected data* (the patient record). Encapsulating this in a class means:
- The configuration is documented by the class itself
- You can inject dependencies through the constructor
- You can test the agent in isolation
- Multiple parts of your codebase can call the same agent consistently

---

## Core Concept 2: PHP Attributes for Configuration

Instead of passing configuration at call time, the SDK uses PHP 8 attributes to pin configuration directly on the class. This makes the agent self-describing.

```php
use Laravel\Ai\Attributes\{Model, Provider, Temperature};
use Laravel\Ai\Enums\Lab;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[Temperature(0.3)]
class DietPlanAgent implements Agent
{
    use Promptable;
    // ...
}
```

| Attribute | What it does | Why it matters |
|---|---|---|
| `#[Provider(Lab::Anthropic)]` | Pins to Anthropic's API | Without this, it uses the default provider from `config/ai.php` |
| `#[Model('claude-haiku-4-5-20251001')]` | Pins to Claude Haiku | Without this, it uses the default text model from config |
| `#[Temperature(0.3)]` | Controls randomness (0.0 = deterministic, 1.0 = creative) | 0.3 gives consistent, structured plans with mild variation |
| `#[MaxSteps(N)]` | Limits LLM calls per prompt | Only needed when using Tool Calls; not needed here |

**Important: there is no `#[UseCheapestModel]` attribute.** Some documentation examples you may find online refer to this, but it does not exist in the current SDK. Always pin the model explicitly with `#[Model('...')]`.

**Why low temperature (0.3) for diet plans?**

Temperature controls how "creative" the model is. At 0.0, it would give nearly identical output every time. At 1.0, it might hallucinate meals or give inconsistent structures. 0.3 gives consistent, medically reasonable plans while allowing natural language variation in meal descriptions. For structured output tasks (where you need consistent JSON shapes), always keep temperature low.

---

## Core Concept 3: The System Prompt

The `instructions()` method returns the **system prompt** — the message that defines the agent's role, constraints, and context. It runs before every user prompt.

The system prompt is where you inject patient data:

```php
public function instructions(): string
{
    $age = now()->diffInYears($this->patient->date_of_birth);
    
    return <<<PROMPT
    You are a clinical nutritionist generating a 7-day meal plan.
    
    Patient profile:
    - Age: {$age}
    - Allergies: {$this->patient->allergies ?? 'None'}
    
    Rules:
    - NEVER include any allergen in any meal
    - Meals must be affordable given the patient's income level
    PROMPT;
}
```

**Why inject data into the system prompt, not the user prompt?**

The user prompt (the argument to `->prompt()`) represents what the user is "asking". The system prompt is the agent's persistent context and rules. Putting patient data in the system prompt means:
- The rules apply to the entire interaction
- Hard constraints ("never include allergens") are harder for the model to ignore
- The structure is cleaner — the user prompt stays simple ("Generate the plan.")

This is a deliberate design decision documented in `research.md` as D2. The alternative (RAG / tool calls to fetch data on demand) adds complexity without benefit when the patient data fits comfortably in the system prompt.

---

## Core Concept 4: Structured Output

By default, agent responses are plain text. For machine consumption, you need structured JSON with a guaranteed shape.

The SDK provides this through the `HasStructuredOutput` interface and the `schema()` method:

```php
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\{Agent, HasStructuredOutput};

class DietPlanAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function schema(JsonSchema $schema): array
    {
        return [
            'summary'    => $schema->string()->required(),
            'risk_level' => $schema->string()->enum(['low', 'medium', 'high'])->required(),
        ];
    }
}
```

The `$schema` parameter is a builder that understands JSON Schema. You describe the shape of the output you expect. The SDK tells the model to respond in that shape.

**Accessing the response:**

```php
$response = (new DietPlanAgent)->prompt('Generate the plan.');

$summary   = $response['summary'];      // string
$riskLevel = $response['risk_level'];   // 'low'|'medium'|'high'

// Or as a PHP array
$data = $response->toArray();
```

The response is array-accessible, so `$response['summary']` works directly. `$response->toArray()` gives you the whole thing as a plain PHP array.

**How to scaffold a structured agent:**

```bash
php artisan make:agent DietPlanAgent --structured
```

This generates the class with `HasStructuredOutput` and an empty `schema()` method already wired up. Much faster than writing it from scratch.

---

## ⚠️ Critical: The SDK Does NOT Validate Structured Output

This is the most important thing in this entire tutorial. Read it twice.

**The SDK sends your schema to the model as instructions. It does NOT enforce or validate the response.**

The model can return:
- `daily_calories: "eighteen hundred"` (string instead of integer)
- `days: [...]` with 6 items instead of 7
- Missing required fields
- Out-of-range values

If you write the response directly to the database without checking, you get silent data corruption. Your DB has a record that claims to be a diet plan but has invalid data inside.

**You must always validate manually with Laravel Validator:**

```php
$response = (new DietPlanAgent($patient))->prompt('Generate the plan.');
$data = $response->toArray();

$validator = Validator::make($data, [
    'rationale'       => ['required', 'string'],
    'daily_calories'  => ['required', 'integer', 'between:1000,4000'],
    'days'            => ['required', 'array', 'size:7'],
    'days.*.day'      => ['required', 'string'],
    'days.*.breakfast'=> ['required', 'string'],
    // ...
]);

if ($validator->fails()) {
    // log it, retry, or mark as failed
    throw new ValidationException($validator);
}

// Only now is it safe to persist
$this->plan->update($validator->validated());
```

**Why does the SDK not validate automatically?**

Two reasons:
1. The SDK defines schema for the *model* using JSON Schema format, which is a documentation standard, not a PHP validation library. These are different ecosystems.
2. Validation rules like `between:1000,4000` or `size:7` go beyond what JSON Schema can express.

The validation step is your responsibility. Never skip it.

---

## Core Concept 5: Testing with Fakes

Real AI API calls are:
- Slow (2–10 seconds per call)
- Non-deterministic (different output every time)
- Expensive (costs real money)
- Network-dependent (tests fail in CI without internet)

For unit and feature tests, you never want real API calls. The SDK provides a fake mechanism:

```php
// In your test
DietPlanAgent::fake([json_encode([
    'rationale'         => 'Test rationale.',
    'daily_calories'    => 1800,
    'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
    'days'              => array_fill(0, 7, [
        'day'       => 'Monday',
        'breakfast' => 'Oatmeal',
        'lunch'     => 'Salad',
        'dinner'    => 'Chicken',
        'snack'     => 'Apple',
    ]),
    'warnings' => [],
])]);
```

When `DietPlanAgent::fake([...])` is set up:
- Any call to `(new DietPlanAgent)->prompt(...)` returns the faked response
- No network request is made
- The response is deterministic

For structured output fakes specifically, the response must be JSON-encoded as a string inside the outer array. This is because the SDK wraps structured output responses in an outer envelope.

**Additional assertions:**

```php
DietPlanAgent::assertPrompted();  // was the agent called at all?
DietPlanAgent::assertNeverPrompted();  // was the agent NOT called?
DietPlanAgent::assertPrompted(fn ($p) => str_contains($p->prompt, 'Generate'));
```

**Testing failure scenarios:**

To test what happens when the AI returns bad data:

```php
// Returns a response with only 3 days instead of 7
DietPlanAgent::fake([json_encode([
    'rationale'         => 'Short plan.',
    'daily_calories'    => 1800,
    'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
    'days'              => array_fill(0, 3, [...]),  // INVALID: only 3 days
    'warnings'          => [],
])]);
```

This triggers your validation failure path without any real API call.

---

## Putting it together

Here is the full lifecycle of an agent call in this system:

```
Controller: POST /api/patients/1/diet-plans
    ↓
Create PatientDietPlan record (status: pending)
Dispatch GenerateDietPlanJob to queue
Return 202

Queue worker picks up GenerateDietPlanJob
    ↓
Load patient + socioeconomic data
(new DietPlanAgent($patient))->prompt('Generate the plan.')
    ↓ (SDK takes over)
Build system prompt from instructions()
Send to Anthropic API with structured output schema
Receive JSON response
    ↓ (back in your code)
Validator::make($response->toArray(), [...])
    ↓ if valid
Update PatientDietPlan to completed
    ↓ if invalid (retry logic)
Try once more
    ↓ if still invalid
Update PatientDietPlan to failed
```

Every box labeled "back in your code" is where you write PHP. Everything labeled "SDK takes over" is handled by the library.

---

## Artisan generators reference

Use these to scaffold files instead of creating them from scratch:

```bash
# Create a basic agent
php artisan make:agent DietPlanAgent

# Create a structured output agent (generates HasStructuredOutput + schema() scaffold)
php artisan make:agent DietPlanAgent --structured

# Create agent middleware (for logging, rate limiting, etc.)
php artisan make:agent-middleware LogDietPlanUsage
```

Continue to: [Building the Diet Plan Agent →](02-the-diet-plan-agent.md)
