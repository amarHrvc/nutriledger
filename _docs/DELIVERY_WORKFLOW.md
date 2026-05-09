# Delivery Workflow — SE and SD Track Publishing

**Last updated**: 2026-04-19
**Status**: Partially implemented (SD delivery clone run successfully; SE delivery clone setup pending)

---

## Overview

NutriLedger (`nutri-ledger`) is a **private monorepo** (NL) — your personal sandbox where all
development happens. Work done here is never submitted anywhere directly. Instead, two separate
**delivery repos** on GitHub receive filtered snapshots of NL on demand:

| Track | Destination repo | Paths included | Push style |
|---|---|---|---|
| SE | `se-origin` (`nutribase-se`) | `backend/` + `frontend/` | `be-delivery` branch → PR → `main` |
| SD | `sd-origin` (`nutri-ledger` public) | `backend/` + `frontend/` + `_sd/README.md` as root `README.md` | Direct push → `sd-origin/main` |

```
NL (nutri-ledger — private sandbox)
  └── develop ──► SE delivery clone ──filter-repo──► se-origin/be-delivery ──PR──► se-origin/main
              ──► SD delivery clone ──filter-repo──► sd-origin/main (direct)
```

---

## Concepts

### Squash merge workflow (inside NL)

All feature work in NL follows this pattern:

1. Branch from `develop`: `git checkout -b feature/xxx develop`
2. Implement + commit (TDD cycles, bd issue references, etc.)
3. When complete, squash merge back to `develop`:
   ```bash
   git checkout develop
   git merge feature/xxx --squash
   git commit -m "feat(scope): short description (TASK-ID)"
   git branch -d feature/xxx
   ```

Each squash commit on `develop` becomes **one delivery commit** in both SE and SD repos.
This keeps the public repos clean — no intermediate TDD micro-commits, no `(RED)/(GREEN)/(REFACTOR)` noise.

### Delivery clones

A **delivery clone** is a separate local folder cloned from NL, used exclusively as a pipeline.
You never do development work in it. It has:

- `upstream` remote pointing at NL (fetch only, restricted to `develop` and `main`)
- Push to `upstream` disabled (safety — prevents accidents)
- `se-origin` or `sd-origin` remote pointing at the public GitHub repo

There are **two delivery clones** on your machine:

| Clone folder | Purpose |
|---|---|
| `nutri-ledger-se-delivery/` | Publishes `backend/` to SE repo |
| `nutri-ledger-sd-delivery/` | Publishes `backend/` + `frontend/` to SD repo |

### Why `git filter-repo`

`git filter-repo` rewrites a repo's history keeping only the specified paths. Key properties:

- **Deterministic**: same input commits + same filter rules = identical output commit hashes every run
- This means after the initial push, subsequent delivery runs produce commits that extend the history cleanly — no `--force` needed on incremental pushes
- **Remote handling**: filter-repo removes remotes named `origin` as a safety measure. Since NL remotes are named `upstream` (not `origin`), they survive the rewrite unchanged

Install once:
```bash
pip install git-filter-repo
# Windows alternative: winget install git-filter-repo
```

---

## SD Delivery Setup (one-time)

### 1. Create the delivery clone

```bash
# From a folder outside NL, e.g. D:\_Learn\_PhpstormProjects\
git clone git@github.com:amarHrvc/nutri-ledger.git nutri-ledger-sd-delivery
cd nutri-ledger-sd-delivery
```

### 2. Configure remotes

```bash
# Rename origin → upstream
git remote rename origin upstream

# Restrict upstream fetch to develop and main only (prevents accidental branch pollution)
git config remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git config --add remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"

# Disable push to upstream (safety — this clone is read-only from NL's perspective)
git remote set-url --push upstream DISABLED

# Add sd-origin pointing at the public SD GitHub repo
git remote add sd-origin git@github.com:amarHrvc/nutri-ledger.git   # replace with actual SD repo URL
```

Verify:
```bash
git remote -v
# upstream  git@github.com:amarHrvc/nutri-ledger.git (fetch)
# upstream  DISABLED (push)
# sd-origin git@github.com:... (fetch)
# sd-origin git@github.com:... (push)
```

### 3. Verify filter-repo is installed

```bash
git filter-repo --version
```

---

## SD Delivery — Routine Push

Run this after each squash merge to `develop` in NL:

```bash
cd nutri-ledger-sd-delivery

# 1. Sync to latest develop from NL
git fetch upstream
git checkout develop
git reset --hard upstream/develop

# 2. Filter: keep backend/, frontend/, and _sd/README.md (renamed to root README.md)
# Remove already_ran so filter-repo treats this as a fresh run (required for repeated deliveries)
rm -f .git/filter-repo/already_ran
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --path .github/workflows/ \
  --path _sd/README.md \
  --path-rename _sd/README.md:README.md \
  --force

# 3. Push to SD repo
git push sd-origin develop:main
```

**Notes:**
- `--path-rename _sd/README.md:README.md` moves the SD-specific README to the root of the SD repo
- After filter-repo, `upstream` and `sd-origin` remotes are still present (they survive because they're not named `origin`)
- First push will create the branch on sd-origin; subsequent runs push incremental commits

---

## SE Delivery Setup (one-time)

SE is collaborative — your colleague works on `frontend/` in `se-origin` directly. The delivery
flow keeps BE and FE changes from overwriting each other via a dedicated `be-delivery` branch.

### 1. Create the delivery clone

```bash
# From a folder outside NL
git clone git@github.com:amarHrvc/nutri-ledger.git nutri-ledger-se-delivery
cd nutri-ledger-se-delivery
```

### 2. Configure remotes

```bash
# Rename origin → upstream
git remote rename origin upstream

# Restrict fetch to develop and main only
git config remote.upstream.fetch "+refs/heads/develop:refs/remotes/upstream/develop"
git config --add remote.upstream.fetch "+refs/heads/main:refs/remotes/upstream/main"

# Disable push to upstream
git remote set-url --push upstream DISABLED

# Add se-origin pointing at the SE GitHub repo
git remote add se-origin git@github.com:amarHrvc/nutribase-se.git   # replace with actual SE repo URL
```

### 3. Configure se-origin branch protection (GitHub UI)

In the `nutribase-se` GitHub repo settings:
- Protect `main` — require pull requests, no direct pushes
- `be-delivery` branch is **not** protected — delivery clone pushes here freely

---

## SE Delivery — Routine Push

### Full delivery (all of develop)

```bash
cd nutri-ledger-se-delivery

# 1. Sync to latest develop from NL
git fetch upstream
git checkout develop
git reset --hard upstream/develop

# 2. Filter: keep backend/ only — colleague owns frontend/ in se-origin directly
rm -f .git/filter-repo/already_ran
git filter-repo \
  --path backend/ \
  --path frontend/ \
  --force

# 3. Push to be-delivery branch in SE repo
git push se-origin develop:be-delivery --force-with-lease

# 4. Open a PR on GitHub: be-delivery → main
```

### Partial delivery (up to a specific commit)

Use this when you want to deliver only up to a certain point in `develop` — e.g. the first
three squash commits, or everything before an unfinished feature.

```bash
cd nutri-ledger-se-delivery

# 1. Inspect the commit log to find your target
git fetch upstream
git log upstream/develop --oneline
# e.g.:
#   a1b2c3d feat(visits): VS-3 implement visit store
#   e4f5g6h feat(patients): PS-12 add patient search
#   i7j8k9l feat(auth): bootstrap Sanctum auth

# 2. Reset to the target commit (inclusive — this commit WILL be delivered)
git checkout develop
git reset --hard <commit-hash>
# e.g.: git reset --hard e4f5g6h  → delivers auth + patients, excludes visits

# 3. Filter and push (same as full delivery)
git filter-repo \
  --path backend/ \
  --force

git push se-origin develop:be-delivery --force-with-lease

# 4. Open a PR on GitHub: be-delivery → main
```

**Note:** because filter-repo is deterministic, the commit hashes it produces for the
delivered range will be identical to those in a full delivery run that covers the same
commits. Future full deliveries extend that history cleanly — no conflicts, no force-push.

**Why `--force-with-lease` for SE but not SD?**
SD is solo — you own both ends. SE is collaborative — `be-delivery` could have diverged if
something went wrong. `--force-with-lease` is a safety net: it refuses to push if the remote
has commits you haven't seen, preventing silent overwrites.

**Why a PR instead of direct push to main?**
Your colleague pushes FE changes to `se-origin/main` (via their own PRs). Your BE delivery
pushes to `se-origin/be-delivery`. The PR from `be-delivery` → `main` is where the two streams
merge. If BE and FE touched the same file, the conflict surfaces in the PR review — the correct
place to resolve it.

---

## Repository Structure in Delivery Repos

After filter-repo, the repos have the following structure:

**SD repo** (`sd-origin`) — full stack delivered from NL:
```
sd-origin root
├── backend/          ← Laravel 12 REST API
│   ├── app/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   ├── composer.json
│   └── ...
├── frontend/         ← React SPA
│   ├── src/
│   ├── package.json
│   └── ...
└── README.md         ← SD only: sourced from _sd/README.md in NL
                        SE: backend/README.md serves as the BE README
└── README.md         ← sourced from _sd/README.md in NL
```

**SE repo** (`se-origin`) — backend only delivered from NL; frontend owned by colleague:
```
se-origin root
├── backend/          ← Laravel 12 REST API (delivered via be-delivery → PR → main)
└── frontend/         ← React SPA (colleague's domain — NOT delivered from NL)
```

Note: all NL-internal paths (`_docs/`, `_bckp/`, `specs/`, `_sd/`, `memory-bank/`, etc.)
are stripped by filter-repo and never appear in delivery repos.

---

## Collaboration Model (SE)

```
You (NL) ──squash──► develop ──filter-repo──► se-origin/be-delivery ──PR──► se-origin/main
                                                                                    ▲
                                              colleague ──feature branches──────────┘
```

- **You** never push directly to `se-origin/main`
- **Colleague** clones `se-origin` directly, branches from `main`, PRs back to `main`
- **Conflicts** between your BE delivery and colleague's FE work are resolved in the PR
- Colleague must not commit to `backend/` in se-origin — if they do, it will conflict with
  your next delivery PR (surfaced cleanly, not silently overwritten)

If you need to contribute FE work directly in se-origin (one-off fix, not via NL):
```bash
# Standard clone of se-origin, separate from delivery clone
git clone git@github.com:amarHrvc/nutribase-se.git nutribase-se-work
git checkout -b fix/fe-something
# ... make changes ...
git push origin fix/fe-something
# open PR to main
```

---

## README Management

Both delivery repos have a root `README.md`. These live in NL and flow through delivery:

| Repo | README source in NL | How it gets there |
|---|---|---|
| SD | `_sd/README.md` | filter-repo `--path-rename _sd/README.md:README.md` |
| SE | `backend/README.md` | present at `backend/README.md` after filter-repo (subfolder preserved) |

**Rule**: never edit README files inside the delivery clones or delivery repos directly.
Always edit in NL, then deliver. Edits in delivery repos are overwritten on the next push.

---

## NL Squash Merge — Step by Step

Complete workflow for finishing a feature and delivering it:

```bash
# 1. Finish feature work on feature branch in NL
git checkout develop

# 2. Squash merge
git merge feature/xxx --squash
git commit -m "feat(scope): descriptive message (TASK-ID)
Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"

# 3. Delete feature branch
git branch -d feature/xxx

# 4. Deliver to SD
cd ../nutri-ledger-sd-delivery
git fetch upstream
git reset --hard upstream/develop
rm -f .git/filter-repo/already_ran
git filter-repo --path backend/ --path frontend/ --path .github/workflows/ --path _sd/README.md --path-rename _sd/README.md:README.md --force
git push sd-origin develop:main

# 5. Deliver to SE (backend only — colleague owns frontend in se-origin)
cd ../nutri-ledger-se-delivery
git fetch upstream
git reset --hard upstream/develop
rm -f .git/filter-repo/already_ran
git filter-repo --path backend/ --force
git push se-origin develop:be-delivery --force-with-lease
# then open PR on GitHub: be-delivery → main

# 6. Return to NL
cd ../nutri-ledger
```

---

## Troubleshooting

### `AssertionError` or "already_ran" prompt from filter-repo

Cause: `.git/filter-repo/already_ran` exists from a previous run. Answering Y triggers
continuation mode which hits an internal assertion (`usoa == intermediate`) when the repo
was reset with `git reset --hard` between runs.

Fix: delete `already_ran` before each filter-repo invocation (already included in the commands above):

```bash
rm -f .git/filter-repo/already_ran
git filter-repo --path backend/ ... --force
```

### `git filter-repo` can't find remote after running

This happens if a remote is named `origin` — filter-repo removes it as a safety measure.
Workaround: rename your NL remote to `upstream` during delivery clone setup (step 2 above).
Your setup already does this, so this should not occur.

If it does happen anyway, re-add the remote manually:
```bash
git remote add upstream <NL-url>
git remote set-url --push upstream DISABLED
git remote add sd-origin <sd-url>
```

### `git push` rejected on SD

SD should never need `--force`. If rejected, the delivery clone's history has diverged from
sd-origin. This means filter-repo produced different hashes — usually because the input commits
changed (e.g. amended a commit in NL after a previous delivery). Resolution:

```bash
git push sd-origin develop:main --force-with-lease
```

Then investigate why NL history changed and avoid amending delivered commits going forward.

### vendor/ or storage/ appeared in git status during rebase

Laravel's `vendor/` and `storage/framework/` are not gitignored in some contexts. Before
`git rebase --continue`:

```bash
git restore --staged backend/vendor/
git restore --staged backend/storage/
```

Verify `.gitignore` covers both:
```
vendor/
/storage/framework/
/storage/logs/
```

### Delivery pushed but se-origin/main not updated

The delivery flow only pushes to `be-delivery`. You still need to open the PR on GitHub and
merge it. Check GitHub for an open `be-delivery → main` PR.

---

## Quick Reference

```bash
# SD delivery (after squash merge to develop in NL)
cd nutri-ledger-sd-delivery
git fetch upstream && git reset --hard upstream/develop
rm -f .git/filter-repo/already_ran
git filter-repo --path backend/ --path frontend/ --path .github/workflows/ --path _sd/README.md --path-rename _sd/README.md:README.md --force
git push sd-origin develop:main

# SE delivery (after squash merge to develop in NL) — backend only
cd nutri-ledger-se-delivery
git fetch upstream && git reset --hard upstream/develop
rm -f .git/filter-repo/already_ran
git filter-repo --path backend/ --force
git push se-origin develop:be-delivery --force-with-lease
# → open PR on GitHub: be-delivery → main
```
