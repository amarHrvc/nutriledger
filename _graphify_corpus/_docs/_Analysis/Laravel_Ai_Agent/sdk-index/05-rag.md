# 05 — RAG, Embeddings & Vector Search

⚠️ **Prerequisites:** PostgreSQL + pgvector extension (`CREATE EXTENSION IF NOT EXISTS vector;`)

---

## Generating Embeddings

```php
use Laravel\Ai\Embeddings;
use Illuminate\Support\Str;

// Single string → array of floats
$vector = Str::of('Patient has recurring headaches.')->toEmbeddings();

// Batch
$response = Embeddings::for([
    'Patient has recurring headaches.',
    'Blood pressure reading: 140/90.',
])->generate();

// With caching (avoid redundant API calls)
$vector = Str::of('text')->toEmbeddings(cache: 3600); // cache 1 hour
$response = Embeddings::for(['text'])->cache()->generate();
```

**Default dimensions:** 1536 (configurable in `config/ai.php` → `models.embedding`)  
**Default provider:** OpenAI `text-embedding-3-small`

---

## Vector Column Migration

```php
// In migration
Schema::table('visits', function (Blueprint $table) {
    $table->vector('embedding', 1536)->nullable();
});
```

⚠️ Requires pgvector on PostgreSQL. Not supported on SQLite/MySQL.

---

## Vector Similarity Query

```php
use App\Models\Visit;

// Find top 5 semantically similar visits to a query
$queryVector = Str::of($userQuery)->toEmbeddings();

$visits = Visit::query()
    ->where('patient_id', $patientId)
    ->whereVectorSimilarTo('embedding', $userQuery, minSimilarity: 0.4)
    ->limit(5)
    ->get();
```

`whereVectorSimilarTo` accepts a string (auto-generates embedding) or a pre-computed vector array.

---

## Embedding on Save (Observer Pattern)

```php
// app/Observers/VisitObserver.php
class VisitObserver
{
    public function saved(Visit $visit): void
    {
        if ($visit->isDirty('notes') && $visit->notes) {
            $visit->updateQuietly([
                'embedding' => Str::of($visit->notes)->toEmbeddings(),
            ]);
        }
    }
}
```

---

## SimilaritySearch Tool (in-agent RAG)

Lets the agent query your DB semantically during a conversation.

```php
use Laravel\Ai\Tools\SimilaritySearch;

// In agent tools()
public function tools(): iterable
{
    return [
        SimilaritySearch::usingModel(Visit::class, 'embedding')
            ->limit(5)
            ->labelColumn('notes'),
    ];
}
```

→ see `04-tools.md` §SimilaritySearch

---

## Reranking

Reorder a set of documents by semantic relevance to a query.

```php
use Laravel\Ai\Reranking;

$response = Reranking::of([
    'Patient took ibuprofen for 3 days.',
    'Patient reports no prior surgeries.',
    'Patient blood pressure elevated at last visit.',
])->rerank('blood pressure history');

$best = $response->first()->document; // most relevant
$score = $response->first()->score;   // 0.0–1.0
```

**Providers:** Cohere, Jina  
**When to use:** Post-retrieval re-scoring when `whereVectorSimilarTo` returns too many low-quality matches.

---

## Files (Upload to Provider)

Upload large documents once; reference by ID across multiple prompts without re-uploading.

```php
use Laravel\Ai\Files\Document;

// Upload
$stored = Document::fromPath('/path/to/report.pdf')->put();
$stored = Document::fromStorage('report.pdf', disk: 'local')->put();
$stored = Document::fromUrl('https://example.com/doc.pdf')->put();

// Reference in agent prompt
$response = (new MyAgent)->prompt(
    'Summarize this document',
    attachments: [Document::fromId($stored->id)]
);

// Delete
Document::fromId($stored->id)->delete();
```

**Providers:** OpenAI, Anthropic, Gemini

---

## Vector Stores (Provider-Side RAG)

Create searchable file collections on the provider (used with `FileSearch` tool).

```php
use Laravel\Ai\Stores;
use Laravel\Ai\Files\Document;

// Create store
$store = Stores::create('Patient Knowledge Base');
$store = Stores::create(
    name: 'Patient Knowledge Base',
    description: 'Clinical notes and reports.',
    expiresWhenIdleFor: days(30),
);

// Add files
$store->add(Document::fromPath('/path/to/notes.pdf'));
$store->add(Document::fromPath('/path/to/doc.pdf'), metadata: [
    'patient_id' => 42,
    'year'       => 2026,
]);

// Remove
$store->remove('file_id');

// Delete store
Stores::delete($store->id);
```

Pair with `FileSearch` tool → see `04-tools.md` §FileSearch
