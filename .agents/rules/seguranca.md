---
trigger: always_on
---

# Proteção de Informações Sensíveis

Esta regra visa prevenir o vazamento de dados sensíveis para o repositório Git.

## Regras de Segurança:

- **Não incluir Credenciais:** Nunca escreva senhas, tokens de API, segredos de clientes ou strings de conexão diretamente no código-fonte.
- **Uso de Variáveis de Ambiente:** Utilize o arquivo `.env` ou o gerenciador de segredos do ambiente para armazenar informações sensíveis.
- **Verificação Proativa:** Antes de finalizar qualquer tarefa que envolva criação ou edição de arquivos de configuração ou modelos, verifique se campos como `Password`, `Secret`, `Token` ou `Key` não possuem valores padrão expostos.
- **Sanitização de Logs:** Garanta que logs não capturem informações sensíveis dos usuários.
- **Dados de Teste:** Use apenas dados fictícios/mockados para testes e exemplos. Nunca use dados reais de usuários da base de produção.
