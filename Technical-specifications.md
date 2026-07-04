# Especificações Técnicas - Recuperação de Senha (Backend)

Este documento descreve os requisitos para os agentes de backend implementarem o fluxo de "Esqueci minha senha" e "Redefinição de senha". O backend atual é construído em Laravel (PHP).

## Endpoints Necessários

### 1. Solicitação de Redefinição (Forgot Password)
- **Método / Rota:** `POST /api/auth/forgot-password`
- **Payload Esperado:** `{ "email": "usuario@exemplo.com" }`
- **Regras de Negócio e Segurança:**
  - O endpoint deve gerar um token seguro de redefinição de senha.
  - O token deve ser hasheado no banco de dados (nunca em texto puro).
  - Enviar um e-mail com o link contendo o token (o link base do frontend será: `https://[dominio-frontend]/redefinir-senha?token=xyz&email=abc`).
  - **Prevenção de Enumeração de Usuários:** Independentemente do e-mail existir no banco de dados ou não, o backend deve responder com um status HTTP `200 OK` e uma mensagem genérica (ex: `"Se o e-mail existir em nossa base de dados, um link de recuperação foi enviado."`). Isso impede que invasores descubram contas válidas.
  - **Rate Limiting:** Aplicar uma restrição rigorosa neste endpoint (ex: máximo de 3 tentativas por IP a cada 10 minutos) para evitar ataques de força bruta e spam de e-mails.
  - **Expiração:** O token deve expirar rapidamente (ex: 15 a 30 minutos).

### 2. Redefinição de Senha (Reset Password)
- **Método / Rota:** `POST /api/auth/reset-password`
- **Payload Esperado:** 
  ```json
  { 
    "email": "usuario@exemplo.com", 
    "token": "token-recebido-no-email", 
    "password": "nova-senha", 
    "password_confirmation": "nova-senha" 
  }
  ```
- **Regras de Negócio e Segurança:**
  - Validar se o token é válido, pertence ao e-mail fornecido e não está expirado.
  - Validar força da senha (mínimo de 8 caracteres).
  - Ao redefinir a senha com sucesso, o token deve ser imediatamente invalidado (deletado do banco).
  - Responder com `200 OK` e uma mensagem de sucesso em caso de êxito. Retornar `400 Bad Request` ou `422 Unprocessable Entity` em caso de falha de validação ou token inválido.

## Notas Adicionais
- Utilize as classes nativas do Laravel (`Password::broker()`, `ResetPassword` notification) caso sejam compatíveis com estes requisitos de segurança, ou implemente um fluxo customizado em uma tabela como `password_reset_tokens`.
- Nenhuma informação sensível (como o próprio token em texto puro) deve ser enviada nas respostas das APIs, apenas por e-mail para o usuário.
