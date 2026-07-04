---
trigger: always_on
---

# Padrões PHP e Eloquent

Sempre que escreveres, editares ou analisares código PHP que interaja com o banco de dados via Eloquent, deves seguir estritamente as seguintes regras:

- _Sincronicidade do PHP:_ O ecossistema padrão do Laravel é síncrono. Não utilize métodos irreais como `getAsync()`, `saveAsync()` ou force o uso da palavra `await` (a menos que explicitamente esteja escrevendo testes Node.js/JS ou utilizando frameworks async específicos que o usuário aprovar). Use os métodos padrões síncronos do Eloquent: `get()`, `first()`, `save()`.
- _Performance (Read-Only):_ Para consultas que servem apenas para leitura e exibição de dados (onde as entidades não serão atualizadas no banco), utilize métodos do Query Builder nativo ou adicione `.toBase()` à query para otimizar o uso de memória.
- _Injeção de Dependência:_ O Eloquent Model (ou a interface do repositório) pode ser injetado via construtor ou resolvido via Service Container, mas o uso de facades (e.g. `User::create()`) também é perfeitamente idiomático no Laravel e permitido.
- _Prevenção de N+1:_ Quando precisar retornar dados de tabelas relacionadas, utilize `with()` (e `with('relacionamento.aninhado')`) para fazer o eager loading de forma explícita. Nunca faça consultas ao banco de dados dentro de loops `foreach`.
- _Nomenclatura Padrão:_ Use a norma PSR-1/PSR-12. `PascalCase` para Classes e Traits. `camelCase` para métodos, propriedades e variáveis locais. Evite prefixos `_` para atributos privados/protegidos em PHP moderno.
- _Tratamento de Erros:_ Evite vazamento de detalhes de infraestrutura (Exception Leakage). Exceções não tratadas ou de banco de dados (como `QueryException`) não devem ser devolvidas como texto simples para os clientes. Utilize o `App\Exceptions\Handler` do Laravel ou retorne respostas padronizadas como JSON claro. Utilize `ProblemDetails` (RFC 9457) se requerido arquiteturalmente.
