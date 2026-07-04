# Architecture & Tech Stack

- **Framework**: Laravel 8.0+
- **Database**: Eloquent with MySQL.
- **Containerization**: Docker & Docker Compose.
- **CI/CD**: Jenkins.
- **Testing**: XUnit and Mockery (Backend), Cypress (Frontend).
- **Frontend Stack**: Razor Pages / Blazor (if applicable)
- **Logging**: Serilog (structured logging).
    - **Sinks**: Console and File.
    - **Configuration**: Daily rotation, 30-day retention, custom timestamped output template.
    - **Integration**: Bootstrap logger for initialization followed by full configuration.
- **Programming Patterns**: Repository Pattern, Service Layer, Dependency Injection.
