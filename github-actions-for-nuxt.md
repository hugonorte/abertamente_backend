# CI/CD para Nuxt com pnpm e Deploy via rsync

Este documento detalha o workflow de Integração Contínua e Entrega Contínua (CI/CD) configurado para este projeto Nuxt.

## Objetivo
O objetivo deste pipeline é automatizar os processos de:
1. Builds the Nuxt application on every `push` and `pull_request`.
2. Deploys to a Hostinger server via `rsync` **only** after a Pull Request is merged into `master` or via a manual trigger.
3. Uses separate jobs for `build` and `deploy` to handle permissions and separation of concerns.
4. **Implements an automatic retry mechanism** to handle transient network failures with the server.

## Git Flow Strategy
Este projeto segue um modelo de ramificação inspirado no Git Flow:
- **master**: Reflete o código em produção. Deploys automáticos acontecem a partir desta branch.
- **Outras branches**: Desenvolvimento de features e correções. Devem ser fundidas na `master` via Pull Request.

## O Workflow (`.github/workflows/deploy.yml`)

O pipeline é dividido em dois jobs principais: `build` e `deploy`.

### Job 1: Build
Este job roda em cada push ou pull request para validar que o código está íntegro.
1. **Checkout**: Obtém o código fonte.
2. **Setup pnpm**: Instala o gerenciador de pacotes pnpm.
3. **Setup Node.js**: Configura o ambiente Node (v20+).
4. **Instalação**: Executa `pnpm install`.
5. **Lint**: Executa o linter para verificar padrões de código.
6. **Build**: Executa `pnpm run generate` para criar o build estático (SSG).
7. **Upload Artifact**: Salva a pasta `.output/public` para ser usada pelo job de deploy.

### Job 2: Deploy
Este job só roda na branch `master` e após o sucesso do job de build.
1. **Download Artifact**: Recupera os arquivos gerados no job anterior.
2. **Deploy via rsync**: Sincroniza os arquivos com o servidor Hostinger.
   - Utiliza `shimataro/ssh-key-action` para gerenciar as chaves SSH.
   - O comando `rsync` é executado com flags para preservar permissões e deletar arquivos obsoletos no destino.

## Configuração de Secrets
Para que o deploy funcione, as seguintes secrets devem estar configuradas no GitHub:
- `HOSTINGER_SSH_KEY`: A chave privada SSH com acesso ao servidor.
- `HOSTINGER_SERVER_IP`: IP ou hostname do servidor.
- `HOSTINGER_SSH_PORT`: Porta SSH (geralmente 65002 na Hostinger).
- `HOSTINGER_USERNAME`: Usuário do SSH.
- `HOSTINGER_REMOTE_PATH`: Caminho no servidor onde os arquivos devem ser colocados (ex: `public_html/`).

## Como Acionar Manualmente
Você pode disparar o deploy manualmente através da aba **Actions** no GitHub, selecionando o workflow "Build and Deploy" e clicando em "Run workflow".