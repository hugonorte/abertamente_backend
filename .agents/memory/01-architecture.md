---
name: Architecture & Project Structure
description: Core architectural patterns, directory layout, and high-level design
type: reference
source: Extracted from memory system for .agents/memory/
---

# Architecture & Project Structure

## 1. Directory Layout

This project follows a standard Laravel Web API directory structure:

```text
/
├── Controllers/         # API endpoints (HTTP routing, input validation)
├── Services/            # Business logic (interfaces and implementations)
├── Models/              # Domain entities and Data Transfer Objects (DTOs)
├── Data/                # Eloquent Eloquent Model and database migrations
├── Tests/               # PHPUnit test projects (Unit and Integration tests)
├── routes/api.php           # App composition root, DI registration, HTTP pipeline
├── .env     # Configuration files
├── abertamente-backend.sln # Visual Studio Solution file
└── .agents/             # Agent rules, workflows, and memory cache
```

## 2. Core Architectural Patterns

### Layered Architecture (N-Tier)
The application separates concerns into discrete layers:
- **Presentation Layer (Controllers)**: Receives HTTP requests, validates input, calls services, returns HTTP responses. No business logic here.
- **Business Logic Layer (Services)**: Contains the core business rules. Operates on domain models and DTOs.
- **Data Access Layer (Data / Eloquent)**: Handles database connectivity, queries, and persistence using Eloquent.

### Dependency Injection (DI)
- Laravel's built-in DI container is used exclusively.
- All services, repositories, and the `Eloquent Model` are injected via constructor.
- Never use `new` to instantiate services.
- Lifetimes:
  - `Transient`: Created each time requested (rarely used for stateful services).
  - `Scoped`: Created once per HTTP request (used for Eloquent `Eloquent Model` and most services).
  - `Singleton`: Created once per application lifetime (used for caches, stateless utilities).

### Eloquent (Eloquent)
- Code-first approach with migrations.
- **Read-Only Queries**: Must use `.toBase()` to improve performance and reduce memory usage.
- **Async Data Access**: All I/O operations must use asynchronous methods (e.g., `get()`, `first()`).

## 3. Data Flow Example

**Requesting a list of active users:**

1. **Client** calls `GET /api/users/active`.
2. **`UsersController`** receives the request.
3. **`UsersController`** calls `await _usersService.GetActiveUsersAsync()`.
4. **`UsersService`** uses `_dbContext.Users.Where(u => u.IsActive).toBase().get()`.
5. **Eloquent** translates Collections/Query Builder to SQL, fetches from the database.
6. **`UsersService`** maps the entity to a `UserDto` (if applicable) and returns it.
7. **`UsersController`** wraps the result in an `Ok()` response and returns HTTP 200.

## 4. Configuration and Environments

- `.env`: Contains global configuration (e.g., logging settings, default connection strings).
- `appsettings.Development.json`: Overrides for local development.
- **Secrets**: Never commit passwords or API keys to `.json` files. Use environment variables or Laravel Secret Manager locally.

## 5. Error Handling and Logging

- **Logging**: Typically configured via Serilog or built-in Microsoft.Extensions.Logging. Injected via `ILogger<T>`.
- **Global Exception Handling**: Expected to use a middleware or Exception Filter to catch unhandled exceptions and return standard problem details responses instead of stack traces.
