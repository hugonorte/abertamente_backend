---
description: Executa apenas o papel de QA Engineer (@qa) para auditar e corrigir o código.
---

Quando o usuário digitar `/qa`, orquestre o papel de **QA Engineer (@qa)** seguindo estritamente as definições em `.agents/agents.md`.

### Sequência de Execução:
1. **Auditoria de Código**: Execute a skill `audit_code.md` para analisar o código gerado pelo Engenheiro e compará-lo com os arquivos na pasta .agents/rules.
2. **Bug Hunting**: Procure agressivamente por dependências ausentes nas configurações, promessas não tratadas (async), erros de sintaxe e bugs lógicos.
3. **Prevenção de Quebra de Compilação e Testes (Crítico)**: Verifique se houve mudanças em assinaturas de métodos ou adição de dependências em construtores. Quando houver, sempre identifique e atualize os testes unitários onde essas classes são instanciadas.
4. **Verificação RIGOROSA de TDD (Sem Exceções)**: Audite o processo do `@engineer`. Se o `@engineer` não tiver seguido a metodologia TDD (criado o teste PRIMEIRO, falhado na fase RED, e só depois implementado o código GREEN), **REJEITE O CÓDIGO IMEDIATAMENTE**. Não há brechas. Se o código funcional foi escrito sem um teste prévio falhando, o `@engineer` violou a política central do projeto.
4. **Correção Proativa**: Aponte para o agente com papel @engineer para que o agente @engineer corrija diretamente qualquer falha encontrada, sobrescrevendo os arquivos necessários no diretório `app/`.
5. **Resumo da Auditoria**: Ao finalizar, apresente ao usuário um resumo dos problemas encontrados e das correções realizadas.