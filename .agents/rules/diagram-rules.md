---
trigger: always_on
---

# Descrição do Contexto para Agentes
**Propósito:** Define o workflow obrigatório para a interpretação e implementação de lógica de negócio baseada em fluxogramas. Esta regra deve ser consultada sempre que um arquivo em `docs/diagrams/` for referenciado.

---

## Cabeçalho
- **Aplicabilidade**: Esta regra é ativada quando a tarefa envolve arquivos `.excalidraw`, `.json` (Excalidraw), `.mmd` ou sintaxe `mermaid` dentro da pasta `docs/diagrams/`.
- **Objetivo**: Garantir que a implementação técnica seja uma tradução fiel da lógica visual definida nos diagramas, mantendo a integridade das regras de negócio.

## Lista de Instruções
1.  **Prioridade do Diagrama**: O fluxograma é a "Fonte da Verdade". Em caso de conflito entre o código existente e o diagrama, a lógica do diagrama prevalece.
2.  **Uso da Skill `diagram-processor`**: Utilize obrigatoriamente a skill `diagram-processor` para extrair os nós de texto, conexões e diamantes de decisão.
3.  **Análise de Ramificações**: Identifique todos os fluxos alternativos (caminhos de "NÃO" ou erro) e garanta que cada um tenha um tratamento correspondente no código.
4.  **Referência no Plano**: Todo `implementation_plan.md` gerado deve conter um link para o arquivo do diagrama e uma breve explicação de como os fluxos visuais foram mapeados para funções ou componentes.
5.  **Mapeamento Estrito**: 
    - Textos em caixas retangulares -> Ações, métodos ou chamadas de API.
    - Textos em diamantes/losangos -> Condicionais (if/else), validações de schema ou guardas de rota.
    - Setas/Conexões -> Fluxo de execução ou transições de estado.
6.  **Logs e Auditoria**: Preste atenção especial a seções de logs ou auditoria (comumente identificadas por cores ou labels específicas) e implemente-as conforme descrito no diagrama.
7.  **Feedback de Inconsistência**: Se encontrar uma lógica impossível ou contraditória no diagrama, pare e peça clarificação ao usuário antes de prosseguir.
8. **Segurança e Roles (RBAC)**: Verificações de permissão no diagrama (ex: "Usuário tem acesso?") devem ser implementadas respeitando os níveis de acesso (Roles) do sistema.
9. **Rastreabilidade**: Fluxos que alteram dados sensíveis ou oficiais (Ativos, Atas, Comuneros) devem garantir que o histórico de modificações seja disparado, conforme as regras de auditoria do projeto.
10. **Execute estritamente o que está no diagrama, caso queira sugerir algo que não esteja no diagrama, antes de adicionar as regras de negócio no arquivo '.agents/rules/business-rules.md', crie o implementation-plan da tarefa e pergunte ao usuário e aguarde a resposta do usuário.