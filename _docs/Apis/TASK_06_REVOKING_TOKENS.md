# Revoking Authentication Tokens — Task 06

> **Source:** Laravel API Masterclass — Episode 06
> **Created:** 2026-03-10
> **Run Time:** 9m 01s
> **Status:** Study Task

---

## Overview

A token issued at login should not live forever by default. Users need the ability to log out — meaning the token becomes invalid immediately. Additionally, tokens can be configured to expire automatically after a set time. This episode covers both: manual revocation and automatic expiration.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `$user->tokens()->delete()` | Deletes ALL tokens for a user |
| `$user->tokens()->where('id', $id)->delete()` | Deletes one token by ID |
| `$request->user()->currentAccessToken()->delete()` | Deletes the token used for this specific request |
| Token expiration | `expiresAt` arg on `createToken()` or global config in `sanctum.php` |
| `auth:sanctum` on logout | Ensures only authenticated users can hit the logout endpoint |
| Postman global variable | Shared bearer token variable across all requests in a collection |

---

## Task 1 — Understand the Three Revocation Approaches

### Goal
Know which revocation method to use for which scenario before writing any code.

### Approach 1 — Delete all tokens

```php
$request->user()->tokens()->delete();
```

**Effect:** Every token the user has, across every device/session, is gone.

**When to use:** "Revoke all sessions" feature — user clicks "sign out everywhere" from their account security settings.

**Why NOT for normal logout:** If the user has a long-lived API token for a service or automation script, deleting it breaks that integration. They will not know immediately and will be confused.

### Approach 2 — Delete by token ID

```php
$request->user()->tokens()->where('id', $tokenId)->delete();
```

**When to use:** Admin revokes a specific token from a token management screen.

**Why NOT for normal logout:** You do not have the token ID at the point of logout — you only have the raw token string in the header.

### Approach 3 — Delete the current access token (correct choice for logout)

```php
$request->user()->currentAccessToken()->delete();
```

**Effect:** Only the token that authenticated *this request* is deleted. All other tokens the user has remain untouched.

**Why this is correct:** Precise, scoped, no side effects on other sessions or integrations.

---

## Task 2 — Implement the Logout Method

### Goal
Add a `logout` method to `AuthController` that revokes the current token.

### Instructions

Open `app/Http/Controllers/Api/AuthController.php` and add:

```php
use Illuminate\Http\Request;

public function logout(Request $request): JsonResponse
{
    $request->user()->currentAccessToken()->delete();

    return $this->ok('');
}
```

> The response body can be minimal — no meaningful data to return after logout. An empty message with status 200 communicates success. You could use 204 No Content instead (no body at all).

---

## Task 3 — Register the Logout Route

### Goal
Add the logout route to `routes/api.php` behind `auth:sanctum` middleware.

### Key Concept — Why protect the logout route?

If an unauthenticated request hits logout, `$request->user()` is `null`. Calling `->currentAccessToken()` on null throws a fatal error. The middleware rejects unauthenticated requests before your code runs.

### Instructions

In `routes/api.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

The login and register routes remain outside this group — you cannot be authenticated when logging in.

---

## Task 4 — Token Expiration

### Goal
Make tokens expire automatically after a set period.

### Option A — Per-token expiration (at creation time)

In `login()`, change `createToken()` to include an expiry:

```php
use Carbon\Carbon;

$token = $user->createToken(
    'API token for ' . $user->email,
    ['*'],                              // abilities — '*' means all
    Carbon::now()->addMonth()           // expires in 1 month
);

return $this->ok('Authenticated.', [
    'token' => $token->plainTextToken,
]);
```

**`Carbon::now()->addX()` options:**

| Expression | Duration |
|---|---|
| `addMinutes(30)` | 30 minutes |
| `addHours(8)` | 8 hours |
| `addDay()` | 1 day |
| `addMonth()` | 1 month |

After creation, the `expires_at` column in `personal_access_tokens` will have a value instead of `null`.

### Option B — Global expiration via config

Open `config/sanctum.php`. Find the `expiration` key:

```php
/*
 * Number of minutes that issued tokens will be considered valid.
 * This overrides any per-token expiry set at creation time.
 */
'expiration' => null,
```

Set it to a number of minutes:

```php
'expiration' => 60 * 24,        // 1 day
'expiration' => 60 * 24 * 30,   // 30 days
'expiration' => 30,             // 30 minutes
```

> **Warning:** The global config **overrides** per-token expiry set via `expiresAt`. If you set both, the config value wins.

---

## Task 5 — Postman: Bearer Token as a Global Variable

### Goal
Avoid manually copying the token into every Postman request by storing it as a variable.

### Setup

1. After a successful login, copy the token value from the response
2. In Postman, go to **Environments → Globals**
3. Add a variable named `bearer` — set both Initial Value and Current Value to your token
4. Save

### Usage in requests

In any request, go to **Authorization → Bearer Token**, set the value to:

```
{{bearer}}
```

Now all requests that use `{{bearer}}` automatically use the current token value. When the token changes (new login), update the global variable once — all requests update automatically.

---

## Task 6 — Pest Tests

```php
use App\Models\User;

it('logout revokes the current token', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/logout')
        ->assertOk();

    // Token record should be gone
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

it('logout requires authentication', function () {
    $this->postJson('/api/logout')
        ->assertUnauthorized();
});

it('logout only revokes the current token not all tokens', function () {
    $user   = User::factory()->create();
    $token1 = $user->createToken('device-1')->plainTextToken;
    $token2 = $user->createToken('device-2')->plainTextToken;

    $this->withToken($token1)
        ->postJson('/api/logout')
        ->assertOk();

    // token1 is gone, token2 still exists
    $this->assertDatabaseCount('personal_access_tokens', 1);
});
```

---

## Final Checklist

- [ ] `logout()` method added to `AuthController`
- [ ] Uses `currentAccessToken()->delete()` (not `tokens()->delete()`)
- [ ] Logout route in `api.php` behind `auth:sanctum` middleware
- [ ] `createToken()` updated with abilities `['*']` and expiry `Carbon::now()->addMonth()`
- [ ] `expires_at` column is populated in DB after login
- [ ] Postman `{{bearer}}` global variable configured
- [ ] After logout, token is absent from `personal_access_tokens`
- [ ] All Pest tests passing

---

## What's Next (Episode 07)

Currently the API dumps raw Eloquent model data — all columns, snake_case names, no structure. The next episode introduces **Eloquent Resources** to control exactly what the JSON payload looks like.
