# Debugging Livewire Test Errors - Getting Better Error Messages

## The Problem

When tests fail, Livewire often gives cryptic error messages:

```
Component did not perform a redirect.
Failed asserting that an array has the key 'redirect'.
```

**This doesn't tell you WHY!** Is it:
- Authorization failure?
- Validation error?
- Database error?
- Logic error?

---

## Real Example: The Typo in UserPolicy

### The Bug
```php
// app/Policies/UserPolicy.php
public function create(User $user): bool
{
    return $user->role === 'Admin';  // ❌ Capital 'A'
    // But database has: 'admin' (lowercase)
}
```

### The Test
```php
test('admin can create a new doctor', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
});
```

### The Error Message (❌ USELESS)
```
Component did not perform a redirect.
Failed asserting that an array has the key 'redirect'.
```

**No mention of:**
- Authorization failed
- Policy returned false
- 403 error
- What went wrong

---

## Solution: Better Test Assertions

### ✅ Method 1: Check for Errors First

```php
test('admin can create a new doctor - with error checking', function () {
    $this->actingAs($this->admin);

    $component = Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save');
    
    // Check for errors BEFORE checking redirect
    $component
        ->assertHasNoErrors()  // ✅ Shows validation errors if any
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
});
```

**Better Error:**
```
Component has errors: "authorization"
or
Component has errors: "email", "password"
```

---

### ✅ Method 2: Dump Component State

```php
test('admin can create a new doctor - with dump', function () {
    $this->actingAs($this->admin);

    $component = Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save');
    
    // Dump everything when debugging
    if ($component->errors()->isNotEmpty()) {
        dump('Validation Errors:', $component->errors()->toArray());
    }
    
    // Check the component's status
    dump('Component Status:', [
        'has_errors' => !$component->errors()->isEmpty(),
        'redirected' => isset($component->effects['redirect']),
    ]);
    
    $component->assertRedirect(route('admin.users.index'));
});
```

**Output:**
```php
"Validation Errors:" // tests/Feature/Admin/UserManagementTest.php:95
array:1 [
  "authorization" => array:1 [
    0 => "This action is unauthorized."
  ]
]

"Component Status:" // tests/Feature/Admin/UserManagementTest.php:98
array:2 [
  "has_errors" => true
  "redirected" => false
]
```

---

### ✅ Method 3: Use dd() to Stop and Inspect

```php
test('admin can create a new doctor - with dd', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->dd();  // ✅ Dies and dumps EVERYTHING
});
```

**Shows:**
- All component properties
- All validation errors
- All effects (redirect, dispatch, etc.)
- Session data
- Much more!

---

### ✅ Method 4: Catch Authorization Exceptions

```php
test('admin can create a new doctor - catch exceptions', function () {
    $this->actingAs($this->admin);

    try {
        Livewire::test(\App\Livewire\Admin\CreateUser::class)
            ->set('name', 'Dr. Jane Doe')
            ->set('email', 'jane.doe@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'doktor')
            ->call('save')
            ->assertRedirect(route('admin.users.index'));
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        dump('Authorization Failed!', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ]);
        throw $e;
    }
});
```

**Output:**
```
"Authorization Failed!" // tests/Feature/Admin/UserManagementTest.php:102
array:2 [
  "message" => "This action is unauthorized."
  "code" => 0
]
```

---

### ✅ Method 5: Use assertForbidden for Authorization

If you expect authorization to fail:

```php
test('doktor cannot create users', function () {
    Livewire::actingAs($this->doktor)
        ->test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'pacijent')
        ->call('save')
        ->assertForbidden();  // ✅ Clear what we're testing
});
```

**Clear Error:**
```
Expected response status code [403] but received 200.
// Now you know authorization DIDN'T fail when it should have!
```

---

## Debugging Authorization Failures

### Check Policy is Being Used

```php
test('debug policy usage', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Check policy directly
    dump('Policy Check:', [
        'user_role' => $admin->role,
        'can_create' => Gate::allows('create', User::class),
        'policy_class' => get_class(Gate::getPolicyFor(User::class)),
    ]);
    
    auth()->login($admin);
    
    dump('After Login:', [
        'auth_check' => auth()->check(),
        'auth_role' => auth()->user()->role,
        'can_create' => auth()->user()->can('create', User::class),
    ]);
});
```

**Output reveals the typo:**
```php
"Policy Check:" // tests/Feature/Admin/UserManagementTest.php:95
array:3 [
  "user_role" => "admin"        // ← lowercase!
  "can_create" => false          // ← Should be true!
  "policy_class" => "App\Policies\UserPolicy"
]
```

---

## Best Practices for Test Assertions

### ❌ BAD: Assert redirect immediately
```php
->call('save')
->assertRedirect(route('admin.users.index'))  // ❌ Cryptic error
```

### ✅ GOOD: Check for errors first
```php
->call('save')
->assertHasNoErrors()  // ✅ Shows what failed
->assertRedirect(route('admin.users.index'))
```

### ✅ BETTER: Add descriptive expectations
```php
$component = Livewire::test(CreateUser::class)
    ->set('name', 'Dr. Jane Doe')
    ->set('email', 'jane.doe@example.com')
    ->set('password', 'password123')
    ->set('password_confirmation', 'password123')
    ->set('role', 'doktor')
    ->call('save');

// Check what you expect
expect($component->errors()->isEmpty())
    ->toBeTrue('Component should have no validation errors');

expect($component->effects)
    ->toHaveKey('redirect', 'Component should redirect after save');

$component
    ->assertRedirect(route('admin.users.index'))
    ->assertSessionHas('success');
```

---

## Common Livewire Test Issues

### Issue 1: "Component did not perform a redirect"

**Possible Causes:**
1. ❌ Authorization failed (`$this->authorize()`)
2. ❌ Validation failed
3. ❌ Database constraint error
4. ❌ Exception thrown
5. ❌ Early return in method

**Debug:**
```php
$component = Livewire::test(...)
    ->call('save');

// Check what happened
dump([
    'has_errors' => !$component->errors()->isEmpty(),
    'errors' => $component->errors()->toArray(),
    'effects' => $component->effects,
    'redirected' => isset($component->effects['redirect']),
]);
```

---

### Issue 2: "Component has errors: X"

**This is GOOD!** It tells you what failed validation.

**Example:**
```
Component has errors: "email", "password"
```

**Debug:**
```php
$component = Livewire::test(...)
    ->call('save');

dump($component->errors()->toArray());
// Shows exact validation messages
```

---

### Issue 3: Test passes but shouldn't

**Check the database:**
```php
$component = Livewire::test(CreateUser::class)
    ->set('name', 'Test')
    ->set('email', 'test@example.com')
    ->set('password', 'password123')
    ->set('password_confirmation', 'password123')
    ->set('role', 'doktor')
    ->call('save');

// Did it actually save?
$this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
]);

// Or check count
expect(User::count())->toBe(2);  // 1 admin + 1 new user
```

---

## Your Specific Case: The Typo

### The Bug
```php
// UserPolicy.php
return $user->role === 'Admin';  // ❌ Should be 'admin'
```

### How to Catch It

**Option 1: Direct policy test**
```php
test('policy allows admin to create users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    expect($admin->can('create', User::class))
        ->toBeTrue('Admin should be able to create users');
});
```

**This fails with:**
```
Failed asserting that false is true.
Admin should be able to create users
```

**Option 2: Test the component with better assertions**
```php
test('admin can create a new doctor', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $component = Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save');
    
    // This will show authorization error
    $component->assertHasNoErrors();  // ❌ Fails with auth error
    
    $component
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
});
```

---

## Summary: How to Get Better Error Messages

1. **Always use `assertHasNoErrors()` before `assertRedirect()`**
   ```php
   ->call('save')
   ->assertHasNoErrors()  // ✅ Shows what went wrong
   ->assertRedirect(...)
   ```

2. **Use `dump()` or `dd()` when debugging**
   ```php
   $component->call('save')->dd();  // Shows everything
   ```

3. **Check component state manually**
   ```php
   dump($component->errors()->toArray());
   dump($component->effects);
   ```

4. **Test policies directly**
   ```php
   expect($user->can('create', User::class))->toBeTrue();
   ```

5. **Use descriptive expect() messages**
   ```php
   expect($component->errors()->isEmpty())
       ->toBeTrue('Component should save without errors');
   ```

6. **Catch specific exceptions**
   ```php
   try {
       $component->call('save');
   } catch (AuthorizationException $e) {
       dump('Authorization failed:', $e->getMessage());
   }
   ```

---

## Recommended Test Pattern

```php
test('admin can create a new doctor', function () {
    $this->actingAs($this->admin);

    $component = Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save');
    
    // Debug aid (can comment out when working)
    if ($component->errors()->isNotEmpty()) {
        dump('Errors:', $component->errors()->toArray());
    }
    
    // Assertions in the right order
    $component
        ->assertHasNoErrors()  // ✅ Check errors first
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
    
    // Verify side effects
    $this->assertDatabaseHas('users', [
        'email' => 'jane.doe@example.com',
        'role' => 'doktor',
    ]);
});
```

**This pattern catches:**
- ✅ Validation errors
- ✅ Authorization errors
- ✅ Database errors
- ✅ Logic errors
- ✅ Shows exactly what went wrong

---

## Key Takeaway

**Never just assert redirect!** Always check for errors first:

```php
// ❌ BAD
->call('save')->assertRedirect(...);

// ✅ GOOD
->call('save')->assertHasNoErrors()->assertRedirect(...);
```

This simple change makes debugging 10x easier! 🎯
