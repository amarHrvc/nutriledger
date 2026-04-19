# 09 — Testing & Observability

## Agent Fakes

```php
use App\Ai\Agents\SalesCoach;
use Laravel\Ai\Prompts\AgentPrompt;

// Prevent all real API calls, return empty response
SalesCoach::fake();

// Queue of responses (returned in order)
SalesCoach::fake(['First response', 'Second response']);

// Dynamic — inspect prompt, return conditionally
SalesCoach::fake(function (AgentPrompt $prompt) {
    return 'Response for: ' . $prompt->prompt;
});

// Prevent accidental real calls in test suite
SalesCoach::fake()->preventStrayPrompts();
```

### Structured Output Fake

```php
SalesCoach::fake([json_encode([
    'summary'            => 'Patient presented with headache.',
    'risk_level'         => 'low',
    'follow_up_required' => false,
    'clinical_flags'     => [],
])]);
```

### Assertions

```php
SalesCoach::assertPrompted('Analyze this...');
SalesCoach::assertPrompted(fn (AgentPrompt $p) => str_contains($p->prompt, 'headache'));
SalesCoach::assertNotPrompted('Missing prompt text');
SalesCoach::assertNeverPrompted();  // agent was never called
```

---

## Image / Audio / Transcription Fakes

```php
use Laravel\Ai\{Image, Audio, Transcription};

Image::fake();
Image::fake([base64_encode($imageBytes)]);
Image::fake()->preventStrayImages();
Image::assertGenerated(fn ($p) => $p->contains('sunset') && $p->isLandscape());
Image::assertNotGenerated('Missing prompt');
Image::assertNothingGenerated();

Audio::fake();
Audio::assertGenerated(fn ($p) => $p->contains('Hello') && $p->isFemale());
Audio::assertNothingGenerated();

Transcription::fake();
Transcription::fake(['Transcribed text.']);
Transcription::assertGenerated(fn ($p) => $p->isDiarized());
Transcription::assertNothingGenerated();
```

---

## Embeddings Fake

```php
use Laravel\Ai\Embeddings;

Embeddings::fake();
Embeddings::assertGenerated(fn ($p) => $p->contains('Laravel'));
Embeddings::assertNothingGenerated();
```

---

## Files & Vector Store Fakes

```php
use Laravel\Ai\{Files, Stores};
use Laravel\Ai\Files\Document;

Files::fake();
Document::fromString('Hello, Laravel!', 'text/plain')->put();
Files::assertStored(fn ($file) => (string) $file === 'Hello, Laravel!');
Files::assertDeleted('file-id');
Files::assertNothingStored();

Stores::fake();
$store = Stores::create('Knowledge Base');
$store->add('file_id');
Stores::assertCreated('Knowledge Base');
$store->assertAdded('file_id');
$store->assertRemoved('removed_id');
```

---

## Conversation Testing

```php
it('continues a conversation', function () {
    SalesCoach::fake(['First response', 'Second response']);

    $user = User::factory()->create();

    // Turn 1
    $r1 = (new SalesCoach)->forUser($user)->prompt('Hello');
    $conversationId = $r1->conversationId;

    // Turn 2
    $r2 = (new SalesCoach)->continue($conversationId, as: $user)->prompt('Tell me more');

    expect((string) $r2)->toBe('Second response');

    // Verify conversation was persisted
    $this->assertDatabaseHas('agent_conversations', ['id' => $conversationId]);
});
```

---

## Observability — AiRun / AiUsage Pattern

The SDK exposes `$response->usage` but does not persist it automatically. Build your own tracking via middleware or observer:

```php
// Suggested schema (implement as your own migration)
// ai_runs: id, user_id, patient_id (nullable), feature, provider, model,
//          status (success|failure), started_at, finished_at
// ai_usages: id, ai_run_id, prompt_tokens, completion_tokens, total_tokens, cost_usd

// Example: track via agent middleware
class TrackUsage
{
    public function __construct(private string $feature) {}

    public function after(AgentResponse $response, Closure $next): mixed
    {
        $run = AiRun::create([
            'user_id'     => auth()->id(),
            'feature'     => $this->feature,
            'provider'    => $response->provider,
            'model'       => $response->model,
            'status'      => 'success',
            'finished_at' => now(),
        ]);

        AiUsage::create([
            'ai_run_id'          => $run->id,
            'prompt_tokens'      => $response->usage->promptTokens,
            'completion_tokens'  => $response->usage->completionTokens,
            'total_tokens'       => $response->usage->totalTokens,
        ]);

        return $next($response);
    }
}
```

→ see `08-production.md` §Agent Middleware for middleware setup

---

## Test Setup Conventions

```php
// Standard test with agent fake
it('summarizes visit notes', function () {
    SalesCoach::fake([json_encode(['summary' => 'Test summary', 'risk_level' => 'low'])]);

    $user = User::factory()->create();

    // trigger the feature under test
    // assert DB state
    // assert agent was prompted
    SalesCoach::assertPrompted(fn ($p) => str_contains($p->prompt, 'notes'));
});
```

- Use `RefreshDatabase` — agent fakes don't reset between tests automatically
- Combine with `actingAs($user)` for authenticated agent features
- `preventStrayPrompts()` in `setUp()` to catch accidental real API calls across the suite
