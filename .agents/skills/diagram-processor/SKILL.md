---
name: diagram-processor
description: Interpreta fluxogramas (Excalidraw/JSON ou Mermaid) para extrair lógica de negócio e gerar planos de implementação.
---

# Diagram Processor Skill

Esta skill capacita o agente a transformar representações visuais de lógica de negócio em especificações técnicas e código.

## Fluxo de Trabalho

1.  **Localização dos Arquivos**: Os diagramas devem ser armazenados preferencialmente em `docs/diagrams/`.
2.  **Formatos Suportados**:
    *   `.excalidraw` ou `.json`: Interpretado como estrutura de objetos do Excalidraw.
    *   `.mmd` ou blocos de código `mermaid`: Interpretado como grafos de fluxo.
3.  **Processo de Interpretação**:
    *   Ao ler um arquivo `.excalidraw`, o agente deve buscar por elementos do tipo `text` e suas conexões (`arrows`) para reconstruir o fluxo lógico.
    *   O agente deve identificar pontos de decisão (losangos ou textos com interrogação) e ramificações.
4.  **Geração de Saída**:
    *   O agente deve mapear cada "caixa" do fluxo para uma funcionalidade, endpoint ou método.
    *   Deve-se gerar um `implementation_plan.md` que referencie o diagrama como fonte da verdade.

## Instruções para o Usuário

Para solicitar uma implementação baseada em um diagrama:
1.  Salve seu diagrama em `docs/diagrams/nome-do-fluxo.excalidraw`.
2.  Peça ao agente: *"Implemente a lógica de [X] descrita no arquivo docs/diagrams/nome-do-fluxo.excalidraw"*.
3.  O agente lerá o JSON, entenderá as condições e passos, e proporá o plano.
