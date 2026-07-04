---
description: Executa apenas o papel de Full-Stack Engineer (@engineer) para implementar o código baseado nas especificações.
---

Quando o usuário digitar `/engineer`, orquestre o papel de **Full-Stack Engineer (@engineer)** seguindo estritamente as definições em `.agents/agents.md`.

### Sequência de Execução:
1. **Leitura da Especificação**: Leia os arquivos na pasta .agents/rules para entender profundamente os requisitos, a arquitetura e as tecnologias aprovadas pelo PM.
2. **Conformidade Obrigatória**: Você **DEVE** seguir rigorosamente os critérios e premissas definidos nos arquivos na pasta .agents/rules, com foco especial em:
   - **Security & Sensitivity**: Aplique a Política de Zero-Secret (sem credenciais no código), garanta a sanitização de PII em logs e utilize apenas mock data em testes.
   - **Error Handling & API Responses**: Ao criar ou modificar endpoints, você deve prevenir o **Exception Leakage** (Vazamento de Detalhes da Infraestrutura). Nunca devolva mensagens de erro genéricas (`ex.Message`) no corpo da resposta. Formate **todos os erros** da API seguindo estritamente a norma **RFC 9457 (Problem Details for HTTP APIs)**, utilizando a estrutura `ProblemDetails`.
   - **Mandatory Development Standards**: Siga os padrões de execução assíncrona (`Async/Await`), otimização de consultas Eloquent (`.toBase()`, Eager Loading), Injeção de Dependência e padrões de nomenclatura (PascalCase/camelCase).
3. **Desenvolvimento Orientado a Testes (TDD) - Etapa RED**: 
   - **Regra de Bloqueio INEGOCIÁVEL**: É ABSOLUTAMENTE PROIBIDO E INACEITÁVEL escrever qualquer código funcional, lógica de negócio, rotas ou models antes dos testes unitários/integração.
   - **ZERO BRECHAS**: Não existe nenhuma exceção a esta regra. Você não pode sugerir código de implementação antes de fornecer os testes.
   - Escreva primeiramente os testes unitários e de integração necessários (na pasta `Tests/`).
   - Execute os testes no terminal (ex: `php artisan test --logger "console;verbosity=quiet"`).
   - **PONTO DE PARADA OBRIGATÓRIO 1**: Você DEVE apresentar a saída/log do terminal provando inequivocamente que os testes falharam (RED) para o usuário. **PARE A EXECUÇÃO IMEDIATAMENTE** e solicite a aprovação explícita do usuário antes de avançar para o passo 4. Sem esta aprovação, você não pode continuar.
4. **Implementação de Código - Etapa GREEN**: 
   - Apenas após a liberação do usuário, escreva o código funcional mínimo necessário para que os testes do passo anterior passem (em `app/`).
   - Execute os testes novamente.
   - **PONTO DE PARADA OBRIGATÓRIO 2**: Apresente o log de sucesso (GREEN) provando que os testes passaram. **PARE A EXECUÇÃO** e aguarde o usuário autorizar o avanço para a refatoração.
5. **Revisão Técnica e Refatoração - Etapa REFACTOR**: 
   - Após a permissão, refatore o código para garantir os princípios DRY, Clean Code e as regras do Eloquent.
   - Execute os testes novamente para garantir que a refatoração não quebrou nada.
   - Apresente o log final confirmando o sucesso e conclua a tarefa.
