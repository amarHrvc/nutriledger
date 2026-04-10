# 🔐 Collection-Level Authentication Guide

Updated Postman collection now includes **collection-level Bearer Token authentication**. This means all requests automatically use `{{admin_token}}` without adding headers to each request.

---

## ✅ What Changed

The collection now has:

1. **Bearer Token Auth** (collection level)
   - Type: Bearer Token
   - Token: `{{admin_token}}`
   - Applies to ALL requests in the collection

2. **Pre-Request Script** (collection level)
   - Verifies `base_url` is set
   - Checks if `admin_token` exists
   - Logs debug messages

3. **Removed** individual auth headers from most requests
   - Login requests don't use auth (they create tokens)
   - Other requests inherit collection-level auth

---

## 🚀 How to Use

### Step 1: Import Updated Collection
```
Postman → Import → postman-nutri-ledger-collection.json
```

### Step 2: Run Login Request
```
Click "01 - Login (Admin)" → Send
This populates {{admin_token}} in environment
```

### Step 3: All Other Requests Use Token Automatically
```
Every request now sends:
  Authorization: Bearer {{admin_token}}
  
No need to add headers manually!
```

---

## 📋 How Collection-Level Auth Works

**Authorization Flow:**

```
User runs "02 - Login (Doctor)"
     ↓
Collection sees request needs auth
     ↓
Checks: Is "admin_token" set? (YES, from step 2)
     ↓
Automatically adds header:
  Authorization: Bearer eyJhbGc...
     ↓
Request succeeds with auth
```

**For requests that shouldn't use admin token:**
- Login requests (they don't have a token yet)
- Doctor/patient operations (use their own tokens)

These requests have `auth: { type: "noauth" }` which overrides collection-level auth.

---

## 🔧 Setting Up in Postman UI

### View Collection-Level Auth

```
1. Right-click collection name
2. Click "Edit"
3. Go to "Authorization" tab
4. You'll see:
   Type: Bearer Token
   Token: {{admin_token}}
```

### Change to Different User's Token

To use **doctor token** instead of **admin token**:

```
1. Collection → Edit → Authorization
2. Change Token from {{admin_token}} to {{doctor_token}}
3. All requests now use doctor's token
4. Click "Save"
```

### Switch Between Tokens Mid-Testing

```
// In request test script or pre-request script:
pm.environment.set('admin_token', newTokenValue);

// OR use this at collection level to switch:
1. Edit collection → Authorization
2. Change token variable
3. Save
```

---

## 📝 Request-Level Auth Overrides

Some requests **override** collection-level auth:

### Login Requests (Auth: None)
```json
{
  "auth": {
    "type": "noauth"
  }
}
```
These don't use any auth (they're creating the token)

### Token-Specific Requests (Auth: Bearer)
```json
{
  "auth": {
    "type": "bearer",
    "bearer": [{ "key": "token", "value": "{{doctor_token}}" }]
  }
}
```
These override collection auth with a specific token

---

## 🎯 Usage Scenarios

### Scenario 1: Test as Admin
```
1. Run "01 - Login (Admin)"
2. All requests now use admin_token
3. Create patients, delete visits, etc.
```

### Scenario 2: Test as Doctor
```
1. Run "02 - Login (Doctor)"
2. Change collection auth to {{doctor_token}}
3. All requests now use doctor token
4. Create visits, update visits, etc.
```

### Scenario 3: Test Authorization Failures
```
1. Run "02 - Login (Doctor)"
2. Try "12 - Delete Visit"
3. Collection sends doctor_token
4. Request returns 403 (expected)
```

---

## 🛠️ Troubleshooting

### "Unauthorized" (401) Error

**Cause:** Token is empty or expired

**Fix:**
```
1. Run "01 - Login (Admin)" again
2. Check environment: {{admin_token}} should have a value
3. Retry the request
```

### "Permission Denied" (403) Error

**Expected for:**
- Doctor trying to delete visit
- Patient trying to create visit
- User accessing another user's data

**Not an error** — this is correct authorization behavior!

### Token Not Automatically Included

**Check:**
```
1. Environment dropdown → Select "NutriLabs Local"
2. Collection → Edit → Auth tab
3. Should show: Bearer Token with {{admin_token}}
4. If not, re-import collection
```

---

## 📊 Collection-Level vs Request-Level Auth

| Level | Scope | Override | Use Case |
|-------|-------|----------|----------|
| **Collection** | All requests | Per-request auth config | Default for 90% of requests |
| **Folder** | All requests in folder | Per-request auth config | Group related requests |
| **Request** | Single request | Yes | Special auth (login, guest test) |

---

## 🔄 Dynamic Token Refresh (Advanced)

If tokens expire during long test runs, use collection pre-request script:

```javascript
// In Collection → Pre-request Scripts tab

const token = pm.environment.get('admin_token');
const tokenExpiry = pm.environment.get('admin_token_expiry');
const now = new Date().getTime();

// If token is missing or expired (60 min = 3600000 ms)
if (!token || (tokenExpiry && now > tokenExpiry)) {
    console.log('⚠️  Token expired, refreshing...');
    
    pm.sendRequest({
        url: pm.environment.get('base_url') + '/api/login',
        method: 'POST',
        header: { 'Content-Type': 'application/json' },
        body: {
            mode: 'raw',
            raw: JSON.stringify({
                email: 'admin@nutrilabs.com',
                password: 'password'
            })
        }
    }, function(err, response) {
        if (!err) {
            var jsonData = response.json();
            pm.environment.set('admin_token', jsonData.data.token);
            pm.environment.set('admin_token_expiry', now + 3600000);
            console.log('✓ Token refreshed');
        }
    });
}
```

---

## 📚 Related Documentation

- **Quick Start:** `POSTMAN-QUICKSTART.txt`
- **Setup Guide:** `POSTMAN-SETUP.md`
- **API Reference:** `POSTMAN-COLLECTION-GUIDE.md`

---

## ✨ Summary

**Before:** Each request needed manual `Authorization: Bearer {{admin_token}}` header

**After:** Collection-level auth handles this automatically
```
✓ Import collection
✓ Run login
✓ All requests inherit token
✓ No manual headers needed
✓ Easy to switch users (change token variable)
```

**Updated Files:**
- `postman-nutri-ledger-collection.json` (now includes auth config)
- All other files remain the same

---

**Last Updated:** 2026-04-10  
**Status:** ✅ Collection-Level Auth Enabled
