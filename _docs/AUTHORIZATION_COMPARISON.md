# Authorization Across Laravel Layers - Complete Guide

## The Question
"For Livewire I need to use `authorize()` for policy to be in effect, what about REST API or routes declared?"

## The Answer
**Different layers need different approaches!** Here's how policies work across Laravel:

---

## 🎯 Quick Comparison Table

| Layer | Policy Auto-Applied? | How to Use Policy | Example |
|-------|---------------------|-------------------|---------|
| **Routes** | ✅ Yes (via middleware) | `->middleware('can:create,App\Models\User')` | Route protection |
| **Controllers** | ❌ No (manual) | `$this->authorize('create', User::class)` | Explicit call |
| **Livewire** | ❌ No (manual) | `$this->authorize('create', User::class)` | Explicit call |
| **API Routes** | ✅ Yes (via middleware) | `->middleware('can:update,post')` | Route protection |
| **Blade** | ✅ Yes (directive) | `@can('create', App\Models\User::class)` | View-level check |
| **Form Requests** | ❌ No (manual) | `Gate::authorize('create', User::class)` | In authorize() method |

---

## 1️⃣ Routes (Web & API) - Policy via Middleware

### ✅ Policies AUTOMATICALLY Applied via Middleware

Routes can use the `can` middleware to apply policies **before** the request even hits your controller:

```php
// routes/web.php or routes/api.php

// Option 1: Using 'can' middleware (recommended)
Route::get('/users/create', function () {
    return view('admin.users.create');
})->middleware('can:create,App\Models\User');

// Option 2: Using ->can() method (shorthand)
Route::get('/users/create', function () {
    return view('admin.users.create');
})->can('create', \App\Models\User::class);

// Option 3: With model binding
Route::put('/users/{user}', function (User $user) {
    // User can update this specific user
})->middleware('can:update,user');

// Or shorthand:
Route::put('/users/{user}', function (User $user) {
    // ...
})->can('update', 'user');

// Option 4: Group routes with authorization
Route::middleware('can:viewAny,App\Models\User')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
});
```

**How it works:**
1. Request hits the route
2. `can` middleware checks UserPolicy
3. If policy returns `false` → 403 Forbidden
4. If policy returns `true` → Request continues

**Benefits:**
- ✅ Automatic - no manual checks needed
- ✅ Fails early - before controller executes
- ✅ Cleaner controllers - no authorization code

---

## 2️⃣ Controllers - Manual `authorize()` Calls

### ❌ Policies NOT Automatically Applied

In controllers, you must explicitly call `authorize()`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display all users
     */
    public function index()
    {
        // Option 1: authorize() - throws 403 if fails
        $this->authorize('viewAny', User::class);
        
        $users = User::all();
        return view('users.index', ['users' => $users]);
    }
    
    /**
     * Show create form
     */
    public function create()
    {
        // Authorize without model instance
        $this->authorize('create', User::class);
        
        return view('users.create');
    }
    
    /**
     * Update a user
     */
    public function update(Request $request, User $user)
    {
        // Authorize with model instance
        $this->authorize('update', $user);
        
        $user->update($request->validated());
        return redirect()->route('users.index');
    }
    
    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        // Option 2: Check first, custom response
        if (auth()->user()->cannot('delete', $user)) {
            return response()->json([
                'error' => 'You cannot delete this user'
            ], 403);
        }
        
        $user->delete();
        return response()->json(['success' => true]);
    }
}
```

**Available Methods:**
```php
// Throws AuthorizationException if fails (403)
$this->authorize('create', User::class);

// Returns boolean
if (auth()->user()->can('create', User::class)) { }
if (auth()->user()->cannot('create', User::class)) { }

// Using Gate facade
use Illuminate\Support\Facades\Gate;

Gate::authorize('create', User::class);  // Throws exception
Gate::allows('create', User::class);     // Returns boolean
Gate::denies('create', User::class);     // Returns boolean
```

---

## 3️⃣ Livewire Components - Manual `authorize()` Calls

### ❌ Policies NOT Automatically Applied (Even with Route Middleware!)

**Critical**: Livewire components need **explicit authorization** in action methods:

```php
<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Component
{
    public $name;
    public $email;
    public $password;
    public $role;
    
    public function save()
    {
        // ⚠️ MUST explicitly authorize!
        // Route middleware doesn't protect Livewire actions
        $this->authorize('create', User::class);
        
        $this->validate();
        
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);
        
        session()->flash('success', 'User created!');
        return $this->redirect(route('admin.users.index'));
    }
}
```

**Why is this necessary?**
```php
// Route middleware protects page load:
Route::get('/users/create', function () {
    return view('admin.users.create');  // ✅ Protected by route middleware
})->middleware('can:create,App\Models\User');

// But Livewire actions happen AFTER page load:
// 1. User loads page → middleware checks ✅
// 2. User fills form
// 3. User clicks save → Livewire AJAX request
// 4. save() method executes → middleware NOT re-applied! ❌
```

**Alternative: Using Gate facade:**
```php
use Illuminate\Support\Facades\Gate;

public function save()
{
    // Option 1: Throw exception if unauthorized
    Gate::authorize('create', User::class);
    
    // Option 2: Check and handle manually
    if (Gate::denies('create', User::class)) {
        session()->flash('error', 'Unauthorized');
        return;
    }
    
    // Option 3: Using auth()->user()
    if (auth()->user()->cannot('create', User::class)) {
        abort(403);
    }
    
    // ... rest of logic
}
```

---

## 4️⃣ REST API Routes - Policy via Middleware

### ✅ Policies Automatically Applied (Same as Web Routes)

API routes work **exactly like web routes** - use middleware:

```php
// routes/api.php

use App\Models\Post;

// Protect API endpoints with policies
Route::middleware('auth:sanctum')->group(function () {
    
    // Check 'create' ability
    Route::post('/posts', [PostController::class, 'store'])
        ->middleware('can:create,App\Models\Post');
    
    // Check 'update' ability on specific post
    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->middleware('can:update,post');
    
    // Check 'delete' ability on specific post
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->middleware('can:delete,post');
    
    // Shorthand syntax
    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->can('update', 'post');
});
```

**API Controller Example:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a new post
     * 
     * Authorization already handled by route middleware!
     */
    public function store(Request $request)
    {
        // No need to authorize here - middleware did it!
        // But you CAN add it for defense-in-depth:
        $this->authorize('create', Post::class);
        
        $post = Post::create($request->validated());
        
        return response()->json($post, 201);
    }
    
    /**
     * Update a post
     */
    public function update(Request $request, Post $post)
    {
        // Again, middleware already checked, but explicit is safer:
        $this->authorize('update', $post);
        
        $post->update($request->validated());
        
        return response()->json($post);
    }
}
```

---

## 5️⃣ Blade Templates - Policy via Directives

### ✅ Policies Automatically Applied via `@can` Directive

Blade has built-in directives for authorization:

```blade
{{-- Check if user can create --}}
@can('create', App\Models\User::class)
    <a href="{{ route('users.create') }}">
        Create New User
    </a>
@endcan

{{-- Check if user can update specific user --}}
@can('update', $user)
    <a href="{{ route('users.edit', $user) }}">
        Edit
    </a>
@endcan

{{-- Check if user can delete specific user --}}
@can('delete', $user)
    <form action="{{ route('users.destroy', $user) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Delete</button>
    </form>
@endcan

{{-- Inverse check --}}
@cannot('delete', $user)
    <p>You cannot delete this user</p>
@endcannot

{{-- Check multiple abilities --}}
@canany(['update', 'delete'], $user)
    <div class="user-actions">
        <!-- Show action buttons -->
    </div>
@endcanany
```

**Important**: This is **UI-only protection**! Always also protect the backend:

```blade
{{-- ❌ INSECURE: Only Blade protection --}}
@can('delete', $user)
    <form action="/users/{{ $user->id }}" method="POST">
        @csrf
        @method('DELETE')
        <button>Delete</button>
    </form>
@endcan

{{-- ✅ SECURE: Backend also protected --}}
Route::delete('/users/{user}', function (User $user) {
    Gate::authorize('delete', $user);  // Backend check
    $user->delete();
})->middleware('can:delete,user');  // Or route middleware
```

---

## 6️⃣ Form Requests - Manual Authorization

### ❌ Policies NOT Automatically Applied

Form Requests have an `authorize()` method:

```php
<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');  // Get route parameter
        
        // Use policy to check authorization
        return $this->user()->can('update', $user);
    }
    
    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
        ];
    }
}
```

**Usage in Controller:**
```php
public function update(UpdateUserRequest $request, User $user)
{
    // Authorization already checked by FormRequest
    // Validation already passed
    
    $user->update($request->validated());
    return redirect()->route('users.index');
}
```

---

## 🔧 Your Current Application

### What You Have:

**Routes (web.php):**
```php
// ✅ Protected with custom middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', ...);          // Protected ✅
    Route::get('/users/create', ...);   // Protected ✅
    Route::get('/users/{user}/edit', ...); // Protected ✅
});
```

**Problem**: `role:admin` middleware with arguments **not supported** by Livewire persistent middleware!

**Livewire Components:**
```php
// ✅ Currently using manual checks
public function save()
{
    if (auth()->user()->role !== 'admin') {
        abort(403);
    }
    // ... logic
}
```

---

## 💡 Recommendations for Your App

### Option 1: Fix UserPolicy + Use Everywhere (BEST)

**Step 1: Fix UserPolicy**
```php
// app/Policies/UserPolicy.php
public function create(User $user): bool
{
    return $user->role === 'admin';  // Fix: lowercase 'admin'
}

public function update(User $user, User $model): bool
{
    return $user->role === 'admin';
}

public function delete(User $user, User $model): bool
{
    return $user->role === 'admin' && $user->id !== $model->id;
}
```

**Step 2: Use Policy in Routes**
```php
// routes/web.php
Route::middleware(['auth', 'can:viewAny,App\Models\User'])->group(function () {
    Route::get('/users', ...);
});

Route::get('/users/create', ...)
    ->middleware(['auth', 'can:create,App\Models\User']);

Route::get('/users/{user}/edit', ...)
    ->middleware(['auth', 'can:update,user']);
```

**Step 3: Use Policy in Livewire**
```php
public function save()
{
    $this->authorize('create', User::class);
    // ... logic
}
```

**Benefits:**
- ✅ Centralized authorization
- ✅ Reusable everywhere
- ✅ Testable
- ✅ Self-documenting

---

### Option 2: Keep Current Approach (SIMPLER)

If you prefer to keep it simple:

**Routes:**
```php
// Keep custom middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    // ...
});
```

**Livewire:**
```php
// Keep manual checks with comment
public function save()
{
    // Manual check needed because Livewire doesn't re-apply
    // route middleware with arguments (role:admin)
    if (auth()->user()->role !== 'admin') {
        abort(403);
    }
    // ... logic
}
```

**Benefits:**
- ✅ Simple and explicit
- ✅ No policy needed
- ✅ Easy to understand

---

## 📊 Summary Table: When to Use What

| Scenario | Best Approach | Example |
|----------|--------------|---------|
| Protect entire route | Route middleware | `->middleware('can:create,Post')` |
| Check in controller | `$this->authorize()` | `$this->authorize('update', $post)` |
| Check in Livewire | `$this->authorize()` | Same as controller |
| Hide UI elements | `@can` directive | `@can('delete', $user)` |
| API endpoints | Route middleware | Same as web routes |
| Form validation | FormRequest `authorize()` | Override authorize() method |
| Complex logic | Gate facade | `Gate::authorize('publish', $post)` |

---

## 🎯 Key Takeaways

1. **Routes**: Policies applied automatically via `can` middleware
2. **Controllers**: Must call `$this->authorize()` manually
3. **Livewire**: Must call `$this->authorize()` manually (route middleware doesn't help!)
4. **API**: Same as web routes - use middleware
5. **Blade**: Use `@can` directive for UI
6. **Everywhere**: Backend authorization is mandatory, UI is optional

---

## 🔒 Security Best Practices

1. **Always authorize on backend** - Never trust frontend checks alone
2. **Use policies** - Centralized, testable, reusable
3. **Fail closed** - Default to deny, explicitly allow
4. **Layer defense** - Route middleware + controller/Livewire checks
5. **Test authorization** - Write tests for each policy method

---

## Example: Full Stack Authorization

```php
// 1. POLICY
class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}

// 2. ROUTE (Web)
Route::put('/posts/{post}', [PostController::class, 'update'])
    ->middleware('can:update,post');

// 3. CONTROLLER
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);  // Defense in depth
    $post->update($request->validated());
}

// 4. LIVEWIRE
public function save()
{
    $this->authorize('update', $this->post);  // Required!
    $this->post->update(['title' => $this->title]);
}

// 5. BLADE
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Edit</a>
@endcan

// 6. API ROUTE
Route::put('/api/posts/{post}', [ApiPostController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:update,post']);
```

**All layers protected!** 🔒
