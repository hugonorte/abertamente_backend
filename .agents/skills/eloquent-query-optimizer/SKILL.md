---
name: eloquent-query-optimizer
description: Analisa métodos e consultas Collections/Query Builder do Eloquent para identificar gargalos de performance, problemas de N+1, falta de paginação e uso desnecessário de tracking de memória. Use quando for solicitado para revisar código de acesso a dados ou otimizar consultas lentas em PHP.
---

# Otimizador de Consultas do Eloquent

## Quando usar esta skill

- Quando o usuário pedir para revisar uma classe Repository ou um Controller que faz chamadas ao banco.
- Quando o usuário reclamar que uma rota da API está lenta.
- Quando você identificar operações de banco de dados dentro de loops (for, foreach).

## Metodologia de Análise e Refatoração (Passo a Passo)

Sempre que atuares com esta skill, aplica o seguinte raciocínio sobre o código PHP:

1. _Caça ao N+1:_ Verifica se existem loops iterando sobre entidades onde propriedades de navegação (relacionamentos) são acessadas sem terem sido carregadas previamente.
   - Solução: Refatora a consulta original usando `with()` (ou `load()` se a coleção já existir) para fazer o eager loading.
2. _Avaliação de Hidratação:_ Pergunta a ti mesmo: "Vou precisar das instâncias de Model para usar métodos, mutators ou salvar no banco?".
   - Solução: Se for apenas para extrair dados brutos (ex: relatórios pesados ou listas enormes), adicione `toBase()` à query para evitar o custo de hidratação (instanciação dos objetos Model), ou use Query Builder (`DB::table()`).
3. _Over-fetching (Excesso de Dados):_ A consulta está trazendo todas as colunas da tabela (`SELECT *`), mas a API só precisa de duas ou três propriedades?
   - Solução: Substitui o carregamento completo por uma seleção específica usando `select('id', 'name')` ou, para buscar um array de uma única coluna, use `pluck('name')`.
4. _Paginação e Limites:_ Consultas que retornam listas (`get()`) sem filtros de limite de um grande conjunto de dados.
   - Solução: Sugere ou implementa `paginate()`, `simplePaginate()`, ou uso de `cursor()` / `chunk()` para processamento em massa. Para pegar top records, use `limit()` e `offset()`.

## Como responder

Quando aplicares esta skill, reescreve o código otimizado e explica brevemente em tópicos _o que_ mudaste e _quantos milissegundos ou memória_ a mudança vai poupar teoricamente.
