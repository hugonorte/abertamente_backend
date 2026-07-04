---
name: Technical Decisions & Rationale
description: Why tools were chosen, non-negotiable constraints, and architectural guidelines
type: reference
source: Extracted from memory system for .agents/memory/
---

# Technical Decisions & Rationale

This document explains *why* the tech stack is what it is, and defines the hard constraints that agents must respect.

## 1. Core Framework: Laravel
- **Why**: High performance, strongly typed (PHP), enterprise-ready, excellent built-in Dependency Injection and configuration management.
- **Constraint**: Use Minimal APIs or MVC Controllers based on existing project patterns. Do not mix paradigms unless requested.

## 2. ORM: Eloquent (Eloquent)
- **Why**: Native integration with Laravel, Collections/Query Builder provides strong typing for queries, handles migrations effectively.
- **Constraint**: 
  - ALWAYS use `async` methods (e.g., `get`).
  - ALWAYS use `.toBase()` for read-only queries to avoid memory bloat.
  - NEVER use lazy loading; use `.Include()` to prevent N+1 query problems.

## 3. Dependency Injection (DI)
- **Why**: Promotes loose coupling and makes unit testing easier.
- **Constraint**: ALL services and repositories must be injected via the constructor. NEVER use `new` to create service instances.

## 4. Testing: PHPUnit & Mockery
- **Why**: Standard in the Laravel ecosystem. PHPUnit provides a mature, attribute-based testing model that is robust and widely supported.
- **Constraint**: Tests must run in isolation. Mock all external dependencies (I/O, Database, APIs) using Mockery.

## 5. Coding Standards
- **Why**: Consistency makes code readable and maintainable.
- **Constraint**: Must follow standard PHP conventions (PascalCase for public members, camelCase for variables, `_` prefix for private fields).

## 6. Asynchronous Programming
- **Why**: Laravel handles thousands of concurrent requests by freeing up threads during I/O operations.
- **Constraint**: Never use `.Result` or `.Wait()`. This causes thread pool starvation. It is "Async All The Way".

## 7. Security & Secrets
- **Why**: Hardcoded secrets leak into source control.
- **Constraint**: Use `.env` or `.env` (untracked) for secrets. Do not commit passwords, tokens, or connection strings.
