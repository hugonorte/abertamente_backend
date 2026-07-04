---
description: Este workflow automatiza a revisão de código em repositórios GitHub seguindo os padrões sênior de Laravel.
---

# Workflow: Review Automático de Pull Request Laravel
Este workflow automatiza a revisão de código em repositórios GitHub seguindo os padrões sênior de Laravel.

## 🏁 Trigger
Comando: "Iniciar revisão de PR" ou "Simular CodeRabbit"

## 📝 Passos do Workflow

### Passo 1: Coleta de Contexto
- **Ação:** Pergunte ao usuário: "Qual é o ID do Pull Request que você deseja revisar?"
- **Variável:** Guarde a resposta como `{{PR_ID}}`.

### Passo 2: Listagem de Arquivos
- **Ação:** Use a ferramenta `github-mcp.list_pull_request_files` informando o `{{PR_ID}}`.
- **Filtro:** Identifique apenas os arquivos com extensão `.cs`.

### Passo 3: Análise e Crítica
- **Ação:** Para cada arquivo `.cs` identificado:
    1. Leia o conteúdo do diff/arquivo.
    2. Aplique rigorosamente a regra `@laravel-reviewer`.
    3. Identifique violações de: Async/Await, Performance de Collections/Query Builder, Injeção de Dependência e Padrões de Clean Code.

### Passo 4: Publicação de Comentários
- **Ação:** É **obrigatório** deixar comentários no PR (comentários em linha para problemas ou um comentário geral parabenizando o desenvolvedor se tudo estiver perfeito).
- **Tratamento de Autenticação (Crítico):** Caso identifique qualquer empecilho de autenticação com o GitHub (ex: token inválido/expirado no keyring, credenciais ausentes no GitHub CLI), você deve:
    1. Informar o usuário de maneira explícita no chat sobre o problema de autenticação.
    2. Apresentar os comentários/revisão gerados diretamente no chat para que o usuário não perca a análise.
    3. Instruir o desenvolvedor com o passo a passo exato do que ele deve fazer para resolver a questão da autenticação (ex: rodar `gh auth login`).
- **Ação com Sucesso:** Use `github-mcp.create_inline_comment` para postar cada sugestão ou o comentário geral diretamente no GitHub.

### Passo 5: Verificar necessidade de ajustes 
- **Ação:** Havendo necessidade de ajustes:
   - Acionar o agente @pm conforme o arquivo agents.md, que deverá montar um Implementation-plan para aprovação do usuário
   - Caso o usuário aprove o implementation-plan do passo anterior, acionar o agente @engineer conforme o arquivo agents.md para implementar os ajustes
   - Após o @engineer efetuar os ajustes, acionar o agente @qa 
- **Ação:** Não havendo necessidade de ajustes:
   - Prosseguir para o passo 6 (finalização)

### Passo 6: Finalização
- **Ação:** Informe ao usuário: "Revisão concluída no PR #{{PR_ID}}. Você pode conferir os comentários diretamente no GitHub." (ou indicar que a revisão foi apresentada no chat devido a limitações de autenticação).