# 03 — Structured Output

## Setup

Implement `HasStructuredOutput` and define `schema(JsonSchema $schema): array`.

```php
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\{Agent, HasStructuredOutput};
use Laravel\Ai\Promptable;

class VisitSummaryAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return 'Summarize clinical visit notes into structured data.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'summary'            => $schema->string()->required(),
            'clinical_flags'     => $schema->array($schema->string())->required(),
            'follow_up_required' => $schema->boolean()->required(),
            'risk_level'         => $schema->string()->enum(['low', 'medium', 'high'])->required(),
        ];
    }
}
```

Shortcut: `php artisan make:agent AgentName --structured`

---

## JsonSchema Helpers

| Builder | Usage |
|---|---|
| `$schema->string()` | String field |
| `$schema->integer()` | Integer field |
| `$schema->boolean()` | Boolean field |
| `$schema->number()` | Float/decimal field |
| `$schema->array($schema->string())` | Array of strings |
| `$schema->object([...])` | Nested object |
| `->required()` | Mark field required |
| `->enum(['a','b','c'])` | Restrict to enum values |
| `->min(N)` / `->max(N)` | Range on integers |
| `->minLength(N)` / `->maxLength(N)` | Length on strings |

---

## Accessing the Response

Response is array-accessible:

```php
$response = (new VisitSummaryAgent)->prompt($notes);

$summary   = $response['summary'];
$flags     = $response['clinical_flags'];   // array
$followUp  = $response['follow_up_required']; // bool
$risk      = $response['risk_level'];        // 'low'|'medium'|'high'
```

---

## ⚠️ CRITICAL — Always Validate Before Persisting

**The SDK does NOT validate structured output against the schema.**  
The model can return wrong types, out-of-range values, or missing fields. Silent data corruption if you skip validation.

```php
use Illuminate\Support\Facades\Validator;

$response = (new VisitSummaryAgent)->prompt($notes);
$data = $response->toArray();

$validator = Validator::make($data, [
    'summary'            => ['required', 'string', 'max:500'],
    'clinical_flags'     => ['required', 'array'],
    'clinical_flags.*'   => ['string'],
    'follow_up_required' => ['required', 'boolean'],
    'risk_level'         => ['required', 'string', 'in:low,medium,high'],
]);

if ($validator->fails()) {
    // log, throw, or handle — do NOT persist
    throw new \RuntimeException('AI response failed validation: ' . $validator->errors());
}

$visit->update($validator->validated());
```

---

## Generator → DB Storage Pattern

```php
// In a queued job
public function handle(): void
{
    $response = (new VisitSummaryAgent)->prompt($this->visit->notes);

    $validated = Validator::make($response->toArray(), [
        'summary'    => ['required', 'string'],
        'risk_level' => ['required', 'in:low,medium,high'],
    ])->validate();  // throws ValidationException on failure

    $this->visit->update([
        'ai_summary'    => $validated['summary'],
        'ai_risk_level' => $validated['risk_level'],
    ]);
}
```
