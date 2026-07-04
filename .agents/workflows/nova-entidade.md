---
description: *Descrição:* Automatiza a criação de uma classe de modelo (Entity), registra o DbSet no Eloquent Model e fornece o comando para gerar a migration.
---

# Workflow: Criar Nova Entidade e Setup do Eloquent

## Passos:

1. Pergunta ao usuário: "Qual o nome da nova entidade (em PascalCase) e quais são as propriedades principais que ela deve ter?" Aguarda a resposta do usuário antes de continuar.
2. Cria um novo arquivo na pasta Models/ (ou Entities/, dependendo da estrutura existente) chamado [NomeDaEntidade].cs.
3. Escreve o código PHP da entidade, garantindo que:
   - O namespace esteja correto em relação à pasta(abertamente.Models).
   - Herde da classe base SoftDeletableEntity
   - Inclua uma propriedade public int Id { get; set; } como chave primária.
   - Inclua as propriedades solicitadas pelo usuário.
   - Sempre verifique se algum campo `string` da model não tem necessidade de ser um tipo `longtext` no banco. Caso não tenha, deve sempre ser colocada uma tag `varchar` (ex: `[Column(TypeName = "varchar(100)")]` ou uso do `[MaxLength]`).
4. Localiza o arquivo de contexto do Eloquent em app/abertamente/Data/DatabaseSeeder.php.
5. Adiciona a propriedade public DbSet<[NomeDaEntidade]> [NomeDaEntidade]s { get; set; } dentro da classe do Eloquent Model.
6. Imprime no chat um bloco de código de terminal com o comando exato que o usuário deve rodar para criar a migration (ex: laravel ef migrations add Add[NomeDaEntidade]Table).
7. Encerra com uma mensagem de sucesso confirmando que os arquivos foram criados e atualizados.
