# nutri-ledger

Private development monorepo for NutriBase — clinical nutrition management platform.

- `backend/` — Laravel 12 REST API
- `frontend/` — React SPA (SD track)
- `_sd/README.md` — SD delivery repo root README (flows through filter-repo as root README)
- `backend/README.md` — Laravel API documentation (delivered to se-origin as `backend/README.md`)

This repo is the **source of truth**. Nothing flows back into it from delivery repos.

---

## Repository Structure

```
nutri-ledger/                  ← private development sandbox (this repo)
├── backend/                   ← Laravel 12 REST API
│   └── README.md              ← Laravel API docs (delivered to se-origin as backend/README.md)
├── frontend/                  ← React SPA
├── _sd/
│   └── README.md              ← SD repo root README (managed here, delivered via filter-repo rename)
└── README.md                  ← this file
```

---

## Branch Strategy

Feature branches are numbered and flow sequentially — each branch is based on the previous:

```
001-bootstrap
002-auth-api              ← branched from 001
003-patient-mgmt-api      ← branched from 002
004-*                     ← branched from 003
...
```

Work happens on feature branches. When a feature is complete it is **squash-merged into `develop`** —
all commits from the feature branch collapse into one clean commit on develop. Feature branches are
deleted after merge.

`develop` is the only branch that delivery repos ever consume.

---

## NL Squash Merge Workflow

This is done inside the main NL repo after finishing work on a feature branch.

```bash
# 1. Switch to develop
git checkout develop

# 2. Squash merge — stages all changes from the feature branch as a single uncommitted diff
git merge --squash 003-patient-mgmt-api

# 3. Commit with a descriptive message — this single commit represents the entire feature
git commit -m "feat(patients): patient management REST API"

# 4. Push develop to NL remote
git push origin develop

# 5. Clean up the feature branch locally and remotely
git branch -D 003-patient-mgmt-api
git push origin --delete 003-patient-mgmt-api
```

After this, trigger the delivery push workflow in the appropriate delivery clone(s).

> **Warning**: never rebase or amend commits already on `develop`. Both SE and SD delivery rely on
> filter-repo determinism — rewriting NL history changes filtered commit hashes and forces a
> `--force-with-lease` push to all delivery remotes to resync.

---

## Delivery Architecture

NL is never pushed to directly from delivery clones. Two separate local delivery clones act as
one-directional pipelines:

```
NL (origin) ──fetch──► SE delivery clone ──filter-repo──► se-origin/be-delivery ──PR──┐
                                                                                        ▼
                                                           colleague FE branch ──PR──► se-origin/develop
                                                           your direct changes  ──PR──► se-origin/develop
                                                                                        │
                                                                               se-origin/develop ──► se-origin/main (deployment, TBD)

NL (origin) ──fetch──► SD delivery clone ──filter-repo────────────────────────────► sd-origin/main
```

### se-origin branch structure

- `develop` — integration branch. All PRs land here: BE from `be-delivery`, FE from colleague, any direct changes.
- `main` (or release branch, name TBD) — deployment/submission branch. Merged from `develop` at milestone. Never committed to directly.

```
se-origin/develop
├── backend/    ← managed via NL delivery pipeline (be-delivery → PR → develop)
└── frontend/   ← managed directly by colleague and/or you (feature branch → PR → develop)
```

### Why separate delivery clones

- NL push is disabled at the remote URL level in both clones — physically impossible to push back
- Fetch is restricted to `main` and `develop` only — feature branch noise never enters delivery clones
- Delivery clones are pipeline tools, not workspaces — never commit to them manually
- For direct work in se-origin (FE or BE), use a separate standard clone of se-origin

---

## Prerequisites — both delivery clones

Install `git-filter-repo` once. Both SE and SD delivery pipelines use it.

```bash
pip install git-filter-repo
```

---

## SE Delivery — Setup (one-time)

SE delivery pipeline delivers `backend/` only to the `be-delivery` branch in se-origin.
filter-repo keeps `backend/` as a subfolder (not promoted to root), so the structure in
`be-delivery` matches se-origin's `backend/` subfolder exactly. Merging `be-delivery` into
`main` cleanly updates `backend/` while leaving `frontend/` untouched.

### 1. Create se-origin on GitHub first

Create an empty repository on GitHub before running any commands below. The initial push
requires the remote to exist.

### 2. Clone NL into a dedicated SE delivery folder

```bash
git clone git@github_ibu:amarHrvc/nutriledger.git nutri-ledger-se-delivery
cd nutri-ledger-se-delivery
```

### 3. Rename origin to upstream

`origin` is the default name after cloning. Renaming makes it explicit that this is a read-only
source, not something you push to.

```bash
git remote rename origin upstream
```

### 4. Restrict fetch to main and develop only

By default, `git fetch` pulls every branch from the remote. Replacing the wildcard refspec with
two explicit ones means only `main` and `develop` ever land in this clone. Feature branches from
NL are invisible here.

```bash
# Replace the default wildcard fetch refspec with main only
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"

# Add develop as a second allowed refspec
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
```

### 5. Disable push to upstream

This makes it physically impossible to push back to NL from this clone. The push URL is set to a
nonsense string — git will refuse the push with a "does not appear to be a git repository" error.

```bash
git remote set-url --push upstream DISABLED
```

### 6. Add se-origin remote

```bash
git remote add se-origin git@github.com:you/nutribase-se.git
```

### 7. Verify all remotes

```bash
git remote show upstream
# Fetch URL: git@github_ibu:amarHrvc/nutriledger.git
# Push  URL: DISABLED
# fetch refspec: +refs/heads/main:refs/remotes/upstream/main
# fetch refspec: +refs/heads/develop:refs/remotes/upstream/develop

git remote -v
# upstream   git@github_ibu:amarHrvc/nutriledger.git (fetch)
# upstream   DISABLED (push)
# se-origin  git@github.com:you/nutribase-se.git (fetch)
# se-origin  git@github.com:you/nutribase-se.git (push)
```

### 8. Initial sync and first push

```bash
git fetch upstream
git checkout -B develop upstream/develop

# filter-repo keeps only backend/ as a subfolder — everything else is stripped
# --force is required because filter-repo refuses to run on a repo with configured remotes
# without it (a safety check) — it does not mean force-push, it means force-run the filter
git filter-repo \
  --path backend/ \
  --force

# filter-repo removes all remote config as a side effect — restore it
git remote add upstream git@github_ibu:amarHrvc/nutriledger.git
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add se-origin git@github.com:you/nutribase-se.git

# First push — establishes be-delivery branch on se-origin
git push se-origin develop:be-delivery
```

### 9. Create PR on GitHub

In se-origin, open a PR from `be-delivery` → `develop`. This PR stays open for the duration of a
milestone. Each subsequent delivery push updates `be-delivery` and the PR reflects the latest
pipeline state automatically.

**Merge strategy**: always use a standard **merge commit** (not squash merge, not rebase) when
merging `be-delivery` into `develop`. Squash and rebase change commit hashes on `develop`, causing
divergence with the next delivery push.

Merge at milestone submission, then promote `develop` → `main` (or the release branch) for deployment.

### 10. Protect branches on se-origin (GitHub settings)

**`develop`** — integration branch, protect with PRs required:
- Require pull request before merging
- No direct pushes allowed

**`main`** (or release branch) — deployment/submission branch:
- Merged from `develop` only at milestone
- No direct pushes, no feature PRs — only receives merges from `develop`

Both BE (via `be-delivery` PR) and FE (via feature branch PRs) flow into `develop` through PRs.

---

## SE Delivery — Push Workflow (after each NL squash merge)

Run this inside `nutri-ledger-se-delivery`, or use the push script below.

```bash
# 1. Save remote URLs before filter-repo removes them
UPSTREAM_URL=$(git remote get-url upstream)
SE_URL=$(git remote get-url se-origin)

# 2. Fetch latest from NL (only main + develop due to restricted refspecs)
git fetch upstream

# 3. Reset local develop to match upstream exactly
#    Restores the full NL tree including the new squash commit
git checkout develop
git reset --hard upstream/develop

# 4. Run filter-repo — rewrites local develop in place, keeping only backend/
#    Old commits produce identical hashes (deterministic), new commit appends
git filter-repo \
  --path backend/ \
  --force

# 5. Restore remote config (filter-repo removes it every time)
git remote add upstream "$UPSTREAM_URL"
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add se-origin "$SE_URL"

# 6. Push to be-delivery — no force needed for normal incremental pushes
#    Old filtered commits already exist in se-origin with identical hashes
#    Only the new commit is sent
git push se-origin develop:be-delivery
```

The open PR (`be-delivery` → `develop`) on GitHub updates automatically. Review and merge at milestone,
then merge `develop` → `main` for deployment/submission.

### Push script

Save as `push-se.sh` in the SE delivery clone root:

```bash
#!/bin/bash
set -e

UPSTREAM_URL=$(git remote get-url upstream)
SE_URL=$(git remote get-url se-origin)

echo "[SE] Fetching upstream (main + develop only)..."
git fetch upstream

echo "[SE] Syncing develop to upstream..."
git checkout develop
git reset --hard upstream/develop

echo "[SE] Running filter-repo (removes remote config as side effect)..."
git filter-repo \
  --path backend/ \
  --force

echo "[SE] Restoring remote config..."
git remote add upstream "$UPSTREAM_URL"
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add se-origin "$SE_URL"

echo "[SE] Pushing to se-origin/be-delivery..."
git push se-origin develop:be-delivery

echo "[SE] Done. Check PR status on se-origin."
```

```bash
chmod +x push-se.sh
./push-se.sh
```

---

## SE Delivery — Force Push Scenarios

A regular push works for all normal incremental deliveries. Force is only needed when filter-repo
rules change — adding or removing paths rewrites all historical commit hashes, making them
incompatible with what se-origin already has.

```bash
# Only use this if filter-repo paths changed
git push se-origin develop:be-delivery --force-with-lease
```

`--force-with-lease` over plain `--force`: before overwriting, it checks that the remote tip
matches what your last fetch saw. If something was pushed to `be-delivery` from another machine
since your last fetch, it refuses instead of silently overwriting.

---

## SE Delivery — Direct Work in se-origin

Both you and the colleague can make direct changes anywhere in se-origin — including `backend/` —
via normal feature branches and PRs. The `be-delivery` + PR design means direct `backend/` changes
are not silently overwritten: if a direct change to `backend/` conflicts with the next NL delivery,
the `be-delivery → main` PR surfaces a merge conflict at review time. Resolve it in the PR by
accepting whichever version is correct.

**You (FE or BE work):** use a separate standard clone — never the delivery clone:

```bash
# One-time: clone se-origin as a normal workspace
# Note: 'origin' here refers to se-origin, not NL
git clone git@github.com:you/nutribase-se.git nutribase-se-workspace
cd nutribase-se-workspace

git checkout -b fix/something
# ... make changes in backend/ or frontend/ ...
git push origin fix/something
# Open PR → develop
```

**Colleague (FE work, or anything else):**

```bash
# Note: 'origin' here refers to se-origin, not NL
git clone git@github.com:you/nutribase-se.git
cd nutribase-se
git checkout -b fe/patient-list
# ... FE work in frontend/ folder ...
git push origin fe/patient-list
# Open PR → develop on GitHub
```

The colleague should put all FE code in a `frontend/` folder at the se-origin root to match the
`backend/` + `frontend/` convention. There is no pre-existing `frontend/` folder — they create and
bootstrap it from scratch in their first PR.

> The delivery clone (`nutri-ledger-se-delivery`) is a pipeline only — never commit to it manually.
> Any work you want in se-origin goes through the workspace clone above.

---

## SD Delivery — Setup (one-time)

SD repo receives `backend/` + `frontend/` + `_sd/README.md` (renamed to root `README.md`).
SD is solo — delivery pushes go directly to `main`, no PR needed.

`_sd/README.md` must exist in NL before the first push. Create it:

```bash
mkdir -p _sd
# write the SD repo README content
touch _sd/README.md
git add _sd/README.md
git commit -m "chore: add SD delivery README"
```

### 1. Create sd-origin on GitHub first

Create an empty repository on GitHub before running any commands below.

### 2. Clone NL into a dedicated SD delivery folder

```bash
git clone git@github_ibu:amarHrvc/nutriledger.git nutri-ledger-sd-delivery
cd nutri-ledger-sd-delivery
```

### 3. Rename origin to upstream

```bash
git remote rename origin upstream
```

### 4. Restrict fetch to main and develop only

```bash
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
```

### 5. Disable push to upstream

```bash
git remote set-url --push upstream DISABLED
```

### 6. Add sd-origin remote

```bash
git remote add sd-origin git@github.com:you/nutribase-sd.git
```

### 7. Verify all remotes

```bash
git remote show upstream
# Fetch URL: git@github_ibu:amarHrvc/nutriledger.git
# Push  URL: DISABLED
# fetch refspec: +refs/heads/main:refs/remotes/upstream/main
# fetch refspec: +refs/heads/develop:refs/remotes/upstream/develop

git remote -v
# upstream   git@github_ibu:amarHrvc/nutriledger.git (fetch)
# upstream   DISABLED (push)
# sd-origin  git@github.com:you/nutribase-sd.git (fetch)
# sd-origin  git@github.com:you/nutribase-sd.git (push)
```

### 8. Initial sync and first push

```bash
git fetch upstream
git checkout -B develop upstream/develop

git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

# Restore remote config after filter-repo removes it
git remote add upstream git@github_ibu:amarHrvc/nutriledger.git
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add sd-origin git@github.com:you/nutribase-sd.git

# First push — establishes history on sd-origin
git push sd-origin develop:main
```

---

## SD Delivery — Push Workflow (after each NL squash merge)

Run this inside `nutri-ledger-sd-delivery`, or use the push script below.

```bash
# 1. Save remote URLs before filter-repo removes them
UPSTREAM_URL=$(git remote get-url upstream)
SD_URL=$(git remote get-url sd-origin)

# 2. Fetch latest from NL
git fetch upstream

# 3. Restore full NL tree — resets local develop to unfiltered upstream state
git checkout develop
git reset --hard upstream/develop

# 4. Run filter-repo
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

# 5. Restore remote config
git remote add upstream "$UPSTREAM_URL"
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add sd-origin "$SD_URL"

# 6. Push — no force needed for normal incremental pushes
git push sd-origin develop:main
```

### Push script

Save as `push-sd.sh` in the SD delivery clone root:

```bash
#!/bin/bash
set -e

UPSTREAM_URL=$(git remote get-url upstream)
SD_URL=$(git remote get-url sd-origin)

echo "[SD] Fetching upstream (main + develop only)..."
git fetch upstream

echo "[SD] Syncing develop to upstream..."
git checkout develop
git reset --hard upstream/develop

echo "[SD] Running filter-repo (removes remote config as side effect)..."
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

echo "[SD] Restoring remote config..."
git remote add upstream "$UPSTREAM_URL"
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git remote set-url --push upstream DISABLED
git remote add sd-origin "$SD_URL"

echo "[SD] Pushing to sd-origin/main..."
git push sd-origin develop:main

echo "[SD] Done."
```

```bash
chmod +x push-sd.sh
./push-sd.sh
```

---

## SD Delivery — Force Push Scenarios

Only needed if filter-repo paths or rename rules change:

```bash
git push sd-origin develop:main --force-with-lease
```

---

## README Management

| File in NL | Appears in delivery repo | Mechanism |
|---|---|---|
| `backend/README.md` | `backend/README.md` in se-origin | filter-repo keeps path as-is |
| `_sd/README.md` | `README.md` (root) in sd-origin | filter-repo path-rename |

Edit these files in NL as normal — they are committed to NL history and delivered with every push.
se-origin's root README is not managed via pipeline — add it directly in se-origin if needed.

---

## NL Protection Summary

| Risk | Protection |
|---|---|
| Accidental push from SE delivery clone to NL | `set-url --push upstream DISABLED` — git refuses at URL level |
| Accidental push from SD delivery clone to NL | `set-url --push upstream DISABLED` — git refuses at URL level |
| Feature branch noise entering delivery clones | Fetch refspecs restricted to `main` + `develop` only |
| Direct commits to se-origin develop bypassing review | `develop` branch protection on se-origin (PRs required) |
| Manual commits inside delivery clones | Convention — delivery clones are pipelines, not workspaces |
| NL history rewrite breaking incremental delivery | Never rebase or amend commits on `develop` — use force push to resync if it happens |

---

## API Documentation & Client Generation (Future)

> Not yet implemented. Revisit after BE stabilises past Group 3 (Visits).

### Overview

The pipeline is: Laravel generates an OpenAPI spec at runtime → Orval reads the spec and generates
typed React hooks and Axios clients → FE components consume the hooks directly.

```
Laravel routes + Form Requests + Resources
        ↓  Scramble reads at runtime
/docs/api.json  (OpenAPI 3.1 spec, live endpoint)
        ↓  Orval reads spec URL or exported file
frontend/src/api/generated/  (TS types, React Query hooks, Axios clients)
        ↓
React components use generated hooks (useGetPatients, useCreatePatient, etc.)
```

---

### BE — Scramble (dedoc/scramble)

Zero-annotation approach. Scramble infers the OpenAPI spec automatically from:
- Route definitions (`apiResource`, named routes, middleware groups)
- Form Request `rules()` → request body schema and validation constraints
- Eloquent Resources (`toArray()`) → response body schema
- PHP return types and PHPDoc where present

No annotations need to be added to existing controllers or resources. The spec is served live
at `/docs/api.json` and a Swagger UI at `/docs/api` by default.

**Install:**

```bash
cd backend
composer require dedoc/scramble
php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag=scramble-config
```

**Config** (`config/scramble.php`) — things worth configuring:
- `api_path` — prefix for your API routes (default `api`)
- `api_domain` — if your API is on a subdomain
- `info.title` / `info.version` — appear in generated docs
- Exclude internal/test routes from the spec

**Auth** — Scramble needs to know about Sanctum so it documents auth correctly:

```php
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

// AppServiceProvider::boot()
Scramble::extendOpenApi(function (OpenApi $openApi) {
    $openApi->secure(
        SecurityScheme::http('bearer')
    );
});
```

---

### FE — Orval

Orval reads the OpenAPI spec (URL or exported file) and generates:
- **TypeScript interfaces** for all request/response schemas
- **React Query hooks** (`useQuery`, `useMutation`) per endpoint
- **Axios instances** pre-configured with base URL and interceptors
- Optionally **Zod schemas** for runtime response validation

**Install:**

```bash
cd frontend
npm install orval --save-dev
npm install @tanstack/react-query axios
```

**Config** (`orval.config.ts` in `frontend/`):

```ts
import { defineConfig } from 'orval';

export default defineConfig({
  nutribase: {
    input: {
      // Live URL — requires backend running locally
      target: 'http://localhost:8000/docs/api.json',
      // Exported file — run: php artisan scramble:export first
      // target: '../backend/storage/app/api.json',
    },
    output: {
      target: './src/api/generated',
      client: 'react-query',
      mode: 'tags-split',        // one file per API tag (patients, users, auth)
      mock: false,
    },
    hooks: {
      afterAllFilesWrite: 'prettier --write',
    },
  },
});
```

**Generate:**

```bash
npx orval
# regenerate any time the BE spec changes
```

Output in `frontend/src/api/generated/`:
- `patients.ts` — `useGetPatients()`, `useGetPatient(id)`, `useCreatePatient()`, etc.
- `users.ts` — user management hooks
- `auth.ts` — login/logout/me hooks
- `model/` — all TypeScript interfaces

**Usage in a component:**

```tsx
import { useGetPatients } from '@/api/generated/patients';

export function PatientList() {
  const { data, isLoading } = useGetPatients();
  // data is fully typed — PatientResource shape from BE
}
```

---

### Regeneration Workflow

Whenever BE routes, Form Requests, or Resources change:

1. Scramble picks up changes automatically (no rebuild — spec is generated at runtime)
2. Run `npx orval` in `frontend/` to regenerate hooks and types
3. TypeScript compiler immediately surfaces any breaking changes in components

Add to `package.json`:

```json
{
  "scripts": {
    "api:generate": "orval"
  }
}
```

---

### Alternative Tools

| Tool | Role | Notes |
|---|---|---|
| Scribe (`knuckleswtf/scribe`) | BE spec generation | More mature than Scramble, supports annotations, also outputs HTML docs. More setup required. |
| Hey API | FE client generation | Newer alternative to Orval, cleaner config, less community adoption currently. |
| openapi-typescript + openapi-fetch | FE client generation | Lightweight — generates types only, no hooks. Manual query setup required. |

---

## Appendix — be-delivery Branch Access Control (Future Consideration)

> Not implemented. Worth revisiting if stricter pipeline enforcement is needed.

By default, anyone with write access to se-origin can push to `be-delivery`. The following options
restrict pushes to `be-delivery` to the delivery pipeline only, making it truly read-only for humans.

### Option A — Dedicated bot account

1. Create a separate GitHub account (e.g. `nutribase-bot`) and add it as a collaborator on se-origin
2. Branch protection on `be-delivery` → **Restrict who can push** → allow only `nutribase-bot`
3. Configure the SE delivery clone's se-origin remote to authenticate as the bot via its SSH key or PAT
4. Neither you nor the colleague can push to `be-delivery` with personal accounts — only the delivery clone authenticating as the bot

**Trade-off**: requires maintaining a second GitHub account and its credentials in the delivery clone.

### Option B — GitHub Actions (fully automated)

Move the delivery push out of the local delivery clone and into a GitHub Actions workflow on NL:

1. Workflow triggers on push to NL `develop`
2. Runs filter-repo + pushes to se-origin `be-delivery` using a stored secret (PAT or deploy key)
3. Branch protection on `be-delivery` → allow only `github-actions[bot]`

Neither you nor the colleague can push to `be-delivery` at all. The pipeline becomes fully automated
and triggered by NL activity rather than run manually.

**Trade-off**: more setup. Requires NL to be hosted where GitHub Actions are available. Local delivery
clone becomes unnecessary for SE BE delivery.

### Option C — Admin bypass (pragmatic, imperfect)

Branch protection on `be-delivery` with no explicit bypass. As repo owner you can override
protections as admin when running the delivery push. The colleague (non-admin collaborator) cannot
push to `be-delivery` at all.

**Trade-off**: does not protect against accidental direct pushes by you. Simplest to set up.

### Recommendation

| Scenario | Option |
|---|---|
| Block colleague only, trust yourself | C — admin bypass |
| Block everyone including yourself | A — bot account |
| Full automation, no manual delivery steps | B — GitHub Actions |
