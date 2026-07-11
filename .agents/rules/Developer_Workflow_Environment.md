# Developer Workflow & Environment

### Docker Configuration (Laravel Sail)
The development environment is fully containerized using Laravel Sail (Docker). **PHP is NOT installed on the host machine.**

**CRITICAL RULE FOR AGENTS:** 
Whenever you need to run any Laravel/PHP command (such as running tests, migrations, or artisan commands), you MUST use Laravel Sail.
❌ **INCORRECT:** `php artisan test`
✅ **CORRECT:** `./vendor/bin/sail artisan test` (or `sail artisan test` se o alias estiver configurado)

### Database & Migrations
To run migrations or interact with the database, always use the Sail wrapper:
- **Add Migration**: `./vendor/bin/sail artisan make:migration <MigrationName>`
- **Update Database**: `./vendor/bin/sail artisan migrate`
- **Run Tests**: `./vendor/bin/sail artisan test`

### Ports & Access
- **API**: Accessible via HTTP standard ports (check `.env` for specifics).
- **MySQL**: Accessible at `localhost:3306`.

### Pull Request (PR) Code Review Workflow
When conducting PR code reviews:
- **Mandatory Comments**: It is strictly mandatory to write and submit reviews/comments directly on the PR in GitHub. Even if the code has zero issues, a general comment congratulating the developer and confirming the code is correct must be posted.
- **Handling GitHub Authentication Obstacles**: If the agent encounters any credential or authentication errors (e.g., invalid token in the keyring, expired CLI credentials) that prevent posting to GitHub:
  1. The agent must explicitly report the authentication failure to the developer in the chat.
  2. The agent must present the full code review findings/comments directly in the chat.
  3. The agent must provide a clear step-by-step guide instructing the developer on how to resolve the authentication problem (e.g., executing `gh auth login` or updating keys).
