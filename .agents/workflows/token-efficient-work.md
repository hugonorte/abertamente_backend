# Workflow: Token-Efficient Work

This workflow guides agents through every task while minimizing token consumption. Follow this for **every interaction** with the codebase.

---

## Phase 1: Context Setup (5-10 min)

### Step 1.1: Load Cache Files
**What**: Read the minimal files needed for your role

**Time**: 2-3 min

| Role | Read This | Size |
|------|-----------|------|
| @pm | `CLAUDE.md` + `tech_decisions.md` | 8KB |
| @engineer | `CLAUDE.md` + `conventions.md` + `hot_paths.md` | 9KB |
| @qa | `conventions.md` testing section + `hot_paths.md` | 5KB |
| @devops | `CLAUDE.md` + tech_decisions.md build section | 5KB |

**Token cost**: ~10K tokens (one time per session)

### Step 1.2: Understand Your Constraints
**What**: Read the constraint section for your role from `tech_decisions.md`

**Token cost**: Included in Step 1.1

### Step 1.3: Identify Your Working Directory
**What**: Know which folder you'll modify:

- @engineer: `Controllers/`, `Services/`, `Models/`, `Data/`
- @qa: `Tests/`
- @devops: `Dockerfile`, `.github/workflows/`, `Jenkinsfile`

**No token cost** — you already know from cache

---

## Phase 2: Task Analysis (5-15 min)

### Step 2.1: Read the Requirement
**What**: Understand what you need to build/fix/test

**How**:
```
Task: "Add new endpoint to get active users"

✅ Do:
1. Read the requirement twice
2. List what needs to change:
   - Add method in IUsersService and UsersService
   - Add endpoint in UsersController
   - Update UsersServiceTests
3. Estimate lines of code
```

### Step 2.2: Find Similar Examples
**What**: Locate existing code that does similar work

**How**:
```
# Task: "Add new endpoint"
# Search pattern:
Tool: grep_search
Query: "\\[HttpGet"
SearchPath: "Controllers"
Includes: ["*.cs"]

# Check UsersController as example
Tool: list_dir (Path: "Controllers/")
Tool: view_file (AbsolutePath: ".../UsersController.cs", StartLine: 1, EndLine: 50)
```

**Token cost**: ~3K tokens (targeted grep + 50 lines)

### Step 2.3: Check Dependencies
**What**: Verify tools you need are installed

**How**:
```
# Task uses a specific Composer package?
Tool: grep_search (Query: "PackageName", SearchPath: ".", Includes: ["*.csproj"])
```

**Token cost**: <1K tokens

---

## Phase 3: Planning (No Code Yet)

### Step 3.1: List Exact Changes
**What**: Write down every file you'll touch

**Example**:
```
Task: Add active users endpoint

Files to modify:
□ Services/IUsersService.cs
  └─ Add List<UserDto>> GetActiveUsersAsync()
□ Services/UsersService.cs
  └─ Implement Eloquent query with .toBase()
□ Controllers/UsersController.cs
  └─ Add [HttpGet("active")] endpoint
□ Tests/UsersServiceTests.cs
  └─ Add unit test for GetActiveUsersAsync
```

### Step 3.2: Check Hot Paths for Impact
**What**: From `hot_paths.md`, understand what breaks if you change this

**Example**:
```
Changing: Services/UsersService.cs

Hot Paths check:
✓ This is in "Core Services" → Ensure logic is robust
✓ Controller depends on this → Update interface
✓ Test coverage: Check if UsersServiceTests.cs exists
  → If yes, must update test
  → If no, write test first (TDD approach)
```

### Step 3.3: Read the Files You'll Modify (Exactly)
**What**: Now read ONLY the files on your modification list

**How**:
```
# File 1: Controller
Tool: view_file (AbsolutePath: ".../UsersController.cs", EndLine: 100) # See structure
Tool: grep_search (Query: "public class\|Route\|HttpGet", SearchPath: ".../UsersController.cs")

# File 2: Service interface
Tool: grep_search (Query: "Task<", SearchPath: "Services/IUsersService.cs")

# File 3: Test
Tool: view_file (AbsolutePath: ".../UsersServiceTests.cs", EndLine: 50)
```

**Token cost**: ~5K tokens (selective reads, not full files)

### Step 3.4: Ask for Approval (if @pm involved)
**What**: If task impacts architecture, get green light before coding

**Do NOT ask**: "Ready to start coding?" ← Too vague

**Do ask**: 
```
This task will:
1. Add new query to UsersService
2. Expose endpoint in UsersController
3. Update unit tests
4. No new dependencies needed

Should I proceed?
```

**Token cost**: <1K tokens

---

## Phase 4: Implementation

### Step 4.1: Follow the Pattern (No Exploration)
**What**: Use the pattern you already know

**From conventions.md**, you know:
```
Eloquent Query pattern:
- Async method with Async suffix
- await _context.Users.Where(u => u.IsActive).toBase().get();
```

→ **Write code. Don't explore for examples. You have the pattern.**

**Token cost**: 0 tokens (all known patterns)

### Step 4.2: Write Each File
**What**: Touch exactly the files you listed, in order

```
File 1: Services/IUsersService.cs
├─ Add method signature

File 2: Services/UsersService.cs
├─ Implement method with Eloquent

File 3: Controllers/UsersController.cs
├─ Add endpoint mapping to service method

File 4: Tests/UsersServiceTests.cs
├─ Write tests mocking Eloquent Model or repository
```

**Check**: 
```
# Verify syntax with build
laravel build
```

**Token cost**: 0 tokens (writing, not reading)

### Step 4.3: Stop Reading Code
**What**: Once you start writing, stop exploring

**Why**: Reading more = more tokens = less time coding

**If you get stuck**:
1. Check the cache pattern again (0 tokens)
2. Grep for exact error (1K tokens)
3. Read only the relevant line (1K tokens)
4. Don't read entire file for context

**Token cost**: Keep this phase under 5K total

---

## Phase 5: Validation

### Step 5.1: Run Tests
**What**: Verify your changes don't break anything

```bash
# Verify build
laravel build

# Run tests quietly
php artisan test --logger "console;verbosity=quiet"
```

**Token cost**: 0 tokens (commands, not reading)

### Step 5.2: Quick Code Review (Yourself)
**What**: Before committing, check:

- [ ] Async properly implemented: `grep_search "Wait()\|Result" in [YOUR_FILES]` — 0 matches?
- [ ] No tracking used for reads: `grep_search "ToList" in [YOUR_FILE]` — Ensure `.toBase()` precedes it.
- [ ] Dependencies injected: `grep_search "new " in [YOUR_FILE]` — No direct instantiation of services/contexts?

**Do NOT**: Open files for review unless test/build fails

**Token cost**: <1K tokens (grep only)

### Step 5.3: Git Diff
**What**: See exactly what changed

```bash
git diff Controllers/
git diff Services/
```

**Token cost**: <1K tokens

---

## Phase 6: Commit & Report

### Step 6.1: Write Commit Message
**Pattern** (from conventions.md):
```
<type>: <description>

Types: feat, fix, refactor, test, docs, chore

Examples:
- feat(users): add endpoint to get active users
- test(users): add unit tests for active users query
```

### Step 6.2: Create Pull Request
**What**: Link task to PR

**Pattern**:
```
Title: feat(users): add active users endpoint

Body:
## Changes
- Added `GetActiveUsersAsync` to `UsersService`
- Exposed `/api/users/active` in `UsersController`
- Added unit tests for service method

## Test Plan
- [x] Build passes: laravel build
- [x] Tests pass: php artisan test
- [x] Verified Eloquent uses toBase
```

### Step 6.3: Report Success
**What**: Tell the user what was done

**Format**:
```
✅ Task complete:
- Modified: Services/UsersService.cs, Controllers/UsersController.cs
- Tests: All passing
- PR: #XXX (link)

Token usage: 28K
- 10K cache load
- 5K code reading
- 8K implementation & validation
- 5K overhead
```

**Token cost**: <1K tokens

---

## Token Budget by Phase

| Phase | Time | Tokens | Notes |
|-------|------|--------|-------|
| 1. Cache Setup | 5 min | 10K | One time per session |
| 2. Analysis | 10 min | 3K | Grep + selective read |
| 3. Planning | 5 min | 5K | Full files to modify only |
| 4. Implementation | 30 min | 0 | Writing, not reading |
| 5. Validation | 10 min | 2K | Grep + git diff + build |
| 6. Commit & Report | 5 min | 1K | Git & commit message |
| **Total per task** | **65 min** | **~21K** | **Budget: 30K** |

**Target**: Stay under 30K tokens per task
**Actual**: Average 21K tokens (30% buffer)

---

## Common Mistakes (Don't Do These)

| ❌ Mistake | 💰 Cost | ✅ Instead |
|-----------|--------|----------|
| Read entire codebase to understand | 200K | Read cache files + grep (15K) |
| Explore similar controllers | 50K | Grep for pattern + read 50 lines (5K) |
| Read full files | 100K | Head/tail or grep + read sections (10K) |
| Ask for clarification without trying | 5K | Try first, ask only if stuck (0K) |
| Run tasks without cache | 50K | Load cache first (10K) |
| Write tests then find test pattern | 30K | Find pattern first (5K), write (0K) |
| Check old code for examples | 20K | Check conventions.md (0K) |

---

## Role-Specific Workflows

### @engineer: Fastest Path
1. ✅ Read `conventions.md` (3KB)
2. ✅ Read `hot_paths.md` (4KB)
3. ✅ Grep for 1 example (1KB)
4. ✅ Write code (0KB)
5. ✅ Build and Test (0KB)
6. ✅ Commit (0KB)
**Total: ~10K tokens + time**

### @qa: Test-First Path
1. ✅ Read test conventions (1KB)
2. ✅ Grep for existing tests (1KB)
3. ✅ Write test (0KB)
4. ✅ See test fail (0KB)
5. ✅ Ask @engineer to fix (0KB)
6. ✅ Verify test passes (0KB)
**Total: ~5K tokens + time**

### @pm: Spec-Only Path
1. ✅ Read `CLAUDE.md` (3KB)
2. ✅ Read `tech_decisions.md` (5KB)
3. ✅ Write spec (0KB)
4. ✅ Ask for approval (0KB)
5. ✅ Iterate on feedback (5KB max)
**Total: ~13K tokens + time**

---

## Emergency: Something's Wrong

**If build/tests fail**:
1. ❌ Don't explore code
2. ✅ Read error message exactly from terminal
3. ✅ Grep for error pattern
4. ✅ Check 1 example that works
5. ✅ Fix your code

**If task unclear**:
1. ❌ Don't read codebase for context
2. ✅ Ask user for clarification
3. ✅ Propose 2-3 approaches
4. ✅ Wait for approval

---

## Success Criteria

✅ **You succeeded if**:
- Task is complete
- Tests and build pass
- Token usage under 30K (ideally 20K)
- No code exploration
- Pattern-based implementation

❌ **You failed if**:
- Token usage over 50K
- You read entire files for pattern matching
- You explored codebase unnecessarily
- Task incomplete or build fails

---

## Quick Checklist

Before starting any task:

- [ ] Read cache (CLAUDE.md, conventions.md, hot_paths.md)
- [ ] Understand requirement
- [ ] Find 1 similar example (grep)
- [ ] Plan exact files to modify
- [ ] Check constraints from tech_decisions.md
- [ ] Get approval if task impacts architecture
- [ ] Start coding (stop exploring)
- [ ] Run build and tests
- [ ] Commit and report

---

**Remember**: The goal is maximum progress with minimum tokens.

Use this workflow every time. Your future self will thank you.
