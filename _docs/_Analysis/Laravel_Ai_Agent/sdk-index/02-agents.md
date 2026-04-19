# 02 — Agents

## Minimal Agent

```php
namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

class SalesCoach implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return 'You are a sales coach analyzing transcripts.';
    }
}

// Usage
$response = (new SalesCoach)->prompt('Analyze this transcript...');
echo (string) $response;
```

---

## PHP Attributes

| Attribute | Purpose | Example |
|---|---|---|
| `#[Provider(Lab::Anthropic)]` | Pin to a specific provider | `#[Provider(Lab::Anthropic)]` |
| `#[Model('claude-haiku-4-5-20251001')]` | Pin to a specific model | `#[Model('gpt-4o-mini')]` |
| `#[Temperature(0.0)]` | Deterministic output (0.0–1.0) | `#[Temperature(0.0)]` |
| `#[MaxSteps(10)]` | Max LLM calls per prompt (needed for tools) | `#[MaxSteps(5)]` |

```php
use Laravel\Ai\Attributes\{MaxSteps, Model, Provider, Temperature};
use Laravel\Ai\Enums\Lab;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[MaxSteps(10)]
#[Temperature(0.7)]
class SalesCoach implements Agent { ... }
```

---

## Prompting

```php
// Basic
$response = (new MyAgent)->prompt('Your prompt here');
echo (string) $response;

// Per-call provider override
$response = (new MyAgent)->prompt('prompt', provider: Lab::Anthropic);

// Array of providers = failover → see 08-production.md
$response = (new MyAgent)->prompt('prompt', provider: [Lab::OpenAI, Lab::Anthropic]);
```

---

## Attachments (File / Image Input)

```php
use Laravel\Ai\Files;

// Image input (vision)
$response = (new MyAgent)->prompt(
    'Describe this image',
    attachments: [Files\Image::fromStorage('photo.jpg')]
);

// Document input
$response = (new MyAgent)->prompt(
    'Summarize this PDF',
    attachments: [Files\Document::fromPath('/path/to/doc.pdf')]
);

// Stored file (uploaded to provider)
$response = (new MyAgent)->prompt(
    'Analyze',
    attachments: [Files\Document::fromId($storedFileId)]
);
```

**Vision providers:** OpenAI, Anthropic, Gemini

---

## Conversations (Memory)

Implement `Conversational` + use `RemembersConversations` trait.

```php
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\{Agent, Conversational};
use Laravel\Ai\Promptable;

class SalesCoach implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        return 'You are a sales coach.';
    }
}

// Start conversation (scoped to user)
$response = (new SalesCoach)->forUser($user)->prompt('Hello!');
$conversationId = $response->conversationId;

// Continue conversation
$response = (new SalesCoach)
    ->continue($conversationId, as: $user)
    ->prompt('Tell me more.');
```

⚠️ **Conversations are scoped per user** — `->forUser($user)` prevents cross-user contamination.  
⚠️ **Requires migrations** — `agent_conversations` and `agent_conversation_messages` tables. Run `php artisan migrate`. → see `01-setup.md`

---

## Agent Middleware

```php
// app/Ai/Middleware/LogPrompts.php
class LogPrompts
{
    public function before(AgentPrompt $prompt, Closure $next): mixed
    {
        Log::info('Prompt: ' . $prompt->prompt);
        return $next($prompt);
    }

    public function after(AgentResponse $response, Closure $next): mixed
    {
        Log::info('Response tokens: ' . $response->usage->totalTokens);
        return $next($response);
    }
}

// Apply via attribute
#[Middleware(LogPrompts::class)]
class SalesCoach implements Agent { ... }
```
