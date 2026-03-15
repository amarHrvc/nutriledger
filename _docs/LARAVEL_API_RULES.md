# Laravel REST API — Design Rules & Patterns

Apply these rules to any Laravel JSON API. Examples use `Post` as the canonical resource name.

---

## 1A. Project Structure — Unversioned (Level 0 — default)

Flat layout under `app/Http/Controllers/Api/` with no `V1/` subfolders. Start here.

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ApiController.php       ← base for all resource controllers
│   │       ├── PostController.php
│   │       └── AuthController.php
│   ├── Filters/
│   │   ├── QueryFilter.php             ← abstract base
│   │   └── PostFilter.php
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php
│   │       ├── BasePostRequest.php
│   │       ├── StorePostRequest.php
│   │       ├── ReplacePostRequest.php
│   │       └── UpdatePostRequest.php
│   └── Resources/
│       └── Api/
│           └── PostResource.php
├── Permissions/
│   └── Abilities.php                   ← ability constants + getAbilities()
├── Policies/
│   └── PostPolicy.php
└── Traits/
    └── ApiResponses.php
routes/
└── api.php                             ← auth + all resource routes
```

---

## 1B. Project Structure — Versioned (Level 1)

Same tree with `V1/` added to the HTTP layer only. **Never version Models, Services, or Repositories.** The version boundary is at the HTTP layer.

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php          ← auth (unversioned)
│   │       └── V1/
│   │           ├── ApiController.php
│   │           ├── PostController.php
│   │           └── OwnerPostsController.php
│   ├── Filters/
│   │   └── V1/
│   │       ├── QueryFilter.php
│   │       └── PostFilter.php
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php
│   │       └── V1/
│   │           ├── BasePostRequest.php
│   │           ├── StorePostRequest.php
│   │           ├── ReplacePostRequest.php
│   │           └── UpdatePostRequest.php
│   └── Resources/
│       └── Api/
│           └── V1/
│               └── PostResource.php
├── Permissions/
│   └── V1/
│       └── Abilities.php
├── Policies/
│   └── Api/
│       └── V1/
│           └── PostPolicy.php
└── Traits/
    └── ApiResponses.php

← never versioned:
app/Models/Post.php
app/Services/PostService.php
app/Repositories/PostRepository.php
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
| 400 | `error(msg, 400)` | Invalid query parameters (bad filter/sort) |
| 401 | `error(msg, 401)` | Unauthenticated |
| 403 | `error(msg, 403)` | Forbidden (prefer 401 in APIs — see note) |
| 404 | `error(msg, 404)` | Resource not found |
| 422 | validation auto | Validation failed |
| 500 | `error(msg, 500)` | Server failure |

> **401 vs 403:** 401 = unauthenticated (no valid session/token). 403 = forbidden (authenticated but blocked). In APIs, prefer returning 401 for authorization failures — 403 reveals that the resource exists and the caller is known to the system, which can be information you do not want to leak.

---

## 3. URL Design Rules

- **Nouns, not verbs**: `/api/posts`, never `/api/getPosts`
- **Plural**: `/api/posts`, never `/api/post`
- **Lowercase**: `/api/posts`, never `/api/Posts`
- **HTTP method expresses the action**, not the URL segment
- **Never use `create` or `edit` route methods** — use `Route::apiResource`, not `Route::resource`

| Method | Unversioned | Versioned | Action |
|---|---|---|---|
| GET | `/api/posts` | `/api/v1/posts` | List all |
| POST | `/api/posts` | `/api/v1/posts` | Create |
| GET | `/api/posts/{id}` | `/api/v1/posts/{id}` | Show one |
| PUT | `/api/posts/{id}` | `/api/v1/posts/{id}` | Replace (all fields) |
| PATCH | `/api/posts/{id}` | `/api/v1/posts/{id}` | Partial update |
| DELETE | `/api/posts/{id}` | `/api/v1/posts/{id}` | Delete |

---

## 4. Versioning

### Level 0 — No versioning (start here)

Default `bootstrap/app.php` with no `then:` callback. All routes in `routes/api.php`:

```php
// bootstrap/app.php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

```php
// routes/api.php
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('posts', PostController::class)->except('update');
    Route::put('posts/{post}',   [PostController::class, 'replace'])->name('posts.replace');
    Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');
});
```

**Rule:** Start here. Add versioning only when external consumers exist and you need to maintain backward compatibility.

---

### Level 1 — URL versioning

Use a `then:` callback in `bootstrap/app.php` to load a separate versioned route file:

```php
// bootstrap/app.php
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

```php
// routes/api_v1.php
Route::middleware('auth:sanctum')->group(function () {

    // Resource — separate PUT/PATCH from apiResource
    Route::apiResource('posts', PostController::class)->except('update');
    Route::put('posts/{post}',   [PostController::class, 'replace'])->name('posts.replace');
    Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');

    // Nested resource
    Route::apiResource('owners.posts', OwnerPostsController::class)
        ->scoped(['post' => 'id'])
        ->except('update');
    Route::put('owners/{owner}/posts/{post}',   [OwnerPostsController::class, 'replace'])->name('owners.posts.replace');
    Route::patch('owners/{owner}/posts/{post}', [OwnerPostsController::class, 'update'])->name('owners.posts.update');

    // Read-only resource
    Route::apiResource('owners', OwnersController::class)->except(['store', 'update', 'destroy']);

});
```

- `->except('update')` removes the default PATCH route so you can register PUT and PATCH separately with distinct controller methods.
- `->scoped(['post' => 'id'])` enables route model binding on nested resources, scoping the child to the parent.

---

### Level 2 — Deprecation

When sunsetting a version, add headers via middleware or a route group:

```php
response()
    ->header('Sunset',      'Sat, 01 Jan 2026 00:00:00 GMT')
    ->header('Deprecation', 'true')
    ->header('Link',        '<https://api.example.com/v2/posts>; rel="successor-version"')
```

**Breaking vs non-breaking changes:**

| Change | Breaking? | Action |
|---|---|---|
| New optional response field | No | Add in place |
| New required request field | Yes | New version |
| Rename/remove response field | Yes | New version |
| New optional query param | No | Add in place |
| Stricter validation | Yes | New version |

---

### Cross-version sharing

Business logic lives in a service class with no version namespace. Both V1 and V2 controllers inject the same service:

```php
// app/Services/PostService.php  ← no version namespace
class PostService
{
    public function list(array $filters): LengthAwarePaginator { ... }
    public function create(array $attributes): Post { ... }
    public function update(Post $post, array $attributes): Post { ... }
}
```

Controllers in different versions call the same service — only request/resource/policy classes differ per version.

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
public function login(LoginRequest $request): JsonResponse
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
- **Never assign `*` as an ability.** `tokenCan('*')` returns `true` for all abilities — including narrow ones like `UpdateOwnPost` — which breaks granular checks.
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
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'          => 'post',
            'id'            => $this->id,
            'attributes'    => [
                'title'       => $this->title,
                // Omit heavy fields on list endpoints:
                'body'        => $this->when(
                    ! $request->routeIs(['posts.index', 'owners.posts.index']),
                    $this->body
                ),
                'status'    => $this->status,
                'createdAt' => $this->created_at,  // camelCase in JSON, snake_case in DB
                'updatedAt' => $this->updated_at,
            ],
            'relationships' => [
                'owner' => [
                    'data'  => ['type' => 'user', 'id' => $this->user_id],
                    'links' => ['self' => route('owners.show', ['owner' => $this->user_id])],
                ],
            ],
            'includes'      => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'links'         => ['self' => route('posts.show', ['post' => $this->id])],
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

### `paginate()` vs `simplePaginate()`

- Use **`paginate()`** when the client needs `meta.total` (e.g. to render page numbers). Runs a COUNT query.
- Use **`simplePaginate()`** when total count is expensive and not required. Returns only previous/next links, no total.

### Pagination response structure

`PostResource::collection(Post::paginate())` produces:

```json
{
    "data": [
        { "type": "post", "id": 1, "attributes": { ... } }
    ],
    "links": {
        "first": "https://api.example.com/posts?page=1",
        "last":  "https://api.example.com/posts?page=4",
        "prev":  null,
        "next":  "https://api.example.com/posts?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 4,
        "per_page": 15,
        "to": 15,
        "total": 52
    }
}
```

### Pagination query parameters

Laravel's `paginate()` reads `?page=N` out of the box. Use that unless you have a specific reason to adopt JSON:API namespacing.

| Style | Params | Notes |
|---|---|---|
| Laravel default | `?page=2&per_page=25` | `paginate()` reads `page` natively; `per_page` is conventional |
| JSON:API namespaced | `?page[number]=2&page[size]=25` | Consistent namespace; requires manual extraction |

**Laravel default (recommended):**

```php
public function index(Request $request): AnonymousResourceCollection
{
    $perPage = min((int) $request->input('per_page', 15), 100);

    return PostResource::collection(
        Post::query()->paginate($perPage)  // reads ?page=N automatically
    );
}
```

**JSON:API namespaced (if required by spec compliance):**

```php
$perPage = min((int) $request->input('page.size', 15), 100);
$page    = (int) $request->input('page.number', 1);
Post::query()->paginate(perPage: $perPage, page: $page);
```

**Rule:** Always cap per-page to prevent full-table dumps — unbounded page size is a denial-of-service vector.

### Controller usage

```php
// Collection
public function index(): AnonymousResourceCollection
{
    return PostResource::collection(Post::paginate());
}

// Single
public function show(int $id): PostResource|JsonResponse
{
    try {
        $post = Post::findOrFail($id);
    } catch (ModelNotFoundException) {
        return $this->error('Post cannot be found.', 404);
    }
    return new PostResource($post);
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
if ($this->include('owner')) {
    $post->load('user');           // load() — already-fetched model
}

// index()
$query = Post::query();
if ($this->include('owner')) {
    $query = $query->with('user');   // with() — pre-fetch in query
}
return PostResource::collection($query->paginate());
```

The resource's `whenLoaded()` handles the rest — no additional changes needed.

---

## 8. Filtering & Sorting

### 8A — Filter parameter conventions

The style you use depends entirely on which implementation you choose:

| Implementation | Filter style | Configurable? |
|---|---|---|
| DIY `QueryFilter` (8B) | Your choice — flat `?status=published` or namespaced `?filter[status]=published` | Yes — you control how `apply()` reads the request |
| spatie/laravel-query-builder (8C) | Always `?filter[status]=published` | No — namespace is baked in |

**If using DIY:** flat `?status=published` works fine for simple APIs. Use `filter[...]` namespacing when the query string also carries `sort`, `include`, and `page` — without it, `QueryFilter::apply()` iterates `$request->all()` and will attempt to dispatch on every key, including those.

**Filter types (DIY — flat style shown; prefix with `filter[key]` if you want namespacing):**

| Type | URL | SQL | When |
|---|---|---|---|
| Exact | `?status=published` | `= ?` | Enum/flags |
| Partial | `?title=*word*` | `LIKE ?` | Free text |
| Multi-value | `?status=A,B` | `IN (?)` | Comma-separated enum |
| Date range | `?createdAt=2024-01-01,2024-02-01` | `BETWEEN` | Date spans |
| Scope | `?scope=active` | Named scope | Complex logic |

---

### 8B — DIY `QueryFilter`

#### Abstract base

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
                abort(400, "Invalid sort column: {$sortAttribute}");
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

> **Deliberate asymmetry:** Unknown filter keys are silently ignored — extra query params are common and harmless. Unknown sort columns produce a **400 Bad Request** — an unknown sort column silently produces incorrect ordering, which is a correctness violation and a JSON:API spec violation. Do not swallow these.

#### PostFilter subclass

```php
class PostFilter extends QueryFilter
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

#### Model scope (required on every filterable model)

```php
public function scopeFilter(Builder $query, QueryFilter $filters): Builder
{
    return $filters->apply($query);
}
```

#### Controller usage

```php
public function index(PostFilter $filters): AnonymousResourceCollection
{
    return PostResource::collection(
        Post::filter($filters)->paginate()
    );
}
```

#### Sorting convention

| URL | Effect |
|---|---|
| `?sort=title` | Ascending |
| `?sort=-title` | Descending |
| `?sort=status,-title` | Multi-column |

Sortable columns must be whitelisted in `$sortable`. Use key-value pairs to alias camelCase API names to snake_case DB columns.

---

### 8C — spatie/laravel-query-builder (recommended for new projects)

Install:

```bash
composer require spatie/laravel-query-builder
```

The package replaces the entire DIY QueryFilter approach with a declarative API:

```php
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

public function index(): AnonymousResourceCollection
{
    $posts = QueryBuilder::for(Post::class)
        ->allowedFilters([
            AllowedFilter::exact('status'),
            AllowedFilter::partial('title'),
            AllowedFilter::scope('recentlyUpdated'),
        ])
        ->allowedSorts([
            'title',
            AllowedSort::field('createdAt', 'created_at'),
        ])
        ->allowedIncludes(['owner'])
        ->paginate();

    return PostResource::collection($posts);
}
```

Shape the 400 errors to match `ApiResponses` format in `bootstrap/app.php`:

```php
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;

$exceptions->render(function (InvalidFilterQuery $e) {
    return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
});
$exceptions->render(function (InvalidSortQuery $e) {
    return response()->json(['message' => $e->getMessage(), 'status' => 400], 400);
});
```

**Filter type mapping:**

| Method | SQL behaviour |
|---|---|
| `AllowedFilter::exact('status')` | `= ?` |
| `AllowedFilter::partial('title')` | `LIKE %?%` |
| `AllowedFilter::scope('active')` | Calls `scopeActive()` |
| `AllowedFilter::custom('name', new MyFilter)` | Arbitrary logic |

---

### Decision guide

| Use DIY `QueryFilter` when | Use `spatie/laravel-query-builder` when |
|---|---|
| You want zero external dependencies | Starting a new project |
| You need highly custom SQL per filter | Standard filter/sort/include patterns cover your needs |
| You have already built and tested it | You want less boilerplate and automatic 400 errors |

---

## 9. Nested Resources

```
GET /api/v1/owners/{owner}/posts
POST /api/v1/owners/{owner}/posts
GET /api/v1/owners/{owner}/posts/{post}
```

### Controller pattern

```php
class OwnerPostsController extends ApiController
{
    protected string $policyClass = PostPolicy::class;

    public function index(User $owner, PostFilter $filters): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::where('user_id', $owner->id)
                ->filter($filters)
                ->paginate()
        );
    }
}
```

### Ownership check — use `firstOrFail` with `where`

```php
// Instead of two queries:
// $post = Post::findOrFail($postId);
// if ($post->user_id !== $ownerId) { ... }

// One query that enforces both constraints:
$post = Post::where(['id' => $postId, 'user_id' => $ownerId])->firstOrFail();
```

---

## 10. CRUD — Write Operations

### PUT vs PATCH

| | PUT | PATCH |
|---|---|---|
| Name | `replace()` | `update()` |
| All fields | Required | Optional (`sometimes`) |
| Validation | `required` | `sometimes` |
| Auth | `ReplacePost` ability | `UpdatePost` or `UpdateOwnPost` |

### BasePostRequest — `mappedAttributes()`

Shared base class for all three write request classes. Maps JSON:API input keys to model column names. Only includes keys that were actually sent.

```php
class BasePostRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.title'             => 'title',
            'data.attributes.body'              => 'body',
            'data.attributes.status'            => 'status',
            'data.relationships.owner.data.id'  => 'user_id',
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
        'data.attributes.title'                    => ['required', 'string'],
        'data.attributes.status'                   => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        'data.relationships.owner.data.id'         => ['required', 'integer', 'exists:users,id'],
    ];

    if ($this->routeIs('posts.store')) {
        if ($this->user()->tokenCan(Abilities::CreateOwnPost)) {
            // Force owner to be the authenticated user
            $rules['data.relationships.owner.data.id'][] = 'size:' . $this->user()->id;
        }
    }

    return $rules;
}

// Inject route owner ID for nested route
protected function prepareForValidation(): void
{
    if ($this->routeIs('owners.posts.store')) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => ['owner' => ['data' => ['id' => $this->route('owner')]]],
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
        'data.attributes.title'            => ['sometimes', 'string'],
        'data.attributes.status'           => ['sometimes', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        'data.relationships.owner.data.id' => ['sometimes', 'integer'],
    ];

    // Prevent regular users from reassigning ownership
    if ($this->user()->tokenCan(Abilities::UpdateOwnPost)) {
        $rules['data.relationships.owner.data.id'] = ['prohibited'];
    }

    return $rules;
}
```

**`prohibited` rule:** Fails validation if the field is present at all. Returns a validation error — does not silently ignore the field. Use when a user submitting a field they are not allowed to set should be an explicit error.

### Controller write methods (consistent pattern)

```php
public function update(UpdatePostRequest $request, int $postId): PostResource|JsonResponse
{
    try {
        $post = Post::findOrFail($postId);
        $this->isAble('update', $post);
    } catch (ModelNotFoundException) {
        return $this->error('Post cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to update that resource.', 401);
    }

    $post->update($request->mappedAttributes());

    return new PostResource($post);
}

public function destroy(int $postId): JsonResponse
{
    try {
        $post = Post::findOrFail($postId);
        $this->isAble('destroy', $post);
    } catch (ModelNotFoundException) {
        return $this->error('Post cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to delete that resource.', 401);
    }

    $post->delete();
    return $this->ok('Post successfully deleted.');
}
```

**Why `int $id` over route model binding?**
Model binding throws Laravel's default `ModelNotFoundException`, which bypasses your structured error format. Manual `findOrFail` in a try/catch gives you full control over the error response.

---

## 11. Policies & Authorization

### Policy location by versioning level

- **Level 0 (no versioning):** `app/Policies/PostPolicy.php`
- **Level 1 (versioned):** `app/Policies/Api/V1/PostPolicy.php`

The `isAble()` mechanism is identical in both cases.

### ApiController `isAble()` — version-aware authorization

```php
// ApiController
protected string $policyClass;

public function isAble(string $ability, mixed $targetModel): void
{
    $this->authorize($ability, [$targetModel, $this->policyClass]);
}

// PostController
protected string $policyClass = PostPolicy::class;
```

Passing the explicit policy class bypasses Laravel's model-to-policy auto-discovery, allowing V1 and V2 policies to coexist for the same model.

For creation (no existing model): `$this->isAble('store', Post::class)` — pass the class string, not an instance.

### Policy structure

```php
class PostPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreatePost)
            || $user->tokenCan(Abilities::CreateOwnPost);
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->tokenCan(Abilities::UpdatePost)) {
            return true;                                  // manager — any post
        }
        if ($user->tokenCan(Abilities::UpdateOwnPost)) {
            return $user->id === $post->user_id;          // regular user — own only
        }
        return false;
    }

    public function replace(User $user, Post $post): bool
    {
        return $user->tokenCan(Abilities::ReplacePost);   // manager only
    }

    public function destroy(User $user, Post $post): bool
    {
        if ($user->tokenCan(Abilities::DeletePost)) { return true; }
        if ($user->tokenCan(Abilities::DeleteOwnPost)) {
            return $user->id === $post->user_id;
        }
        return false;
    }
}
```

---

## 12. Token Abilities

### Abilities class

```php
// app/Permissions/V1/Abilities.php  (or app/Permissions/Abilities.php for Level 0)
final class Abilities
{
    // Manager abilities — any resource
    public const string CreatePost  = 'post:create';
    public const string UpdatePost  = 'post:update';
    public const string ReplacePost = 'post:replace';
    public const string DeletePost  = 'post:delete';

    // Regular user abilities — own resources only
    public const string CreateOwnPost = 'post:own:create';
    public const string UpdateOwnPost = 'post:own:update';
    public const string DeleteOwnPost = 'post:own:delete';

    // User management — manager only
    public const string CreateUser  = 'user:create';
    public const string UpdateUser  = 'user:update';
    public const string ReplaceUser = 'user:replace';
    public const string DeleteUser  = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->is_manager) {
            return [
                self::CreatePost, self::UpdatePost, self::ReplacePost, self::DeletePost,
                self::CreateUser, self::UpdateUser, self::ReplaceUser, self::DeleteUser,
            ];
        }

        // Regular user — own posts only
        // Do NOT assign '*' — tokenCan('post:own:update') would return true for '*',
        // which defeats the ownership checks that are meant to restrict managers.
        return [
            self::CreateOwnPost,
            self::UpdateOwnPost,
            self::DeleteOwnPost,
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
            "owner": {
                "data": { "id": 1 }
            }
        }
    }
}
```

Laravel validation keys use dot notation: `data.attributes.title`, `data.relationships.owner.data.id`.

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
    if ($this->routeIs('owners.posts.store')) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => ['owner' => ['data' => ['id' => $this->route('owner')]]],
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

## 15. Semantic URL Roles

When a domain has users that play different roles, use semantic naming in URLs:

- `GET /api/v1/owners` — users who have created posts (read-only, derived from the resource table)
- `GET /api/v1/users` — user management (full CRUD, manager-only)

The pattern: expose a read-only endpoint named for the role, scoped to users who have created the primary resource.

### Owners endpoint (scoped join)

```php
public function index(): AnonymousResourceCollection
{
    return UserResource::collection(
        User::select('users.*')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->filter(new OwnerFilter(request()))
            ->distinct()  // one row per user regardless of post count
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
$this->actingAs($user)->getJson('/api/v1/posts')->assertOk();

// withToken() tests the actual token + ability system
$token = $user->createToken('test', [Abilities::UpdateOwnPost])->plainTextToken;
$this->withToken($token)->patchJson("/api/v1/posts/{$id}", [...]);
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
$this->assertDatabaseHas('posts', ['title' => 'Test']);
$this->assertDatabaseMissing('posts', ['id' => $post->id]);
$this->assertDatabaseCount('personal_access_tokens', 1);
```

### Dataset for validation rules

```php
it('validates required fields', function (array $body) {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/posts', $body)
        ->assertUnprocessable();
})->with([
    'missing title'  => [['data' => ['attributes' => ['status' => 'A']]]],
    'invalid status' => [['data' => ['attributes' => ['title' => 'T', 'status' => 'Z']]]],
    'missing body'   => [['data' => ['attributes' => ['title' => 'T', 'status' => 'A']]]],
]);
```

### Factory helpers

```php
Post::factory()->for($user)->create();          // set BelongsTo
Post::factory()->count(3)->for($user)->create();
User::factory()->hasPosts(2)->create();         // inverse HasMany
User::factory()->count(10)->create();
Post::factory()->count(100)->recycle($users)->create();  // share existing models
```

---

## 17. Common Artisan Commands

```bash
# Controllers
php artisan make:controller Api/V1/PostController --resource --requests --no-interaction

# Requests
php artisan make:request Api/V1/StorePostRequest --no-interaction

# Resources
php artisan make:resource Api/V1/PostResource --no-interaction

# Policies
php artisan make:policy Api/V1/PostPolicy --no-interaction

# Models (with migration + factory)
php artisan make:model Post -mf --no-interaction

# Filters (plain class)
php artisan make:class Http/Filters/V1/PostFilter --no-interaction

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
- **Return 400 for unknown sort columns** — do not silently ignore them. An unknown column produces incorrect ordering without any indication to the client, which is both a correctness violation and a JSON:API spec violation.
- **Cap `page[size]`** — unbounded page size is a denial-of-service vector. Always enforce a maximum (e.g. 100).
