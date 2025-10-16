# Agent Pre-Flight Checks

**MANDATORY: Every agent task must perform these checks BEFORE starting work.**

These checks prevent race conditions when multiple agents work on related tasks in parallel.

## Pre-Flight Checklist

### 1. Code Existence Check
Before implementing any feature, verify the target code doesn't already exist:

**For Form Requests:**
```bash
if [ -f "app/Http/Requests/Api/{RequestName}.php" ]; then
  echo "ERROR: {RequestName} already exists at {commit/location}"
  exit 1
fi
```

**For Resources:**
```bash
if [ -f "app/Http/Resources/{ResourceName}.php" ]; then
  echo "ERROR: {ResourceName} already exists at {commit/location}"
  exit 1
fi
```

**For Policies:**
```bash
if [ -f "app/Policies/{PolicyName}.php" ]; then
  echo "ERROR: {PolicyName} already exists — check if already implemented"
  exit 1
fi
```

**For Methods in existing files (sanctum.php, AuthController, etc):**
```bash
# Check if target line/method already exists
grep -n "SEARCH_PATTERN" file.php
# If found: "ERROR: Already implemented at line X in {file}"
# If not found: Safe to proceed
```

### 2. Git Status Check
```bash
git status --short
# If changes exist for this task's files:
# ERROR: Changes already staged/committed. Resolve and retry.
```

### 3. Recent Commit Check
```bash
git log --oneline -10 | grep -i "{TASK_NAME_PATTERN}"
# If found: Check if changes are already implemented
# Report which commit and what was done
```

## What to Do If Code Already Exists

**STOP immediately and report:**
```
⚠️ RACE CONDITION DETECTED

Task: {TASK_ID} - {TASK_NAME}
Target File: {file_path}
Status: Already implemented
Found In: {commit_hash} - {commit_message}
Last Modified: {commit_date}

Action: Task already complete. Skipping implementation.
Notify: User should close issue or consolidate commits if necessary.
```

## Workflow When Pre-Flight Passes

Only proceed with TDD workflow if:
- ✅ Target code does NOT exist
- ✅ No conflicting changes staged
- ✅ Task hasn't been completed in recent commits

Then execute:
1. Write tests (RED)
2. Implement (GREEN)
3. Run tests
4. Pint format
5. Larastan analyze
6. Clean cache
7. Commit

## Example: Task-Specific Pre-Flight

### For Form Requests (AUTH-07, AUTH-08, AUTH-09)
```bash
# Check if request file exists
if [ -f "app/Http/Requests/Api/RegisterRequest.php" ]; then
  # Already exists — notify user
  exit 1
fi

# Check for method signatures in AuthController
if grep -q "public function login" app/Http/Controllers/Api/AuthController.php; then
  # Check if it uses the new sanctum config
  if grep -q "config('sanctum.expiration')" app/Http/Controllers/Api/AuthController.php; then
    # Already implemented — notify
    exit 1
  fi
fi
```

### For Config Changes (AUTH-02, AUTH-03)
```bash
# Check if sanctum.php has env() for expiration
if grep -q "env('SANCTUM_EXPIRATION'" config/sanctum.php; then
  # Already done — notify
  exit 1
fi

# Check if AppServiceProvider has rate limiter
if grep -q "RateLimiter::for('login'" app/Providers/AppServiceProvider.php; then
  # Already done — notify
  exit 1
fi
```

## Implementation in Agent Prompts

Add this section to the top of all agent task prompts:

```
**PRE-FLIGHT CHECKS (DO THIS FIRST):**
1. Verify target file(s) don't already exist
2. Check recent commits for this feature
3. Run `git status --short` to confirm clean state
4. If code already exists: REPORT race condition and STOP
5. Only proceed if all checks pass

**Report format if issue found:**
⚠️ RACE CONDITION: {task_id} code already at {commit}
Notify user for manual resolution.
```

---

**Why this matters:**
- Prevents duplicate work from parallel agents
- Catches stale task specs early
- Keeps commit history clean
- Saves execution time
