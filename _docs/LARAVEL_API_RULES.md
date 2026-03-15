# Laravel REST API — Design Rules & Patterns

Distilled from the Laravel API Masterclass (Episodes 01–21).
Apply these rules to any Laravel JSON API regardless of domain.

---

## 1. Project Structure

### Folder layout (mirrors URL)

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php          ← auth (unversioned)
│   │       └── V1/
│   │           ├── ApiController.php       ← base for all V1 resource controllers
│   │           ├── ResourceController.php  ← per resource
│   │           └── ParentChildController.php
│   ├── Filters/
│   │   └── V1/
│   │       ├── QueryFilter.php             ← abstract base
│   │       └── ResourceFilter.php          ← per resource
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginUserRequest.php        ← auth request (unversioned)
│   │       └── V1/
│   │           ├── BaseResourceRequest.php ← shared mappedAttributes() + messages()
│   │           ├── StoreResourceRequest.php
│   │           ├── ReplaceResourceRequest.php
│   │           └── UpdateResourceRequest.php
│   └── Resources/
│       └── Api/
│           └── V1/
│               └── ResourceResource.php
├── Permissions/
│   └── V1/
│       └── Abilities.php                   ← ability constants + getAbilities()
├── Policies/
│   └── Api/
│       └── V1/
│           └── ResourcePolicy.php
└── Traits/
    └── ApiResponses.php
routes/
├── api.php         ← auth routes only (login, logout, register)
└── api_v1.php      ← all versioned resource routes
```

---

## 2. Response Format

### ApiResponses trait

All controllers use this trait (via `ApiController`). Never call `response()->json()` directly in controllers.

```php
// app/Traits/ApiResponses.php
trait ApiResponses
{
    protected function ok(string $message, mixed $data = []): JsonResponse
    {
        return $this->success($message, $data, 200);
    }

    protected function created(string $message, mixed $data = []): JsonResponse
    {
        return $this->success($message, $data, 201);
    }

    protected function success(string $message, mixed $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => $statusCode,
            'data'    => $data,
        ], $statusCode);
    }

    protected function error(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => $statusCode,
        ], $statusCode);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
```

**Status code reference:**

| Code | Method | Use when |
|---|---|---|
| 200 | `ok()` | Success with data |
| 201 | `created()` | Resource created |
| 204 | `noContent()` | Success, nothing to return |
| 401 | `error(msg, 401)` | Not authenticated / not authorized |
| 403 | `error(msg, 403)` | Forbidden (use 401 in APIs — see note) |
| 404 | `error(msg, 404)` | Resource not found |
| 422 | validation auto | Validation failed |
| 500 | `error(msg, 500)` | Server failure |

> In API authorization errors, prefer returning 401 over 403 — 403 reveals that the resource exists and the user is authenticated but blocked. 401 reveals less.

---

## 3. URL Design Rules

- **Nouns, not verbs**: `/api/v1/tickets`, never `/api/v1/getTickets`
- **Plural**: `/api/v1/tickets`, never `/api/v1/ticket`
- **Lowercase**: `/api/v1/tickets`, never `/api/v1/Tickets`
- **HTTP method expresses the action**, not the URL segment
- **Never use `create` or `edit` route methods** — use `Route::apiResource`, not `Route::resource`

| Method | URL | Action |
|---|---|---|
| GET | `/api/v1/resources` | List all |
| POST | `/api/v1/resources` | Create |
| GET | `/api/v1/resources/{id}` | Show one |
| PUT | `/api/v1/resources/{id}` | Replace (all fields) |
| PATCH | `/api/v1/resources/{id}` | Partial update |
| DELETE | `/api/v1/resources/{id}` | Delete |

---

## 4. Versioning

### Route file registration (`bootstrap/app.php`)

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(base_path('routes/api_v1.php'));
    },
)
```

### `routes/api_v1.php` structure

```php
Route::middleware('auth:sanctum')->group(function () {

    // Resource — separate PUT/PATCH from apiResource
    Route::apiResource('tickets', TicketController::class)->except('update');
    Route::put('tickets/{ticket}',   [TicketController::class, 'replace'])->name('tickets.replace');
    Route::patch('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

    // Nested resource
    Route::apiResource('authors.tickets', AuthorTicketsController::class)
        ->scoped(['ticket' => 'id'])
        ->except('update');
    Route::put('authors/{author}/tickets/{ticket}',   [AuthorTicketsController::class, 'replace'])->name('authors.tickets.replace');
    Route::patch('authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'update'])->name('authors.tickets.update');

    // Read-only resource (e.g. derived/computed resources)
    Route::apiResource('authors', AuthorsController::class)->except(['store', 'update', 'destroy']);

});
```

- `->except('update')` removes the default PATCH route so you can register PUT and PATCH separately with distinct controller methods.
- `->scoped(['ticket' => 'id'])` enables route model binding on nested resources, scoping the child to the parent.

---

## 5. Authentication (Sanctum)

### `routes/api.php` (unversioned)

```php
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

### Login — issue a token

```php
public function login(LoginUserRequest $request): JsonResponse
{
    $request->validated();

    if (! Auth::attempt($request->only('email', 'password'))) {
        return $this->error('Invalid credentials.', 401);
    }

    $user = $request->user();

    return $this->ok('Authenticated.', [
        'token' => $user->createToken(
            'API token for ' . $user->email,
            Abilities::getAbilities($user),   // assign abilities at token creation time
            Carbon::now()->addMonth()          // expiry
        )->plainTextToken,
    ]);
}
```

**Key rules:**
- `createToken()->plainTextToken` is the **only time** you can retrieve the unhashed token. Store it; it cannot be retrieved again.
- **Never assign `*` as an ability.** `tokenCan('*')` returns `true` for all abilities — including narrow ones like `UpdateOwnTicket` — which breaks granular checks.
- The client sends `Authorization: Bearer <token>` on every subsequent request.

### Logout — revoke the current token only

```php
public function logout(Request $request): JsonResponse
{
    $request->user()->currentAccessToken()->delete();
    return $this->ok('');
}
```

| Method | Effect | When to use |
|---|---|---|
| `currentAccessToken()->delete()` | Revokes this request's token only | Normal logout |
| `tokens()->where('id', $id)->delete()` | Revokes one specific token | Admin revoke |
| `tokens()->delete()` | Revokes ALL tokens for user | "Sign out everywhere" |

---

## 6. Eloquent Resources (JSON:API)

### Standard resource structure

```php
class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'          => 'ticket',
            'id'            => $this->id,
            'attributes'    => [
                'title'       => $this->title,
                // Omit heavy fields on list endpoints:
                'description' => $this->when(
                    ! $request->routeIs(['tickets.index', 'authors.tickets.index']),
                    $this->description
                ),
                'status'    => $this->status,
                'createdAt' => $this->created_at,  // camelCase in JSON, snake_case in DB
                'updatedAt' => $this->updated_at,
            ],
            'relationships' => [
                'author' => [
                    'data'  => ['type' => 'user', 'id' => $this->user_id],
                    'links' => ['self' => route('authors.show', ['author' => $this->user_id])],
                ],
            ],
            'includes'      => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'links'         => ['self' => route('tickets.show', ['ticket' => $this->id])],
        ];
    }
}
```

### Rules

- **Always use camelCase keys** in JSON — map from snake_case model attributes.
- **Use `when($condition, $value)`** to omit a field when the condition is false (field is absent, not null).
- **Use `mergeWhen($condition, $array)`** to conditionally include a group of fields as a unit.
- **Use `whenLoaded($relation, $callback)`** to include relationships only when explicitly eager-loaded — prevents N+1 queries.
- **`route('resource.show', $model)`** — use named routes from `apiResource` for self links.
- **Wrap collections with `::collection()`**, single models with `new Resource($model)`.
- **Use `paginate()`** (not `all()`) on index — adds `links` and `meta` automatically.

### Controller usage

```php
// Collection
public function index(): AnonymousResourceCollection
{
    return TicketResource::collection(Ticket::paginate());
}

// Single
public function show(int $id): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($id);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }
    return new TicketResource($ticket);
}
```

---

## 7. Optional Includes (`?include=relation`)

### ApiController base class

```php
class ApiController extends Controller
{
    use ApiResponses;

    protected string $policyClass;

    public function include(string $relationship): bool
    {
        $param = request()->get('include');
        if (! isset($param)) {
            return false;
        }
        return in_array(strtolower($relationship), explode(',', strtolower($param)));
    }

    public function isAble(string $ability, mixed $targetModel): void
    {
        $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}
```

### Controller usage

```php
// show()
if ($this->include('author')) {
    $ticket->load('user');           // load() — already-fetched model
}

// index()
$query = Ticket::query();
if ($this->include('author')) {
    $query = $query->with('user');   // with() — pre-fetch in query
}
return TicketResource::collection($query->paginate());
```

The resource's `whenLoaded()` handles the rest — no additional changes needed.

---

## 8. Filtering

### Architecture

```
?filter[status]=C&filter[title]=*word*&filter[createdAt]=2024-01-01,2024-02-01
```

Filters are namespaced under `filter[...]` to separate them from `include`, `sort`, and other params.

### QueryFilter (abstract base)

```php
abstract class QueryFilter
{
    protected Builder $builder;

    public function __construct(protected Request $request) {}

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $this->builder;
    }

    protected function filter(array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $this->builder;
    }

    // Sorting lives here — inherited by all subclasses
    protected array $sortable = [];

    protected function sort(string $value): Builder
    {
        foreach (explode(',', $value) as $sortAttribute) {
            $direction = 'asc';
            if (str_starts_with($sortAttribute, '-')) {
                $direction     = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }
            if (! $this->isSortable($sortAttribute)) {
                continue;
            }
            $this->builder->orderBy($this->resolveColumnName($sortAttribute), $direction);
        }
        return $this->builder;
    }

    private function isSortable(string $attribute): bool
    {
        return in_array($attribute, $this->sortable) || array_key_exists($attribute, $this->sortable);
    }

    private function resolveColumnName(string $attribute): string
    {
        return array_key_exists($attribute, $this->sortable)
            ? $this->sortable[$attribute]
            : $attribute;
    }
}
```

### ResourceFilter subclass

```php
class TicketFilter extends QueryFilter
{
    protected array $sortable = [
        'title',
        'status',
        'createdAt' => 'created_at',   // camelCase → snake_case mapping
        'updatedAt' => 'updated_at',
    ];

    public function status(string $value): Builder
    {
        return $this->builder->whereIn('status', explode(',', strtoupper($value)));
    }

    public function title(string $value): Builder
    {
        return $this->builder->where('title', 'like', str_replace('*', '%', $value));
    }

    public function createdAt(string $value): Builder
    {
        $dates = explode(',', $value);
        return count($dates) > 1
            ? $this->builder->whereBetween('created_at', $dates)
            : $this->builder->whereDate('created_at', $dates[0]);
    }
}
```

### Model scope (required on every filterable model)

```php
public function scopeFilter(Builder $query, QueryFilter $filters): Builder
{
    return $filters->apply($query);
}
```

### Controller usage

```php
public function index(TicketFilter $filters): AnonymousResourceCollection
{
    return TicketResource::collection(
        Ticket::filter($filters)->paginate()
    );
}
```

### Sorting convention

| URL | Effect |
|---|---|
| `?sort=title` | Ascending |
| `?sort=-title` | Descending |
| `?sort=status,-title` | Multi-column |

- Unknown sort columns are **silently ignored** — no error, query runs without that sort.
- Sortable columns must be whitelisted in `$sortable`. Use key-value pairs to alias camelCase API names to snake_case DB columns.

---

## 9. Nested Resources

```
GET /api/v1/authors/{author}/tickets
POST /api/v1/authors/{author}/tickets
GET /api/v1/authors/{author}/tickets/{ticket}
```

### Controller pattern

```php
class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    public function index(User $author, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author->id)
                ->filter($filters)
                ->paginate()
        );
    }
}
```

### Ownership check — use `firstOrFail` with `where`

```php
// Instead of two queries:
// $ticket = Ticket::findOrFail($ticketId);
// if ($ticket->user_id !== $authorId) { ... }

// One query that enforces both constraints:
$ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
```

---

## 10. CRUD — Write Operations

### PUT vs PATCH

| | PUT | PATCH |
|---|---|---|
| Name | `replace()` | `update()` |
| All fields | Required | Optional (`sometimes`) |
| Validation | `required` | `sometimes` |
| Auth | `ReplaceResource` ability | `UpdateResource` or `UpdateOwnResource` |

### BaseResourceRequest — `mappedAttributes()`

Shared base class for all three write request classes. Maps JSON:API input keys to model column names. Only includes keys that were actually sent.

```php
class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.title'             => 'title',
            'data.attributes.description'       => 'description',
            'data.attributes.status'            => 'status',
            'data.relationships.author.data.id' => 'user_id',
            'data.attributes.createdAt'         => 'created_at',
            'data.attributes.updatedAt'         => 'updated_at',
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $inputKey => $attribute) {
            if ($this->has($inputKey)) {
                $attributesToUpdate[$attribute] = $this->input($inputKey);
            }
        }
        return $attributesToUpdate;
    }

    public function messages(): array
    {
        return [
            'data.attributes.status' => 'Invalid status. Use A, C, H, or X.',
        ];
    }
}
```

- **`$this->has()`** returns `true` even for null values — use over `isset()`.
- **`$otherAttributes`** allows callers to inject extra mappings (e.g. route parameters).

### StoreRequest

```php
public function rules(): array
{
    $rules = [
        'data.attributes.title'                     => ['required', 'string'],
        'data.attributes.status'                    => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        'data.relationships.author.data.id'         => ['required', 'integer', 'exists:users,id'],
    ];

    if ($this->routeIs('tickets.store')) {
        if ($this->user()->tokenCan(Abilities::CreateOwnTicket)) {
            // Force author to be the authenticated user
            $rules['data.relationships.author.data.id'][] = 'size:' . $this->user()->id;
        }
    }

    return $rules;
}

// Inject route author ID for nested route
protected function prepareForValidation(): void
{
    if ($this->routeIs('authors.tickets.store')) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => ['author' => ['data' => ['id' => $this->route('author')]]],
            ]),
        ]);
    }
}
```

### UpdateRequest

```php
public function rules(): array
{
    $rules = [
        'data.attributes.title'             => ['sometimes', 'string'],
        'data.attributes.status'            => ['sometimes', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        'data.relationships.author.data.id' => ['sometimes', 'integer'],
    ];

    // Prevent regular users from reassigning ownership
    if ($this->user()->tokenCan(Abilities::UpdateOwnTicket)) {
        $rules['data.relationships.author.data.id'] = ['prohibited'];
    }

    return $rules;
}
```

**`prohibited` rule:** Fails validation if the field is present at all. Returns a validation error — does not silently ignore the field. Use when a user submitting a field they are not allowed to set should be an explicit error.

### Controller write methods (consistent pattern)

```php
public function update(UpdateTicketRequest $request, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
        $this->isAble('update', $ticket);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to update that resource.', 401);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}

public function destroy(int $ticketId): JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
        $this->isAble('destroy', $ticket);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to delete that resource.', 401);
    }

    $ticket->delete();
    return $this->ok('Ticket successfully deleted.');
}
```

**Why `int $id` over route model binding?**
Model binding throws Laravel's default `ModelNotFoundException`, which bypasses your structured error format. Manual `findOrFail` in a try/catch gives you full control over the error response.

---

## 11. Policies & Authorization

### Versioned policy location

```
app/Policies/Api/V1/TicketPolicy.php
app/Policies/Api/V1/UserPolicy.php
```

### ApiController `isAble()` — version-aware authorization

```php
// ApiController
protected string $policyClass;

public function isAble(string $ability, mixed $targetModel): void
{
    $this->authorize($ability, [$targetModel, $this->policyClass]);
}

// ResourceController
protected string $policyClass = TicketPolicy::class;
```

Passing the explicit policy class bypasses Laravel's model-to-policy auto-discovery, allowing V1 and V2 policies to coexist for the same model.

For creation (no existing model): `$this->isAble('store', Ticket::class)` — pass the class string, not an instance.

### Policy structure

```php
class TicketPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateTicket)
            || $user->tokenCan(Abilities::CreateOwnTicket);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;                                   // manager — any ticket
        }
        if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;         // regular user — own only
        }
        return false;
    }

    public function replace(User $user, Ticket $ticket): bool
    {
        return $user->tokenCan(Abilities::ReplaceTicket);  // manager only
    }

    public function destroy(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) { return true; }
        if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }
        return false;
    }
}
```

---

## 12. Token Abilities

### Abilities class

```php
// app/Permissions/V1/Abilities.php
final class Abilities
{
    // Manager abilities — any resource
    public const string CreateTicket  = 'ticket:create';
    public const string UpdateTicket  = 'ticket:update';
    public const string ReplaceTicket = 'ticket:replace';
    public const string DeleteTicket  = 'ticket:delete';

    // Regular user abilities — own resources only
    public const string CreateOwnTicket = 'ticket:own:create';
    public const string UpdateOwnTicket = 'ticket:own:update';
    public const string DeleteOwnTicket = 'ticket:own:delete';

    // User management — manager only
    public const string CreateUser  = 'user:create';
    public const string UpdateUser  = 'user:update';
    public const string ReplaceUser = 'user:replace';
    public const string DeleteUser  = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->is_manager) {
            return [
                self::CreateTicket, self::UpdateTicket, self::ReplaceTicket, self::DeleteTicket,
                self::CreateUser,   self::UpdateUser,   self::ReplaceUser,   self::DeleteUser,
            ];
        }

        // Regular user — own tickets only
        // Do NOT assign '*' — tokenCan('ticket:own:update') would return true for '*',
        // which defeats the ownership checks that are meant to restrict managers.
        return [
            self::CreateOwnTicket,
            self::UpdateOwnTicket,
            self::DeleteOwnTicket,
        ];
    }
}
```

**Rules:**
- Use constants everywhere — never hardcode ability strings. Typos fail silently.
- `final` class — no reason to subclass a constants class.
- **Never assign `*`** — it makes every `tokenCan()` check return `true`, including narrow "own" abilities, which breaks policy logic.
- Regular users get `XxxOwn` abilities; managers get the unrestricted `Xxx` abilities.

---

## 13. Request Validation Conventions

### `authorize()` always returns `true`

Authentication is handled by `auth:sanctum` middleware. Authorization is handled by policies via `isAble()`. `authorize()` in Form Requests has no role — return `true`.

### Nested input keys

The request body mirrors the JSON:API response structure:

```json
{
    "data": {
        "attributes": {
            "title": "...",
            "status": "A"
        },
        "relationships": {
            "author": {
                "data": { "id": 1 }
            }
        }
    }
}
```

Laravel validation keys use dot notation: `data.attributes.title`, `data.relationships.author.data.id`.

### Validation rule summary

| Rule | Use when |
|---|---|
| `required` | Field must be present — POST/PUT |
| `sometimes` | Validate only if present — PATCH |
| `prohibited` | Reject if present (field not allowed for this user) |
| `Rule::in([...])` | Restrict to allowed values |
| `exists:table,column` | Foreign key must exist in DB (replaces manual `findOrFail`) |
| `size:$userId` | Integer must equal specific value (enforce own-resource creation) |

### `prepareForValidation()` — inject route params before rules run

Used when a URL parameter needs to be validated as if it were a body field:

```php
protected function prepareForValidation(): void
{
    if ($this->routeIs('authors.tickets.store')) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => ['author' => ['data' => ['id' => $this->route('author')]]],
            ]),
        ]);
    }
}
```

---

## 14. Password Handling (User Resources)

Hash passwords inside `mappedAttributes()` — never in controllers. Ensures no write path can accidentally store a plaintext password.

```php
// BaseUserRequest::mappedAttributes()
foreach ($attributeMap as $inputKey => $attribute) {
    if ($this->has($inputKey)) {
        $value = $this->input($inputKey);
        if ($attribute === 'password') {
            $value = bcrypt($value);
        }
        $attributesToUpdate[$attribute] = $value;
    }
}
```

**Never include password in a Resource's `toArray()`** — omit it entirely. Even a hash should never be returned to the client.

---

## 15. Authors vs Users Distinction

When a domain has users that play different roles, use semantic naming in URLs:

- `GET /api/v1/authors` — users who have created resources (read-only, joined from the resource table)
- `GET /api/v1/users` — user management (full CRUD, manager-only)

### Authors endpoint (scoped join)

```php
public function index(): AnonymousResourceCollection
{
    return UserResource::collection(
        User::select('users.*')
            ->join('tickets', 'users.id', '=', 'tickets.user_id')
            ->filter(new AuthorFilter(request()))
            ->distinct()  // one row per user regardless of ticket count
            ->paginate()
    );
}
```

**`select('users.*')`** — prevents column name collisions when both tables have `id`, `created_at`, etc.

---

## 16. Test Patterns (Pest)

### Setup

```php
// actingAs() bypasses token issuance — correct for most tests
$this->actingAs($user)->getJson('/api/v1/tickets')->assertOk();

// withToken() tests the actual token + ability system
$token = $user->createToken('test', [Abilities::UpdateOwnTicket])->plainTextToken;
$this->withToken($token)->patchJson("/api/v1/tickets/{$id}", [...]);
```

### Assertion helpers

```php
->assertOk()            // 200
->assertCreated()       // 201
->assertNoContent()     // 204
->assertUnauthorized()  // 401
->assertForbidden()     // 403
->assertNotFound()      // 404
->assertUnprocessable() // 422

// Structure
->assertJsonStructure(['data' => ['*' => ['type', 'id', 'attributes']]])
->assertJsonPath('data.attributes.title', 'Expected value')
->assertJsonCount(3, 'data')
->assertJsonMissingPath('data.includes')
->assertJsonValidationErrors(['data.attributes.status'])

// Database
$this->assertDatabaseHas('tickets', ['title' => 'Test']);
$this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
$this->assertDatabaseCount('personal_access_tokens', 1);
```

### Dataset for validation rules

```php
it('validates required fields', function (array $body) {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tickets', $body)
        ->assertUnprocessable();
})->with([
    'missing title'       => [['data' => ['attributes' => ['status' => 'A']]]],
    'invalid status'      => [['data' => ['attributes' => ['title' => 'T', 'status' => 'Z']]]],
    'missing description' => [['data' => ['attributes' => ['title' => 'T', 'status' => 'A']]]],
]);
```

### Factory helpers

```php
Ticket::factory()->for($user)->create();          // set BelongsTo
Ticket::factory()->count(3)->for($user)->create();
User::factory()->hasTickets(2)->create();         // inverse HasMany
User::factory()->count(10)->create();
Ticket::factory()->count(100)->recycle($users)->create();  // share existing models
```

---

## 17. Common Artisan Commands

```bash
# Controllers
php artisan make:controller Api/V1/TicketController --resource --requests --no-interaction

# Requests
php artisan make:request Api/V1/StoreTicketRequest --no-interaction

# Resources
php artisan make:resource Api/V1/TicketResource --no-interaction

# Policies
php artisan make:policy Api/V1/TicketPolicy --no-interaction

# Models (with migration + factory)
php artisan make:model Ticket -mf --no-interaction

# Filters (plain class)
php artisan make:class Http/Filters/V1/TicketFilter --no-interaction

# Inspect routes
php artisan route:list --path=api
```

---

## 18. Security Notes

- **Do not expose `ModelNotFoundException` stack traces.** Catch them and return a structured `error()` response.
- **Return 404 (not 403) for owned-resource checks** on nested routes — 403 reveals the resource exists but is inaccessible, 404 reveals nothing.
- **Use `exists:table,column` in validation** instead of a manual `try { findOrFail() }` in the controller — keeps validation concerns in the request class.
- **Do not return passwords** (even hashed) in resources.
- **Token expiry** should always be set — either globally in `config/sanctum.php` (`expiration` in minutes) or per-token via `Carbon::now()->addMonth()` at creation.
- **Namespace filter parameters** (`filter[status]`, not `?status`) — separates filter intent from include/sort params and prevents controller method name collisions in `QueryFilter::apply()`.
