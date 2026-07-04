# Developer Workflow & Environment

### Docker Configuration
The development environment is fully containerized using Docker Compose:
- **Application Container**: `sandbox_app` (Laravel SDK 9.0).
- **Database Container**: `sandbox_db` (MySQL 8.0).
- **Orchestration**: Managed via `docker-compose.yml`, loading environment variables from a root `.env` file.

### Eloquent Migrations
For agents to run migrations autonomously, they must be executed inside the `sandbox_app` container to avoid host-side permission issues and ensure correct database connectivity.

- **Tool Path**: `/root/.laravel/tools/laravel-ef` (Installed inside the container).
- **Add Migration**:
  ```bash
  docker exec sandbox_app /root/.laravel/tools/php artisan make:migration <MigrationName> --project abertamente/
  ```
- **Update Database**:
  ```bash
  docker exec sandbox_app /root/.laravel/tools/php artisan migrate --project abertamente/
  ```

### Ports & Access
- **API**: Accessible at `http://localhost:5271/swagger`.
- **MySQL**: Accessible at `localhost:3306`.

### Pull Request (PR) Code Review Workflow
When conducting PR code reviews:
- **Mandatory Comments**: It is strictly mandatory to write and submit reviews/comments directly on the PR in GitHub. Even if the code has zero issues, a general comment congratulating the developer and confirming the code is correct must be posted.
- **Handling GitHub Authentication Obstacles**: If the agent encounters any credential or authentication errors (e.g., invalid token in the keyring, expired CLI credentials) that prevent posting to GitHub:
  1. The agent must explicitly report the authentication failure to the developer in the chat.
  2. The agent must present the full code review findings/comments directly in the chat.
  3. The agent must provide a clear step-by-step guide instructing the developer on how to resolve the authentication problem (e.g., executing `gh auth login` or updating keys).
