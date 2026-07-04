# CI/CD para Laravel e Deploy via rsync

Este documento detalha o workflow de Integração Contínua e Entrega Contínua (CI/CD) configurado para este projeto Laravel (PHP e MySQL).

## Objetivo
O objetivo deste pipeline é automatizar os processos de:
1. Validar e rodar testes da aplicação Laravel em cada `push` e `pull_request`.
2. Fazer Deploy para um servidor (ex: Hostinger) via `rsync` **apenas** após um Pull Request ser mesclado na branch `master` ou via execução manual.
3. Usar jobs separados para `build/test` e `deploy` para lidar com permissões e separação de responsabilidades.
4. **Implementar um mecanismo de repetição automática** para lidar com falhas de rede transitórias com o servidor.

## Estratégia de Git Flow
Este projeto segue um modelo de ramificação inspirado no Git Flow:
- **master**: Reflete o código em produção. Deploys automáticos acontecem a partir desta branch.
- **Outras branches**: Desenvolvimento de features e correções. Devem ser fundidas na `master` via Pull Request.

## O Workflow (`.github/workflows/deploy.yml`)

O pipeline é dividido em dois jobs principais: `build-test` e `deploy`.

### Job 1: Build & Test
Este job roda em cada push ou pull request para validar que o código está íntegro e os testes passam.
1. **Checkout**: Obtém o código fonte.
2. **Setup PHP**: Configura o ambiente PHP com a versão adequada e extensões necessárias (ex: pdo_mysql).
3. **Instalação do Composer**: Executa `composer install --prefer-dist --no-progress --no-suggest` para instalar as dependências do backend.
4. **Setup Node.js & NPM** (Opcional): Instala dependências do frontend e compila os assets usando Vite/Mix, caso existam no mesmo repositório.
5. **Configuração de Ambiente**: Copia o `.env.example` para `.env` e gera a chave da aplicação com `php artisan key:generate`.
6. **Lint & Análise Estática**: Executa ferramentas como PHPStan ou Laravel Pint para verificar padrões de código.
7. **Testes**: Executa os testes automatizados com `php artisan test`.
8. **Upload Artifact**: Salva o build (incluindo o diretório `vendor` compilado) para ser usado pelo job de deploy, evitando refazer a instalação no destino.

### Job 2: Deploy
Este job só roda na branch `master` e após o sucesso do job de build-test.
1. **Download Artifact**: Recupera os arquivos preparados no job anterior.
2. **Deploy via rsync**: Sincroniza os arquivos com o servidor destino.
   - Utiliza `shimataro/ssh-key-action` para gerenciar as chaves SSH.
   - O comando `rsync` é executado com flags para preservar permissões e deletar arquivos obsoletos no destino (ignorando pastas como `storage/` e `.env`).
3. **Pós-Deploy**: Conecta no servidor via SSH para executar comandos pós-deploy, como `php artisan migrate --force`, `php artisan optimize:clear` e `php artisan queue:restart`.

## Configuração de Secrets
Para que o deploy funcione, as seguintes secrets devem estar configuradas no GitHub:
- `SERVER_SSH_KEY`: A chave privada SSH com acesso ao servidor.
- `SERVER_IP`: IP ou hostname do servidor.
- `SERVER_SSH_PORT`: Porta SSH.
- `SERVER_USERNAME`: Usuário do SSH.
- `SERVER_REMOTE_PATH`: Caminho no servidor onde os arquivos devem ser colocados.

## Como Acionar Manualmente
Você pode disparar o deploy manualmente através da aba **Actions** no GitHub, selecionando o workflow correspondente e clicando em "Run workflow".
