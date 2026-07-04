---
description: Executa todos os testes unitários e de integração do projeto utilizando o comando php artisan test.
---

# Workflow: Executar Testes do Sistema

Este workflow automatiza a execução de testes para validar as models e a integridade do sistema após alterações.

## Passos:

// turbo

1. Executa o comando de testes no diretório raiz ou na pasta de testes:

   ```powershell
   php artisan test app/tests/tests.csproj
   ```

2. Exibe o resultado da execução dos testes para o usuário.
3. Se houver falhas, sugere a análise dos logs de erro para correção.
