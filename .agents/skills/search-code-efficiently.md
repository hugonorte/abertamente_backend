# Skill: Search Code Efficiently (Zero Token Waste)

## Objective

Learn exactly where code lives and what it does **without reading entire files**. Use targeted grep patterns and strategic reads to minimize token consumption.

---

## Rule 1: Never Grep the Entire Project

**❌ Bad**:
```
Tool: grep_search (Query: "MyService", SearchPath: ".") # Searches bin/ and obj/ too → 1000s of hits
Tool: grep_search (Query: "public class", SearchPath: ".") # Every class matches → worthless
```

**✅ Good**:
```
Tool: grep_search (Query: "MyService", SearchPath: "Services/", Includes: ["*.cs"]) # Only Services code
Tool: grep_search (Query: "public class", SearchPath: "Models/")                  # Specific area
Tool: grep_search (Query: "\\[HttpGet", SearchPath: "Controllers/")               # Specific components
```

---

## Rule 2: Use Hot Paths List First

From `.agents/rules/project-context-cache.md` and `.agents/memory/03-hot-paths.md`:

**Know these file groups**:
```
🏠 Controllers (API Endpoints):
  Controllers/UsersController.cs
  Controllers/AuthController.cs

📝 Services (Business Logic):
  Services/IUsersService.cs
  Services/UsersService.cs

⚡ Models (Database Entities & DTOs):
  Models/User.cs
  Models/UserDto.cs

🗺️ Data (Eloquent):
  Data/ApplicationDbContext.cs
```

→ **Start with the hot path list, not blind grep**

---

## Rule 3: Controller Structure Pattern

Every controller follows this structure:
```
Controllers/
├── [ControllerName]Controller.cs        # Contains route endpoints
```

**To find a controller**:
```
# ✅ Exact path
Tool: list_dir (Path: "Controllers/")

# ✅ Find specific controller
Tool: grep_search (Query: "UsersController", SearchPath: "Controllers/")
```

---

## Rule 4: Service Structure Pattern

Every service usually has an interface and implementation:
```
Services/
├── I[ServiceName]Service.cs
└── [ServiceName]Service.cs
```

**To find a service**:
```
# ✅ List all services
Tool: list_dir (Path: "Services/")

# ✅ Find specific logic
Tool: grep_search (Query: "UserDto>", SearchPath: "Services/IUsersService.cs")

# ✅ See what's implemented
Tool: grep_search (Query: "public async Task", SearchPath: "Services/UsersService.cs")
```

---

## Rule 5: Eloquent Pattern

Entities and Eloquent Model:
```
Models/
├── User.cs

Data/
├── ApplicationDbContext.cs
```

**To find database structures**:
```
# ✅ Find all DbSets
Tool: grep_search (Query: "DbSet<", SearchPath: "Data/ApplicationDbContext.cs")

# ✅ Find Entity properties
Tool: grep_search (Query: "public string", SearchPath: "Models/User.cs")
```

---

## Rule 6: Test File Location Pattern

Tests are usually in a separate folder:
```
Tests/
├── UsersServiceTests.cs  # Unit test
```

**To find a test**:
```
# ✅ List all tests
Tool: grep_search (Query: "", SearchPath: "Tests/", Includes: ["*.cs"])

# ✅ Find test for specific service
Tool: grep_search (Query: "UsersService", SearchPath: "Tests/", Includes: ["*.cs"])

# ✅ Check test syntax
Tool: view_file (AbsolutePath: ".../UsersServiceTests.cs", EndLine: 30)  # See imports and Fact blocks
```

---

## Rule 7: Config Location

Global configuration:
```
.env
appsettings.Development.json
```

**To find config**:
```
# ✅ Check connection strings
Tool: grep_search (Query: "ConnectionStrings", SearchPath: ".env")

# ✅ Check Logging config
Tool: grep_search (Query: "Logging", SearchPath: ".env")
```

---

## Rule 8: Strategic Full-File Reads (Small Files Only)

✅ **SAFE to read entire file** (<100 lines):
```
Tool: view_file (AbsolutePath: "routes/api.php")              # DI setup
Tool: view_file (AbsolutePath: "Models/UserDto.cs")       # DTOs
Tool: view_file (AbsolutePath: ".env")        # Config
```

❌ **NOT safe to read entire file** (>200 lines):
```
Tool: view_file without EndLine on Controllers/UsersController.cs  # Don't read whole
Tool: view_file without EndLine on Services/UsersService.cs        # Don't read whole
```

**For large files**:
```
# ✅ Instead of reading entire file:
Tool: view_file (AbsolutePath: "Services/UsersService.cs", StartLine: 1, EndLine: 50)  # Read DI setup
Tool: grep_search (Query: "public async Task", SearchPath: "Services/UsersService.cs")  # Find method names
```

---

## Rule 9: Search Patterns for Native Tools

### Find all API Endpoints
```
Tool: grep_search (Query: "\\[Http", SearchPath: "Controllers/")
```

### Find all DbSets
```
Tool: grep_search (Query: "public DbSet", SearchPath: "Data/")
```

### Find all DI Registrations
```
Tool: grep_search (Query: "builder.Services.Add", SearchPath: "routes/api.php")
```

### Find method usage
```
Tool: grep_search (Query: "GetActiveUsersAsync", SearchPath: "Controllers/")
```

### Find specific test facts
```
Tool: grep_search (Query: "\\[Fact\\]\|\\[Theory\\]", SearchPath: "Tests/")
```

---

## Rule 10: Use Git to Understand Changes

**Instead of reading code**, use git to understand what changed:

```bash
# ✅ See what changed in a file
git log --oneline Models/User.cs | head -5

# ✅ See specific change
git show HEAD~1:Controllers/UsersController.cs

# ✅ See who changed what
git blame Services/UsersService.cs | grep -A2 "Task<"

# ✅ See recent commits
git log --oneline -15
```

---

## Rule 11: Line-Number-Based Reads (Ultra-Efficient)

When you know the line, read only that using native tools:

```
# ❌ Read whole file
Tool: view_file without EndLine

# ✅ Read just what you need
Tool: view_file (StartLine: 1, EndLine: 30)   # DI Setup
Tool: view_file (StartLine: 31, EndLine: 70)  # Method 1
Tool: view_file (StartLine: 71, EndLine: 100) # Method 2
```

**To find line numbers**:
```
Tool: grep_search (Query: "public async Task\|public class", MatchPerLine: true)
```

---

## Efficiency Checklist

Before reading a file:

- [ ] Is this in the cache (architecture.md, conventions.md, hot_paths.md)?
- [ ] Can I grep instead of read?
- [ ] Can I read just the top 50 lines instead of whole file?
- [ ] Can I use git log to understand the change?
- [ ] Is this a massive file (>500 lines)? If yes, use head/tail/grep only.
- [ ] Have I seen this pattern before? If yes, skip the example.

---

## Example: "I need to understand UsersController"

**❌ Inefficient (150K tokens)**:
```
1. Read entire UsersController.cs (500 lines)
2. Read UsersService.cs (300 lines)
3. Read UserDto.cs (40 lines)
Total: 840 lines, 80K tokens
```

**✅ Efficient (3K tokens)**:
```
1. Check conventions.md: Controller pattern
2. Grep for HttpGet in UsersController: Tool: grep_search
3. Read lines 1-30: Tool: view_file (StartLine: 1, EndLine: 30)
4. Grep for specific service usage
5. If specific bug: grep for exact error, then read 5-10 relevant lines
Total: 30 lines strategically read, 3K tokens
```

→ **25x fewer tokens, same understanding**

---

## Performance Tips

**Token per action**:
- Full file read (500 lines): 50K tokens
- Head/tail (50 lines): 5K tokens
- Grep (10 matches): 1K tokens
- Git log (5 commits): 2K tokens

**Always prefer** (in order):
1. Cache/memory files
2. Git history
3. Grep output
4. Head/tail selective read
5. Full file read (last resort)

---

**Remember**: Every line you read costs tokens. Read strategically, grep aggressively, cache first.
