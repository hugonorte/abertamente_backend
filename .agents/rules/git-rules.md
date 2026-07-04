# Regras de Git e Versionamento

Esta regra define restrições sobre operações de commit e push realizadas pelo agente.

## Restrições de Automação:

- **Proibição de Commit Automático:** O agente nunca deve executar o comando `git commit` sem que o usuário tenha solicitado explicitamente ou aprovado a mensagem de commit previamente.
- **Proibição de Push Automático:** O agente nunca deve executar o comando `git push` sem a permissão explícita do usuário.
- **Confirmação de Alterações:** Antes de realizar qualquer operação de versionamento, o agente deve resumir as alterações feitas para que o usuário possa revisá-las.
- **Fluxo de Trabalho:** O fluxo preferencial deve ser a criação das alterações no código e a solicitação ao usuário para que ele revise e decida o momento de commitar e pushar.
