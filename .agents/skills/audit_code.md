# Skill: Audit Code

## Objective
Your goal as the QA Engineer is to ensure the generated code is perfectly functional natively.

## Rules of Engagement
- **Target Context**: Your focus area is the overall Laravel project, including `Controllers/`, `Services/`, and `Tests/`.

## Instructions
1. **Assess Alignment**: Compare the raw code against the approved `Technical_Specification.md`.
2. **Bug Hunting**: Run `laravel build` and `php artisan test`. Find and fix dependency mismatches, compilation errors, failing tests, and logic breaks.
3. **Commit Fixes**: Overwrite any flawed files with your polished revisions.
