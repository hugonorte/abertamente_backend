---
name: Coding Conventions
description: Required coding standards, naming conventions, and best practices for PHP
type: reference
source: Extracted from memory system for .agents/memory/
---

# Coding Conventions (Laravel / PHP)

All code written by agents MUST follow these strict conventions.

## 1. Naming Conventions

- **Classes, Traits, Interfaces**: `PascalCase`
- **Interfaces**: Sufixo `Interface` (e.g., `UserRepositoryInterface`) ou uso direto do nome. Não usar prefixo `I`.
- **Methods and Properties**: `camelCase` (e.g., `getActiveUsers()`)
- **Local Variables and Parameters**: `camelCase` (e.g., `userId`, `activeUsers`)
- **Private/Protected Fields**: `camelCase` (e.g., `dbContext`, `logger`). Não usar prefixo `_`.
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `MAX_RETRY_COUNT`)
- **Traits**: Sufixo `Trait` (e.g., `HasRolesTrait`)

## 2. Tipagem e Retornos

- **Strict Types**: Usar `declare(strict_types=1);` no topo dos arquivos PHP.
- **Tipagem de parâmetros e retornos**: Sempre tipar parâmetros e definir tipos de retorno explícitos nos métodos (e.g. `public function getUser(int $id): ?User`).
- **Nulo**: Usar `?` para tipos anuláveis em vez de depender apenas de documentação PHPDoc.

## 3. Dependency Injection (DI)

- Use injeção de dependência via construtor, que é resolvida automaticamente pelo Service Container do Laravel.
- Exemplo:
  ```php
  class UserController extends Controller
  {
      public function __construct(
          private readonly UserService $userService,
          private readonly LoggerInterface $logger
      ) {}
  }
  ```

## 4. Eloquent (Eloquent)

- **Read-Only**: Para consultas que não precisarão de instâncias de models (como relatórios ou listas massivas de leitura), use `toBase()` ou consultas ao `DB` (Query Builder).
  ```php
  $users = User::toBase()->get();
  ```
- **N+1 Problem**: Sempre use `with()` para fazer o eager loading de relacionamentos e evitar queries em loops.
  ```php
  $users = User::with('profile')->get();
  ```
- **Performance de Memória**: Se for iterar sobre milhares de registros, prefira o método `chunk()` ou `cursor()`.

## 5. API Controllers

- Herdar de `Controller` (namespace `App\Http\Controllers`).
- Usar injeção de dependência via métodos ou construtores (graças ao Container do Laravel).
- Manter controllers limpos (thin controllers). Eles devem apenas lidar com a requisição HTTP (status code, payload) e delegar a regra de negócios para as Classes de Serviço (Services).
- Retornar respostas utilizando os helpers do Laravel (e.g., `response()->json()`) ou `JsonResponse`.

## 6. Testing (PHPUnit + Mockery)

- **Frameworks**: Use `PHPUnit` for tests, `Mockery` for mocking interfaces.
- **Naming**: `*Test.php` (e.g., `UserServiceTest.php`).
- **Structure**: Use Arrange / Act / Assert pattern.
- Example:
  ```php
  use PHPUnit\Framework\Attributes\Test;
  use Tests\TestCase;

  class UserServiceTest extends TestCase
  {
      #[Test]
      public function get_active_users_returns_only_active_users(): void
      {
          // Arrange
          $mock = \Mockery::mock(UserRepository::class);
          // ... setup mock ...
          $service = new UserService($mock);

          // Act
          $result = $service->getActiveUsers();

          // Assert
          $this->assertNotNull($result);
      }
  }
  ```

## 7. SOLID & Clean Code

- **Single Responsibility Principle**: A class should have only one reason to change.
- **Open/Closed Principle**: Open for extension, closed for modification.
- **Liskov Substitution**: Derived classes must be substitutable for their base classes.
- **Interface Segregation**: Don't force clients to implement interfaces they don't use.
- **Dependency Inversion**: Depend on abstractions, not concretions (use interfaces).

## 8. Exceptions

- Não utilize Exceptions para fluxo de controle básico.
- Lance Exceptions específicas (e.g., `InvalidArgumentException`, `ModelNotFoundException`) em vez do genérico `Exception`.
- Nunca capture uma exceção com um `catch` vazio apenas para silenciá-la sem registrar no log.
- **Prevenção de Exception Leakage:** Exceções não tratadas não devem vazar detalhes técnicos da base de dados para o cliente em ambiente de produção.
- O Laravel possui o `App\Exceptions\Handler` (ou `bootstrap/app.php` no Laravel 11+) onde erros devem ser renderizados e reportados.
- Respostas de Erro de API: Siga o padrão do Laravel retornando JSON claro ou adote `ProblemDetails` (RFC 9457) se requerido pela arquitetura.

## 9. PHP Features to Use

- Use Promoted Properties no construtor para evitar boilerplate na declaração de dependências.
- Use `match` expressions em vez de `switch` longos quando estiver buscando valores de retorno.
- Utilize Nullsafe Operator (`?->`) em vez de checagens repetitivas de `is_null`.
- Faça uso intenso de Collection Methods do Laravel (`map`, `filter`, `reduce`) no lugar de `foreach` extensos.
