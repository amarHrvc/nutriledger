# 🔐 Laravel Sanctum Token Format

## Token Structure

NutriLabs uses **Laravel Sanctum** for API authentication. Tokens follow this format:

```
2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
│ └─ Token hash (unique identifier)
└──── Token ID (2 = second token created)
```

### Format Breakdown

| Part | Example | Meaning |
|------|---------|---------|
| **ID** | `2` | Which token number (1st, 2nd, 3rd, etc.) |
| **Separator** | `\|` | Literal pipe character dividing parts |
| **Hash** | `bmBOPQ5LbGBIMkJY...` | Unique token value (sent in `Authorization` header) |

---

## How to Use in Postman

### Bearer Token Header Format

When Postman sends a request, it constructs the header like this:

```
Authorization: Bearer 2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
                      └─ Full token (ID|Hash)
```

### In Collection Auth Configuration

```json
{
  "auth": {
    "type": "bearer",
    "bearer": [
      {
        "key": "token",
        "value": "{{admin_token}}",
        "type": "string"
      }
    ]
  }
}
```

Postman automatically:
1. Takes `{{admin_token}}` value: `2|bmBOPQ5LbGBIMkJY...`
2. Creates header: `Authorization: Bearer 2|bmBOPQ5LbGBIMkJY...`
3. Sends with every request

---

## Pre-Request Script Handling

Updated pre-request script now recognizes Sanctum format:

```javascript
// Validate token format (should contain '|' for Sanctum tokens)
function isValidSanctumToken(token) {
    return token && typeof token === 'string' && token.includes('|');
}

// Parse token for logging
const tokenParts = token.split('|');
const tokenId = tokenParts[0];        // "2"
const tokenHash = tokenParts[1];      // "bmBOPQ5LbGBIMkJY..."

console.log('✓ Using Sanctum token: ' + tokenId + '|' + tokenHash.substring(0, 20) + '...');
// Output: ✓ Using Sanctum token: 2|bmBOPQ5LbGBIMkJY...
```

---

## Token Lifecycle

### 1. Login Request Creates Token

```
POST /api/login
  ↓
Response:
{
  "data": {
    "token": "2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6",
    "user": { ... }
  }
}
  ↓
Postman captures and stores in {{admin_token}}
```

### 2. Subsequent Requests Use Token

```
GET /api/patients
Headers:
  Authorization: Bearer {{admin_token}}
             = Bearer 2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
  ↓
Server validates token and processes request
```

### 3. Logout Deletes Token

```
POST /api/logout
Headers:
  Authorization: Bearer 2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
  ↓
Server deletes token from database
  ↓
Token no longer valid for future requests
```

---

## Token Duration & Expiration

### Expiration Time

From `config('sanctum.expiration')` in Laravel config:

```bash
# In backend/.env
SANCTUM_EXPIRATION=60  # 60 minutes
```

### How Expiration Works

```
Token created:      2026-04-10 17:30:00
Expires at:         2026-04-10 18:30:00 (60 min later)
Current time:       2026-04-10 17:45:00
Status:             ✅ Valid (15 min left)

Current time:       2026-04-10 18:35:00
Status:             ❌ Expired (5 min past expiry)
Error on request:   401 Unauthorized
Solution:           Run login request again
```

---

## Storing Multiple Tokens

### Admin Token
```
{{admin_token}} = 2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
                  (captured from "01 - Login (Admin)")
```

### Doctor Token
```
{{doctor_token}} = 3|aB9xCdEfGhIjKlMnOpQrStUvWxYzAbCdEfGhIjKlMnOp
                   (captured from "02 - Login (Doctor)")
```

### Patient Token
```
{{patient_token}} = 4|zYxWvUtSrQpOnMlKjIhGfEdCbAzYxWvUtSrQpOnMlKj
                    (if needed for authorization tests)
```

### In Environment
```json
{
  "admin_token": "2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6",
  "doctor_token": "3|aB9xCdEfGhIjKlMnOpQrStUvWxYzAbCdEfGhIjKlMnOp",
  "patient_token": "4|zYxWvUtSrQpOnMlKjIhGfEdCbAzYxWvUtSrQpOnMlKj"
}
```

---

## Troubleshooting

### "Invalid token" or "Unauthenticated" (401)

**Cause:** Token is missing or expired

**Check:**
1. Environment → See if `{{admin_token}}` is empty
2. Console → Check pre-request script output
3. Response → Look for error message details

**Fix:**
```
Run "01 - Login (Admin)" again to get fresh token
```

### Token Not Updating

**Cause:** Postman not capturing token from response

**Check:**
1. Run login request
2. Check "Tests" tab → See if assertions pass
3. Check environment → Value should be populated

**Debug Script:**
```javascript
// Add to login test script
var jsonData = pm.response.json();
console.log('Response token:', jsonData.data.token);
console.log('Setting to admin_token:', jsonData.data.token);
pm.environment.set('admin_token', jsonData.data.token);
console.log('Stored value:', pm.environment.get('admin_token'));
```

### Token Includes Extra Characters

**Valid Token:**
```
2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
```

**Invalid (includes newline):**
```
2|bmBOPQ5LbGBIMkJYtgLU8OJ32hDjHB7EB4S7BejZ7c01c7b6
```

**Check in Postman:**
```javascript
// Add to test script
const token = pm.environment.get('admin_token');
if (token !== token.trim()) {
    console.log('⚠️  Token has whitespace!');
    pm.environment.set('admin_token', token.trim());
}
```

---

## Advanced: Token Validation

### Validate Token Before Request

```javascript
// Pre-request script
const token = pm.environment.get('admin_token');

if (!token) {
    throw new Error('❌ admin_token not set. Run login first.');
}

if (!token.includes('|')) {
    throw new Error('❌ Invalid token format. Expected: ID|Hash');
}

const [id, hash] = token.split('|');
if (!hash || hash.length < 20) {
    throw new Error('❌ Token hash too short. Token may be corrupted.');
}

console.log('✓ Token valid: ' + id + '|' + hash.substring(0, 20) + '...');
```

### Auto-Refresh Expired Tokens

```javascript
// Pre-request script
const token = pm.environment.get('admin_token');
const tokenCreatedAt = pm.environment.get('admin_token_created_at');
const expirationMinutes = 60;

if (tokenCreatedAt) {
    const ageMinutes = (Date.now() - parseInt(tokenCreatedAt)) / (1000 * 60);
    if (ageMinutes > expirationMinutes - 5) {
        console.log('⚠️  Token expiring soon, refreshing...');
        // Re-run login to get new token
    }
}
```

---

## Configuration

### Check Sanctum Expiration Time

In backend `.env`:
```bash
# Default is 60 minutes if not set
SANCTUM_EXPIRATION=60
```

To change (in minutes):
```bash
SANCTUM_EXPIRATION=120   # 2 hours
SANCTUM_EXPIRATION=1440  # 1 day
```

### Token Abilities

From `AuthController::login()`:
```php
$user->createToken('api-token', ['*'], Carbon::now()->addMinutes(...))
                               └─ Abilities (all '*' = full access)
```

---

## Summary

| Concept | Value | Notes |
|---------|-------|-------|
| **Format** | `ID\|Hash` | Sanctum format with pipe separator |
| **Example** | `2\|bmBOPQ5LbGBIMkJY...` | 2=token ID, rest=hash |
| **Duration** | 60 minutes | Default, configurable in .env |
| **Header** | `Authorization: Bearer {{admin_token}}` | Postman auto-constructs |
| **Storage** | Environment variables | `{{admin_token}}`, `{{doctor_token}}` |
| **Invalid After** | Expiration time | Re-login to refresh |

---

## Related Files

- `postman-nutri-ledger-collection.json` — Updated pre-request script
- `POSTMAN-COLLECTION-AUTH-GUIDE.md` — Full auth setup guide
- `POSTMAN-AUTH-REFERENCE.txt` — Visual reference

---

**Last Updated:** 2026-04-10  
**Sanctum Version:** Laravel 12 default  
**Token Format:** `ID|Hash` (Sanctum standard)
