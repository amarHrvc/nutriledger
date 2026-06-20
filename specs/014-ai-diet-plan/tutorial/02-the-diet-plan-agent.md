# Tutorial 02: Building the Diet Plan Agent

The agent is the AI's brain for this feature. It knows what to generate (through the system prompt) and what shape to generate it in (through the schema). This document explains every design decision.

---

## Step 1: Scaffold the agent

Run this from `backend/`:

```bash
php artisan make:agent DietPlanAgent --structured
```

This creates `app/Ai/Agents/DietPlanAgent.php` with `HasStructuredOutput` already implemented and an empty `schema()` method. Edit it from there — don't write the boilerplate by hand.

---

## Step 2: Pin the model and provider

Add the PHP attributes at the top of the class:

```php
namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\{Model, Provider, Temperature};
use Laravel\Ai\Contracts\{Agent, HasStructuredOutput};
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

use App\Models\Patient;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[Temperature(0.3)]
class DietPlanAgent implements Agent, HasStructuredOutput
{
    use Promptable;
    
    public function __construct(private Patient $patient) {}
    
    // instructions() and schema() to come
}
```

**Why these attributes?**

- `#[Provider(Lab::Anthropic)]` — pins to Anthropic so a future change to `config/ai.php` default won't accidentally switch this agent to a different provider
- `#[Model('claude-haiku-4-5-20251001')]` — Claude Haiku is fast (~2–5 seconds), cheap (~$0.0003/generation), and handles structured output well; pinning the model string means a config file change won't silently change cost or behaviour
- `#[Temperature(0.3)]` — low enough for consistent structure, high enough for natural meal variety

**There is no `#[UseCheapestModel]` attribute.** If you've seen this in other code or documentation, it doesn't exist in `laravel/ai`. You must pin the model explicitly.

---

## Step 3: Inject patient data through the constructor

```php
public function __construct(private Patient $patient)
{
    // Patient must be loaded with its socioeconomic relation
    // before this agent is instantiated:
    // $patient->loadMissing('socioeconomic')
}
```

The agent receives a `Patient` model with its `socioeconomic` relation already eager-loaded. The job that calls this agent is responsible for loading the relation before constructing the agent.

**Why constructor injection instead of passing data at prompt time?**

The patient data is *context* for the agent, not the *request* to the agent. The system prompt (context) describes who the patient is and what constraints apply. The user prompt (request) is the simple instruction "Generate the plan."

Keeping these separate makes the code cleaner and the agent more testable — you can unit-test the `instructions()` output for a given patient without calling the API at all.

---

## Step 4: Build the system prompt

The `instructions()` method returns the system prompt that sets the agent's role and injects the patient's clinical and socioeconomic profile:

```php
public function instructions(): string
{
    $socio = $this->patient->socioeconomic;
    $age = now()->diffInYears($this->patient->date_of_birth);

    return <<<PROMPT
    You are a clinical nutritionist generating a 7-day meal plan for a specific patient.
    
    ## Patient Profile
    
    Age: {$age} years
    Gender: {$this->patient->gender}
    Blood Type: {$this->patient->blood_type}
    Allergies: {$this->patient->allergies ?? 'None'}
    Medical Notes: {$this->patient->medical_notes ?? 'None'}
    Dietary Restrictions: {$this->patient->dietary_restrictions ?? 'None'}
    
    ## Socioeconomic Context
    
    Food Security Status: {$socio?->food_security_status ?? 'Unknown'}
    Income Level: {$socio?->income_level ?? 'Unknown'}
    Physical Activity Level: {$socio?->physical_activity_level ?? 'Unknown'}
    Smoking: {$socio?->smoking_status ?? 'Unknown'}
    Alcohol Consumption: {$socio?->alcohol_consumption ?? 'Unknown'}
    
    ## Rules (NON-NEGOTIABLE)
    
    1. NEVER include any item from the patient's allergy list in any meal, snack, or ingredient
    2. All meals must be realistically affordable given the patient's food security status and income level
    3. Generate exactly 7 days, one for each day Monday through Sunday
    4. Every meal field (breakfast, lunch, dinner, snack) must be a specific food description — not "balanced meal" or similar generic text
    5. Include clinical warnings in the warnings array — e.g., if the patient is food-insecure, note that affordable staples are used; if allergies were excluded, note them
    PROMPT;
}
```

**Design notes on the system prompt:**

- **Age is computed, not stored** — `now()->diffInYears($this->patient->date_of_birth)` calculates the current age dynamically. The DB stores birth date, not age, because age changes.
- **`?? 'Unknown'` fallbacks** — If socioeconomic data hasn't been collected yet, the prompt still works. The agent will generate a generic plan rather than failing.
- **Hard rules section** — The "NON-NEGOTIABLE" label tells the model these constraints must not be violated. This is prompt engineering: models are more compliant with clearly labelled constraints than with inline prose.
- **"Exactly 7 days"** — This instruction redundantly reinforces the schema constraint. Redundancy helps with consistency — the model is told once in the schema and once in the prompt.

**What data is injected and why:**

| Field | Why it matters for diet planning |
|---|---|
| `allergies` | Direct safety constraint — allergen presence in meals is harmful |
| `dietary_restrictions` | Religious, ethical, or medical restrictions (halal, vegan, low-sodium) |
| `food_security_status` | Determines whether expensive ingredients are appropriate |
| `income_level` | Informs meal complexity and ingredient cost |
| `physical_activity_level` | Affects caloric needs (sedentary vs. active) |
| `medical_notes` | Conditions like diabetes or hypertension affect acceptable foods |
| `blood_type` | Some clinical nutrition frameworks use blood type as guidance |
| `smoking_status`, `alcohol_consumption` | May require specific nutrient supplementation (e.g., B vitamins for smokers) |

---

## Step 5: Define the output schema

The `schema()` method tells the model exactly what structure to return:

```php
public function schema(JsonSchema $schema): array
{
    return [
        'rationale' => $schema->string()->required(),

        'daily_calories' => $schema->integer()->required(),

        'nutritional_goals' => $schema->object([
            'protein_g' => $schema->integer()->required(),
            'carbs_g'   => $schema->integer()->required(),
            'fat_g'     => $schema->integer()->required(),
        ])->required(),

        'days' => $schema->array(
            $schema->object([
                'day'       => $schema->string()->required(),
                'breakfast' => $schema->string()->required(),
                'lunch'     => $schema->string()->required(),
                'dinner'    => $schema->string()->required(),
                'snack'     => $schema->string()->required(),
            ])
        )->required(),

        'warnings' => $schema->array($schema->string())->required(),
    ];
}
```

**Field-by-field explanation:**

`rationale` — A prose explanation of why this plan was designed the way it is, given the patient's profile. This surfaces the AI's reasoning to the doctor, who can then review whether the plan makes medical sense for their patient. It's not just data — it's clinical accountability.

`daily_calories` — The target total calories per day. This is validated later to be between 1,000 and 4,000 kcal. Values outside this range indicate a model error (a 200-calorie plan is physiologically impossible; a 10,000-calorie plan is nonsensical).

`nutritional_goals` — Macro targets (protein, carbs, fat in grams). These help the doctor and patient understand the dietary approach at a glance without reading all 7 days.

`days` — The actual plan: 7 objects, one per day (Monday–Sunday), each with `breakfast`, `lunch`, `dinner`, and `snack` as descriptive strings.

`warnings` — An array of clinical notices. Examples: "Patient is food-insecure — plan uses affordable staples only." or "Shellfish excluded due to patient allergy." This is the AI signalling to the doctor that it applied specific constraints, making the plan reviewable.

**Why `$schema->array($schema->object([...]))` instead of just `$schema->array()`?**

The outer `array()` call takes a type argument describing the array's element type. Without it, the model might return an array of strings, numbers, or mixed types. With `array($schema->object([...]))`, each element must be an object with the specified fields. This significantly reduces the chance of malformed output.

**Why are all fields marked `->required()`?**

If a field is not marked required, the model may omit it — especially for the `warnings` field (it might think "no warnings needed" and skip the field entirely). Marking everything required forces a consistent shape every time.

---

## Step 6: The full agent class

Putting it all together:

```php
<?php

namespace App\Ai\Agents;

use App\Models\Patient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\{Model, Provider, Temperature};
use Laravel\Ai\Contracts\{Agent, HasStructuredOutput};
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[Temperature(0.3)]
class DietPlanAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(private Patient $patient) {}

    public function instructions(): string
    {
        $socio = $this->patient->socioeconomic;
        $age = now()->diffInYears($this->patient->date_of_birth);

        return <<<PROMPT
        You are a clinical nutritionist generating a 7-day meal plan.
        
        Patient: age {$age}, gender {$this->patient->gender}, blood type {$this->patient->blood_type}
        Allergies: {$this->patient->allergies ?? 'None'}
        Medical Notes: {$this->patient->medical_notes ?? 'None'}
        Dietary Restrictions: {$this->patient->dietary_restrictions ?? 'None'}
        Food Security: {$socio?->food_security_status ?? 'Unknown'}
        Income Level: {$socio?->income_level ?? 'Unknown'}
        Activity: {$socio?->physical_activity_level ?? 'Unknown'}
        Smoking: {$socio?->smoking_status ?? 'Unknown'}
        Alcohol: {$socio?->alcohol_consumption ?? 'Unknown'}
        
        Rules:
        1. NEVER include allergens from the patient's allergy list
        2. Meals must be affordable for the patient's income and food security level
        3. Generate exactly 7 days (Monday through Sunday)
        4. Each meal field must be a specific food, not generic descriptions
        5. Document any constraints applied in the warnings array
        PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'rationale'         => $schema->string()->required(),
            'daily_calories'    => $schema->integer()->required(),
            'nutritional_goals' => $schema->object([
                'protein_g' => $schema->integer()->required(),
                'carbs_g'   => $schema->integer()->required(),
                'fat_g'     => $schema->integer()->required(),
            ])->required(),
            'days' => $schema->array(
                $schema->object([
                    'day'       => $schema->string()->required(),
                    'breakfast' => $schema->string()->required(),
                    'lunch'     => $schema->string()->required(),
                    'dinner'    => $schema->string()->required(),
                    'snack'     => $schema->string()->required(),
                ])
            )->required(),
            'warnings' => $schema->array($schema->string())->required(),
        ];
    }
}
```

---

## How the agent is called

The agent is never called directly from a controller. It is called from inside `GenerateDietPlanJob`:

```php
// Inside GenerateDietPlanJob::handle()
$patient = $this->plan->patient()->with('socioeconomic')->firstOrFail();

// The patient is passed with socioeconomic relation loaded
$response = (new DietPlanAgent($patient))->prompt('Generate the plan.');

// response is now array-accessible
$data = $response->toArray();
// $data['rationale'] → string
// $data['daily_calories'] → integer
// $data['days'] → array of 7 objects
```

The job handles:
- Loading patient data
- Calling the agent
- Validating the response
- Retrying if validation fails
- Saving the result

The agent handles only one thing: generating a plan given a patient. This separation of concerns is deliberate.

---

## What can go wrong and why

Even with a well-designed schema and prompt, the model can misbehave:

**Wrong day count:**
The model returns 6 days instead of 7. Your validator catches this with `'days' => ['required', 'array', 'size:7']`.

**Out-of-range calories:**
The model returns `daily_calories: 500` (starvation) or `15000` (absurd). Your validator catches this with `'daily_calories' => ['required', 'integer', 'between:1000,4000']`.

**Generic meal descriptions:**
The model returns `"breakfast": "A healthy breakfast"`. This passes schema validation (it's a string) but isn't useful. This is a prompt engineering concern — the rule "Each meal field must be a specific food" reduces but doesn't eliminate this.

**Allergen slippage:**
The model ignores the allergy rule and includes shellfish in a meal for a shellfish-allergic patient. Schema validation cannot catch this — it doesn't know what shellfish is. This is a known limitation documented in `plan.md`. The mitigation for a production system would be a post-generation allergen scan. For this POC, it's documented as a known risk.

The key insight: **schema validation catches structural errors; it cannot catch semantic errors.** Both the schema and the prompt do their best to prevent problems, but the validator is the last line of defense for structural issues.

Continue to: [The Queue Job & Validation →](03-the-queue-job.md)
