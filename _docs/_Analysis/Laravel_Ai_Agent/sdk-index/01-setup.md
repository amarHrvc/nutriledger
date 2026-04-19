# 01 — Setup & Configuration

## Installation

```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate
```

Creates: `config/ai.php`, migrations for `agent_conversations` + `agent_conversation_messages`.

---

## Artisan Generators

| Command | Output |
|---|---|
| `php artisan make:agent AgentName` | `app/Ai/Agents/AgentName.php` |
| `php artisan make:agent AgentName --structured` | Agent + `HasStructuredOutput` scaffold |
| `php artisan make:tool ToolName` | `app/Ai/Tools/ToolName.php` |
| `php artisan make:agent-middleware MiddlewareName` | `app/Ai/Middleware/MiddlewareName.php` |

---

## Environment Variables

| Provider | ENV key |
|---|---|
| OpenAI | `OPENAI_API_KEY` |
| Anthropic | `ANTHROPIC_API_KEY` |
| Gemini | `GEMINI_API_KEY` |
| xAI | `XAI_API_KEY` |
| Mistral | `MISTRAL_API_KEY` |
| Groq | *(uses OpenAI-compatible base URL)* |
| DeepSeek | `DEEPSEEK_API_KEY` *(via custom url)* |
| Ollama | `OLLAMA_API_KEY` *(optional)* |
| Cohere | `COHERE_API_KEY` |
| ElevenLabs | `ELEVENLABS_API_KEY` |
| Jina | `JINA_API_KEY` |
| VoyageAI | `VOYAGEAI_API_KEY` |

---

## `config/ai.php` Structure

```php
return [
    'providers' => [
        'openai' => [
            'driver' => 'openai',
            'key'    => env('OPENAI_API_KEY'),
            'url'    => env('OPENAI_BASE_URL'),   // optional, custom base URL
        ],
        'anthropic' => [
            'driver' => 'anthropic',
            'key'    => env('ANTHROPIC_API_KEY'),
        ],
        // groq, gemini, xai, deepseek, mistral, ollama, azure...
    ],

    'models' => [
        'text'          => 'gpt-4o',
        'image'         => 'dall-e-3',
        'audio'         => 'tts-1',
        'transcription' => 'whisper-1',
        'embedding'     => 'text-embedding-3-small',
    ],

    'caching' => [
        'embeddings' => [
            'cache' => false,
            'store' => env('CACHE_STORE', 'database'),
        ],
    ],
];
```

**Custom base URL support:** OpenAI, Anthropic, Gemini, Groq, Cohere, DeepSeek, xAI, OpenRouter.

---

## Provider Enum

```php
use Laravel\Ai\Enums\Lab;

Lab::OpenAI
Lab::Anthropic
Lab::Gemini
// Used in: #[Provider(Lab::Anthropic)], failover arrays, generate() calls
```

---

## Key Namespaces

| Type | Namespace |
|---|---|
| Agents | `App\Ai\Agents\` |
| Tools | `App\Ai\Tools\` |
| Middleware | `App\Ai\Middleware\` |
| Contracts | `Laravel\Ai\Contracts\` |
| Attributes | `Laravel\Ai\Attributes\` |
| Files | `Laravel\Ai\Files\` |
