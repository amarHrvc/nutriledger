# 06 — Multi-Agent Patterns

## Pattern Decision Guide

| Pattern | Use When |
|---|---|
| Prompt Chaining | Fixed sequence: generate → validate → refine → format |
| Routing | Inputs vary in type or complexity — send to right specialist |
| Parallelization | Multiple independent analyses of same input |
| Orchestrator-Workers | Dynamic planning — steps not known upfront |
| Evaluator-Optimizer | Clear quality bar, output benefits from iteration |

---

## 1. Prompt Chaining

One agent's output feeds the next. Use Laravel `Pipeline`.

```php
use Illuminate\Pipeline\Pipeline;

$result = app(Pipeline::class)
    ->send(['transcript' => $rawText])
    ->through([
        DraftEmailStep::class,
        ReviewQualityStep::class,
        FormatOutputStep::class,
    ])
    ->thenReturn();

// Each step class:
class DraftEmailStep
{
    public function handle(array $payload, Closure $next): array
    {
        $payload['draft'] = (string) (new DraftAgent)->prompt($payload['transcript']);
        return $next($payload);
    }
}
```

---

## 2. Routing

Classify input → dispatch to specialist agent.

```php
// Classifier agent (HasStructuredOutput)
class RouterAgent implements Agent, HasStructuredOutput
{
    use Promptable;
    public function instructions(): string { return 'Classify this support ticket.'; }
    public function schema(JsonSchema $schema): array
    {
        return [
            'department' => $schema->string()->enum(['billing', 'technical', 'sales'])->required(),
            'complexity' => $schema->string()->enum(['simple', 'complex'])->required(),
        ];
    }
}

// Dispatch
$classification = (new RouterAgent)->prompt($ticketText);

$agent = match($classification['department']) {
    'billing'   => new BillingAgent,
    'technical' => new TechnicalAgent,
    'sales'     => new SalesAgent,
};

// Use cheapest model for simple queries
$response = $classification['complexity'] === 'simple'
    ? (new SimpleResponder)->prompt($ticketText)
    : $agent->prompt($ticketText);
```

Tip: `#[UseCheapestModel]` attribute auto-selects cheapest available model for classifier agents.

---

## 3. Parallelization

Run independent agents simultaneously with `Concurrency::run()`.

```php
use Illuminate\Support\Facades\Concurrency;

[$security, $performance, $style] = Concurrency::run([
    fn() => (string) (new SecurityReviewAgent)->prompt($code),
    fn() => (string) (new PerformanceReviewAgent)->prompt($code),
    fn() => (string) (new StyleReviewAgent)->prompt($code),
]);

// Synthesize results
$summary = (new SynthesisAgent)->prompt(
    "Security: $security\nPerformance: $performance\nStyle: $style\n\nSynthesize into final review."
);
```

---

## 4. Orchestrator-Workers

Orchestrator decides dynamically which workers to call using `AgentTool`.

```php
use Laravel\Ai\Tools\AgentTool;

// Worker agents (each focused on one thing)
class CreateFileAgent implements Agent { use Promptable; ... }
class ModifyFileAgent implements Agent { use Promptable; ... }
class RunTestsAgent implements Agent { use Promptable; ... }

// Orchestrator
#[MaxSteps(20)]
class FeatureOrchestratorAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return 'Implement the requested feature by coordinating file creation, modification, and testing.';
    }

    public function tools(): iterable
    {
        return [
            AgentTool::for(new CreateFileAgent),
            AgentTool::for(new ModifyFileAgent),
            AgentTool::for(new RunTestsAgent),
        ];
    }
}

// Usage — orchestrator plans and executes autonomously
$result = (new FeatureOrchestratorAgent)->prompt('Add a password reset feature.');
```

⚠️ Set `#[MaxSteps(N)]` high enough — each tool call + LLM reasoning counts as steps.

---

## 5. Evaluator-Optimizer

Generate → score → rewrite loop until quality threshold met.

```php
// Evaluator (HasStructuredOutput)
class ContentEvaluatorAgent implements Agent, HasStructuredOutput
{
    use Promptable;
    public function schema(JsonSchema $schema): array
    {
        return [
            'score'    => $schema->integer()->min(1)->max(10)->required(),
            'approved' => $schema->boolean()->required(),
            'issues'   => $schema->array($schema->string())->required(),
        ];
    }
}

// Writer agent
class ContentWriterAgent implements Agent { use Promptable; }

// Loop
$maxIterations = 3;
$draft = (string) (new ContentWriterAgent)->prompt($topic);

for ($i = 0; $i < $maxIterations; $i++) {
    $eval = (new ContentEvaluatorAgent)->prompt("Evaluate:\n\n$draft");

    if ($eval['approved'] || $eval['score'] >= 8) {
        break;
    }

    $issues = implode(', ', $eval['issues']);
    $draft = (string) (new ContentWriterAgent)->prompt(
        "Rewrite improving these issues: $issues\n\nOriginal:\n$draft"
    );
}

return $draft;
```
