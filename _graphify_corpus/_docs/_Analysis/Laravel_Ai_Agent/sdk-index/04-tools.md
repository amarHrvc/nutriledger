# 04 — Tools

## Tool Class Structure

```php
namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class GetPatientHistory implements Tool
{
    public function __construct(private int $patientId) {}

    public function description(): string
    {
        return 'Retrieve the last N visits for the current patient.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()->min(1)->max(20)->required(),
        ];
    }

    public function handle(Request $request): string
    {
        // ⚠️ Validate args — they come from LLM
        $limit = (int) $request['limit'];
        $limit = min(max($limit, 1), 20); // clamp even after schema

        $visits = Visit::where('patient_id', $this->patientId)
            ->latest('date')
            ->limit($limit)
            ->get(['date', 'notes']);

        return $visits->toJson();
    }
}
```

Generator: `php artisan make:tool ToolName`

---

## Registering Tools on an Agent

```php
class PatientAssistant implements Agent
{
    use Promptable;

    public function tools(): iterable
    {
        return [
            new GetPatientHistory($this->patientId),
            new SimilaritySearch::usingModel(Visit::class, 'embedding'),
        ];
    }
}
```

⚠️ **Tools require `#[MaxSteps(N)]` where N ≥ 2** — default may be 1, blocking tool use.

---

## Built-in Tools

### SimilaritySearch

Semantic search over any Eloquent model with an embedding column.

```php
use Laravel\Ai\Tools\SimilaritySearch;

SimilaritySearch::usingModel(Document::class, 'embedding')
// Optional config:
SimilaritySearch::usingModel(Document::class, 'embedding')
    ->limit(5)
    ->labelColumn('title'); // what the agent sees as result label
```

Prerequisite: model has `embedding VECTOR(1536)` column. → see `05-rag.md`

### WebSearch (Anthropic, OpenAI, Gemini)

```php
use Laravel\Ai\Providers\Tools\WebSearch;

new WebSearch                        // basic
(new WebSearch)->max(5)              // limit results
(new WebSearch)->allow(['laravel.com', 'php.net'])  // restrict domains
```

### WebFetch (Anthropic, Gemini)

```php
use Laravel\Ai\Providers\Tools\WebFetch;

(new WebFetch)->max(3)->allow(['docs.laravel.com'])
```

### FileSearch (OpenAI, Gemini)

```php
use Laravel\Ai\Providers\Tools\FileSearch;

new FileSearch(stores: ['store_id_1', 'store_id_2'])
```

Prerequisite: files uploaded to provider vector store. → see `05-rag.md` §Vector Stores

---

## Agents as Tools (Orchestrator-Workers)

Register a sub-agent as a tool inside an orchestrator agent:

```php
use Laravel\Ai\Tools\AgentTool;

class OrchestratorAgent implements Agent
{
    use Promptable;

    public function tools(): iterable
    {
        return [
            AgentTool::for(new FileWriterAgent),
            AgentTool::for(new CodeReviewAgent),
        ];
    }
}
```

→ see `06-multi-agent.md` §Orchestrator-Workers for full pattern

---

## Security Rules

- ⚠️ Always validate/clamp `$request` args in `handle()` — treat as untrusted LLM output
- ⚠️ Scope DB queries to authenticated user / injected context (never trust `$request['user_id']`)
- ⚠️ Never expose raw SQL, file paths, or sensitive config values in tool `description()`
- ⚠️ Return strings from `handle()` — the agent reads this output, keep it concise
