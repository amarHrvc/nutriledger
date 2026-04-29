# 08 — Production Patterns

## Failover

Automatically switch to backup provider on failure/rate-limit.

```php
use Laravel\Ai\Enums\Lab;

// Agent prompt
$response = (new MyAgent)->prompt(
    'Analyze this...',
    provider: [Lab::OpenAI, Lab::Anthropic]  // tries OpenAI first
);

// Image generation
$image = Image::of('A sunset')
    ->generate(provider: [Lab::Gemini, Lab::xAI]);
```

⚠️ Failover only catches pre-response errors. If streaming starts then fails mid-stream, failover does NOT kick in.

---

## Queuing

Dispatch agent to a queue job. Use for long-running or async operations.

```php
// Basic queue dispatch
(new MyAgent)
    ->queue('Analyze this transcript...')
    ->then(function (AgentResponse $response) {
        // handle success — runs in queue worker
        Visit::find($this->visitId)->update(['ai_summary' => (string) $response]);
    })
    ->catch(function (Throwable $e) {
        Log::error('AI agent failed: ' . $e->getMessage());
    });

// Specify queue
(new MyAgent)
    ->onQueue('ai-processing')
    ->queue('Analyze...');
```

Same API available for images, audio, transcriptions:
```php
Image::of('...')->queue();
Audio::of('...')->queue();
Transcription::fromStorage('audio.mp3')->queue();
```

---

## Streaming (SSE)

Returns a `StreamedResponse` — must be returned directly from a route/controller.

```php
// Route
Route::get('/chat', function (Request $request) {
    return (new ChatAgent)
        ->forUser($request->user())
        ->stream($request->input('message'));
});

// With completion callback
return (new ChatAgent)
    ->stream('Analyze...')
    ->then(function (StreamedAgentResponse $response) {
        // Runs after stream completes
        $this->logUsage($response->usage);
    });
```

⚠️ Do NOT assign `->stream()` to a variable and return it later — it starts streaming immediately.  
⚠️ Cannot be used inside a queued job (no HTTP response context).

**Frontend consumption:** Standard `EventSource` / `fetch` with `ReadableStream`. Compatible with Vercel AI SDK Data Protocol.

---

## Broadcasting (WebSocket)

Broadcast streamed response to a private channel via Laravel Reverb + Echo.

```php
(new MyAgent)
    ->broadcastOnQueue(
        channel: 'user.' . $user->id,
        event: 'ai.response'
    )
    ->queue('Generate a summary...');
```

Requires: Laravel Reverb (WebSocket server) + Laravel Echo (frontend).

---

## Agent Middleware

Intercept prompts and responses for logging, rate limiting, auth checks, etc.

```php
// app/Ai/Middleware/CheckSubscription.php
class CheckSubscription
{
    public function before(AgentPrompt $prompt, Closure $next): mixed
    {
        if (! auth()->user()->hasActiveSubscription()) {
            throw new \RuntimeException('Subscription required.');
        }
        return $next($prompt);
    }

    public function after(AgentResponse $response, Closure $next): mixed
    {
        // log token usage after every response
        AiUsageLog::record($response->usage);
        return $next($response);
    }
}

// Apply via attribute
#[Middleware(CheckSubscription::class)]
class MyAgent implements Agent { ... }
```

Generator: `php artisan make:agent-middleware MiddlewareName`  
→ see `02-agents.md` §Agent Middleware

---

## Usage / Token Tracking

Access usage data from any response:

```php
$response = (new MyAgent)->prompt('...');

$response->usage->promptTokens;      // input tokens
$response->usage->completionTokens;  // output tokens
$response->usage->totalTokens;       // total
// cost_usd not directly on response — track via middleware or observer
```

For full observability with DB records → see `09-testing.md` §Observability
