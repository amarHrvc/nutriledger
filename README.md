# nutri-ledger

Private development monorepo for NutriBase — clinical nutrition management platform.

- `backend/` — Laravel 12 REST API
- `frontend/` — React SPA (SD track)
- `_sd/README.md` — SD delivery repo README (flows through filter-repo as root README)
- `backend/README.md` — SE delivery repo README (flows through subtree split as root README)

This repo is the **source of truth**. Nothing flows back into it from delivery repos.

---

## Repository Structure

```
nutri-ledger/                  ← private development sandbox (this repo)
├── backend/                   ← Laravel 12 REST API
├── frontend/                  ← React SPA
├── _sd/
│   └── README.md              ← SD repo root README (managed here, delivered via filter-repo)
├── backend/README.md          ← SE repo root README (managed here, delivered via subtree split)
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

Work happens on feature branches. When a feature is complete it is **squash-merged into `develop`** — all commits from the feature branch collapse into one clean commit on develop. Feature branches are deleted after merge.

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

---

## Delivery Architecture

NL is never pushed to directly from delivery clones. Two separate local delivery clones act as
one-directional pipelines:

```
NL (origin) ──fetch──► SE delivery clone ──subtree split──► se-origin/be-delivery ──PR──► se-origin/main
                                                                                    ↑
                                                             colleague FE branch ───┘

NL (origin) ──fetch──► SD delivery clone ──filter-repo────► sd-origin/main
```

### Why separate delivery clones

- NL push is disabled at the remote URL level in both clones — physically impossible to push back
- Fetch is restricted to `main` and `develop` only — feature branch noise never enters delivery clones
- Delivery clones are pipeline tools, not workspaces — never commit to them manually
- If you need to do manual work in se-origin (e.g. FE contribution), use a separate standard clone
  of se-origin — completely independent from the delivery clone

---

## SE Delivery — Setup (one-time)

SE repo receives `backend/` only. The subtree split promotes `backend/` content to the repo root,
so se-origin looks like a plain Laravel project. The colleague adds their FE directly to se-origin
without ever touching NL.

### 1. Clone NL into a dedicated SE delivery folder

```bash
git clone git@github_ibu:amarHrvc/nutriledger.git nutri-ledger-se-delivery
cd nutri-ledger-se-delivery
```

### 2. Rename origin to upstream

`origin` is the default name after cloning. Renaming makes it explicit that this is a read-only
source, not something you push to.

```bash
git remote rename origin upstream
```

### 3. Restrict fetch to main and develop only

By default, `git fetch` pulls every branch from the remote. Replacing the wildcard refspec with
two explicit ones means only `main` and `develop` ever land in this clone. Feature branches from
NL are invisible here.

```bash
# Replace the default wildcard fetch refspec with main only
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"

# Add develop as a second allowed refspec
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
```

Verify the result — you should see exactly two fetch lines for upstream:

```bash
git remote -v show upstream
# fetch = +refs/heads/main:refs/remotes/upstream/main
# fetch = +refs/heads/develop:refs/remotes/upstream/develop
```

### 4. Disable push to upstream

This makes it physically impossible to push back to NL from this clone. The push URL is set to a
nonsense string — git will refuse the push with a "does not appear to be a git repository" error.

```bash
git remote set-url --push upstream DISABLED
```

### 5. Add se-origin remote

```bash
git remote add se-origin git@github.com:you/nutribase-se.git
```

### 6. Verify all remotes

```bash
git remote -v
# upstream   git@github_ibu:amarHrvc/nutriledger.git (fetch)
# upstream   DISABLED (push)
# se-origin  git@github.com:you/nutribase-se.git (fetch)
# se-origin  git@github.com:you/nutribase-se.git (push)
```

### 7. Initial sync and first push

```bash
git fetch upstream
git checkout -B develop upstream/develop

# Subtree split — extracts all commits that touched backend/ and creates a new branch
# where backend/ content is promoted to the repo root
git subtree split --prefix=backend -b subtree/se-backend

# Push the split branch to se-origin as be-delivery (not main — main is protected)
git push se-origin subtree/se-backend:be-delivery --set-upstream
```

### 8. Create PR on GitHub

In se-origin, open a PR from `be-delivery` → `main`. This PR stays open for the duration of a
milestone. Each subsequent delivery push updates the `be-delivery` branch and the PR reflects the
latest BE state automatically. Merge the PR at milestone submission.

### 9. Protect main on se-origin (GitHub settings)

In se-origin repository settings → Branches → Add rule for `main`:
- Require pull request before merging
- No direct pushes allowed

This ensures neither you nor the colleague can accidentally commit BE changes directly to main,
bypassing the delivery pipeline.

---

## SE Delivery — Push Workflow (after each NL squash merge)

Run this inside `nutri-ledger-se-delivery`.

```bash
# 1. Fetch latest from NL (only main + develop due to restricted refspecs)
git fetch upstream

# 2. Reset local develop to match upstream exactly
#    This restores the full NL tree including the new squash commit
git checkout develop
git reset --hard upstream/develop

# 3. Delete stale split branch and recreate
#    subtree split is deterministic — old commits produce identical hashes,
#    only the new squash commit produces a new hash that appends to be-delivery
git branch -D subtree/se-backend 2>/dev/null || true
git subtree split --prefix=backend -b subtree/se-backend

# 4. Push to be-delivery — no force needed for normal incremental pushes
#    Old filtered commits already exist in se-origin with identical hashes
#    Only the new commit is sent
git push se-origin subtree/se-backend:be-delivery
```

The open PR on GitHub updates automatically. Review and merge at milestone.

### Push script

Save as `push-se.sh` in the delivery clone root for convenience:

```bash
#!/bin/bash
set -e

echo "[SE] Fetching upstream (main + develop only)..."
git fetch upstream

echo "[SE] Syncing develop to upstream..."
git checkout develop
git reset --hard upstream/develop

echo "[SE] Running subtree split on backend/..."
git branch -D subtree/se-backend 2>/dev/null || true
git subtree split --prefix=backend -b subtree/se-backend

echo "[SE] Pushing to se-origin/be-delivery..."
git push se-origin subtree/se-backend:be-delivery

echo "[SE] Done. Check PR status on se-origin."
```

```bash
chmod +x push-se.sh
./push-se.sh
```

---

## SE Delivery — Force Push Scenarios

A regular push works for all normal incremental deliveries. Force is only needed when the filter
rules themselves change — adding or removing paths from the subtree split prefix would rewrite all
historical commit hashes, making them incompatible with what se-origin already has.

```bash
# Only use this if you changed the split prefix or rules
git push se-origin subtree/se-backend:be-delivery --force-with-lease
```

`--force-with-lease` over plain `--force`: before overwriting, it checks that the remote tip
matches what your last fetch saw. If someone pushed to be-delivery from another machine since your
last fetch, it refuses instead of silently overwriting. Safe habit even in a solo delivery context.

---

## SE Delivery — Colleague Workflow ***

The colleague clones se-origin directly — they never interact with NL.

```bash
git clone git@github.com:you/nutribase-se.git
cd nutribase-se
git checkout -b fe/patient-list
# ... FE work in frontend/ folder ...
git push origin fe/patient-list
# Open PR → main on GitHub
```

Their FE commits and your BE delivery commits both flow into se-origin/main via PRs. The two
streams are fully independent.

**Critical convention**: the colleague must never commit to `backend/` in se-origin. The next BE
delivery push would overwrite those changes because they are not in NL. If a BE fix is discovered
during FE work, it must be ported to NL first, squash-merged to develop, then delivered through
the pipeline.

---

## SE Delivery — Manual FE Work by You

If you need to contribute to FE in se-origin yourself, use a separate standard clone — completely
independent from the delivery clone:

```bash
# One-time: clone se-origin as a normal workspace
git clone git@github.com:you/nutribase-se.git nutribase-se-workspace
cd nutribase-se-workspace

# Normal git workflow — feature branch, PR to main
git checkout -b fe/fix-something
# ... make changes ...
git push origin fe/fix-something
# Open PR → main
```

Never commit manually inside `nutri-ledger-se-delivery`. It is a pipeline, not a workspace.

---

## SD Delivery — Setup (one-time)

SD repo receives `backend/` + `frontend/` + `_sd/README.md` (renamed to `README.md`). Uses
`git filter-repo` instead of subtree split because multiple paths are involved. SD is solo so
main is not protected — delivery pushes go directly to main.

### Prerequisites

Install `git-filter-repo` once:

```bash
pip install git-filter-repo
# or on Windows: winget install git-filter-repo
```

### 1. Clone NL into a dedicated SD delivery folder

```bash
git clone git@github_ibu:amarHrvc/nutriledger.git nutri-ledger-sd-delivery
cd nutri-ledger-sd-delivery
```

### 2. Rename origin to upstream

```bash
git remote rename origin upstream
```

### 3. Restrict fetch to main and develop only

Same reasoning as SE — only the branches you care about are fetchable.

```bash
git config remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"
git config --add remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
```

### 4. Disable push to upstream

```bash
git remote set-url --push upstream DISABLED
```

### 5. Add sd-origin remote

```bash
git remote add sd-origin git@github.com:you/nutribase-sd.git
```

### 6. Verify all remotes

```bash
git remote -v
# upstream   git@github_ibu:amarHrvc/nutriledger.git (fetch)
# upstream   DISABLED (push)
# sd-origin  git@github.com:you/nutribase-sd.git (fetch)
# sd-origin  git@github.com:you/nutribase-sd.git (push)
```

### 7. Initial sync and first push

```bash
git fetch upstream
git checkout -B develop upstream/develop

# filter-repo strips everything except the listed paths and renames _sd/README.md to README.md
# --force is required because filter-repo refuses to run on a repo with a configured remote
# without it (safety check) — it does not mean force-push, it means force-run
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

# First push — establishes history on sd-origin
git push sd-origin develop:main
```

---

## SD Delivery — Push Workflow (after each NL squash merge)

Run this inside `nutri-ledger-sd-delivery`.

```bash
# 1. Fetch latest from NL
git fetch upstream

# 2. Restore full NL tree — this undoes the previous filter-repo rewrite
#    and brings in the new squash commit from develop
git checkout develop
git reset --hard upstream/develop

# 3. Run filter-repo — rewrites the local develop history in place
#    Old commits produce identical hashes (deterministic), new commit appends
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

# 4. Push — no force needed for normal incremental pushes
git push sd-origin develop:main
```

### Push script

Save as `push-sd.sh` in the delivery clone root:

```bash
#!/bin/bash
set -e

echo "[SD] Fetching upstream (main + develop only)..."
git fetch upstream

echo "[SD] Syncing develop to upstream..."
git checkout develop
git reset --hard upstream/develop

echo "[SD] Running filter-repo..."
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

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

Same rule as SE — only needed if filter paths or rename rules change:

```bash
git push sd-origin develop:main --force-with-lease
```

---

## README Management

Both delivery repos get a managed README that lives in NL and flows through automatically.

| File in NL | Appears as in delivery repo | Mechanism |
|---|---|---|
| `backend/README.md` | `README.md` in se-origin | subtree split promotes backend/ to root |
| `_sd/README.md` | `README.md` in sd-origin | filter-repo path-rename |

Edit these files in NL as normal. They are committed to NL history and delivered with every push.
Never edit README directly in se-origin or sd-origin — it will be overwritten on next delivery.

---

## NL Protection Summary

| Risk | Protection |
|---|---|
| Accidental push from SE delivery clone to NL | `set-url --push upstream DISABLED` — git refuses at URL level |
| Accidental push from SD delivery clone to NL | `set-url --push upstream DISABLED` — git refuses at URL level |
| Feature branch noise entering delivery clones | Fetch refspecs restricted to `main` + `develop` only |
| Direct BE commits to se-origin bypassing pipeline | `main` branch protection on se-origin (PRs required) |
| Manual commits inside delivery clones | Convention — delivery clones are pipelines, not workspaces |
| Colleague BE changes in se-origin lost on next push | Branch protection + code review on PRs catches this |

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
        ↓  Orval reads spec URL or local file
frontend/src/api/  (generated TS types, React Query hooks, Axios clients)
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
// AppServiceProvider::boot()
Scramble::extendOpenApi(function (OpenApi $openApi) {
    $openApi->secure(
        SecurityScheme::http('bearer')
    );
});
```

---

### FE — Orval

Orval reads the OpenAPI spec (URL or local file) and generates:
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
      target: 'http://localhost:8000/docs/api.json',  // Scramble live endpoint
      // or: target: '../backend/docs/api.json'       // exported local file
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

1. Scramble picks up changes automatically (no rebuild needed — spec is generated at runtime)
2. Run `npx orval` in `frontend/` to regenerate hooks and types
3. TypeScript compiler immediately surfaces any breaking changes in components

Optionally add orval to a `package.json` script:

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
| openapi-typescript + openapi-fetch | FE client generation | Lightweight alternative — generates types only, no hooks. Manual query setup required. |
