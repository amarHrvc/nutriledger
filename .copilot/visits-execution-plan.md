# Visits Feature - Parallel Agent Execution Plan

**Started**: 2026-04-10 08:20 UTC  
**Status**: 🔄 In Progress

---

## Parallel Agent Configuration

### Agent 1: visits-agent-1 (Foundation Tasks)
**Agent ID**: visits-agent-1  
**Tasks**: 3 sequential tasks  

| # | Task ID | Title | Status | Commit |
|---|---------|-------|--------|--------|
| 1 | VS-1.1 | Run existing domain tests | ⏳ Running | — |
| 2 | VS-2.1 | Fix date cast in Visit model | ⏳ Queued | — |
| 3 | VS-2.2 | Fix VisitPolicy | ⏳ Queued | — |

---

### Agent 2: visits-agent-2 (Policy & Request Tasks)
**Agent ID**: visits-agent-2  
**Tasks**: 3 sequential tasks (starts after Agent 1 completes VS-2.2)

| # | Task ID | Title | Status | Commit |
|---|---------|-------|--------|--------|
| 1 | VS-2.3 | Update VisitPolicyTest | ⏳ Waiting | — |
| 2 | VS-2.4 | Fix StoreVisitRequest | ⏳ Waiting | — |
| 3 | VS-3.1 | Create VisitResource | ⏳ Waiting | — |

---

## Quality Gates (Pre-Commit)

All tasks must pass:

```bash
# 1. Run all tests
php artisan test

# 2. Run static analysis
composer run analyse

# 3. Format changed files only
vendor/bin/pint --dirty

# 4. If any fails → FIX before committing
```

---

## Commit Standards

**Format**: `feat(visits): [TASK-ID] Task description`

Examples:
- `feat(visits): VS-2.1 Fix date cast in Visit model`
- `feat(visits): VS-2.2 Fix VisitPolicy`
- `feat(visits): VS-2.3 Update VisitPolicyTest`

**Rules**:
- One logical change per commit
- Atomic commits (don't mix unrelated changes)
- No pushing to remote (local only)
- Clear, descriptive messages

---

## Monitoring Instructions

### Check Agent Status
```bash
# Agent 1
read_agent visits-agent-1

# Agent 2
read_agent visits-agent-2
```

### Check Git Progress
```bash
# Latest 10 commits
git log --oneline -10

# See commits by message
git log --oneline | grep "visits:"
```

### Check Test Status
```bash
# Run tests
php artisan test

# Run static analysis
composer run analyse

# Format code
vendor/bin/pint --dirty
```

---

## Workflow

### Phase 1: Agent 1 (Foundation)
Agent 1 executes VS-1.1, VS-2.1, VS-2.2 sequentially:
1. Each task reads bd issue
2. Makes code changes
3. Runs quality gates
4. Commits with clear message
5. Reports commit hash

### Phase 2: Agent 2 (Policy & Request)
Agent 2 waits for Agent 1 to complete VS-2.2, then executes VS-2.3, VS-2.4, VS-3.1:
1. Waits for Agent 1 signal
2. Verifies VS-2.2 commit exists
3. Executes next tasks
4. Same quality gate flow
5. Reports commit hash

### Phase 3: Agent 1 Continues (Routes & Controller)
After Agent 2 starts, Agent 1 continues with:
- VS-3.2: Write failing VisitCreateTest (10 tests)
- VS-3.3: Register nested visit routes
- VS-3.4: Create VisitController and implement store()

---

## Next Steps

1. ✅ **Agents Running**: Both agents executing
2. 🔄 **Monitor**: Watch for completion notifications
3. 📋 **Review**: Check git log for commits
4. ✅ **Verify**: Run quality gates manually if needed
5. 📝 **Approval**: Review changes before pushing to remote

---

## Notes

- Agents restart after each task (as per requirements)
- No remote push until you review and approve
- All changes are local only
- Quality gates are non-negotiable (tests, linter, format)
- Each task is independent and can be reverted if needed
