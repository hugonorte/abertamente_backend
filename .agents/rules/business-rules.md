# Regras de Negócio: Abertamente Qqahuac

Este documento descreve as regras de negócio de domínio do projeto Abertamente Qqahuac, servindo como guia para o desenvolvimento e manutenção das funcionalidades do sistema.

---

## 1. Gestão de Comuneros (Membros)

O sistema centraliza a gestão dos membros da comunidade (Comuneros).

- **Requisitos de Admissão**: Para ser um Comunero, o indivíduo deve geralmente ser um agricultor, residir na comunidade e cumprir com suas obrigações comunitárias.
- **Documentação Obrigatória**: A admissão requer comprovante de residência, solicitação formal de admissão e pagamento das taxas de registro.
- **Ciclo de Vida (Status)**:
    - **Período Probatório**: Novos membros podem passar por um período de teste com datas de início e fim definidas.
    - **Status do Comunero**: Trackeado via `ComuneroStatusEnum` (Ex: Ativo, Aposentado, etc.).
- **Vínculos Familiares**: O sistema registra informações de filiação (Pai e Mãe) e vínculos com cônjuges e dependentes.

## 2. Gestão de Patrimônio e Ativos (Assets)

O sistema permite o registro e monitoramento de bens pertencentes aos membros ou à comunidade.

- **Tipagem de Ativos**: Os ativos são classificados via `AssetTypeEnum` e incluem:
    - **Terrenos (Terrains)**: O bem principal, requerendo geolocalização (Latitude/Longitude), área em metros quadrados e valor estimado.
    - **Animais (Livestock)**: Registro de gado ou outros animais (via `AnimalModel`).
    - **Habitação (Houses)**: Registro de propriedades residenciais.
    - **Veículos (Automobiles)**: Registro de automóveis da comunidade.
- **Regras de Terrenos**:
    - Devem possuir um endereço vinculado ou coordenadas precisas.
    - Atributos como "Terreno de Irrigação" são fundamentais para a classificação produtiva.
- **Vínculo de Propriedade**: Todo ativo deve estar associado a um Usuário/Comunero responsável.

## 3. Escrituração e Governança (Books & Meetings)

A governança comunitária é documentada através de livros oficiais e registros de assembleias.

- **Livro de Transações/Atas (Books)**:
    - Os livros possuem tipos específicos (Ex: Livro de Atas, Livro de Transações).
    - Possuem data de abertura e fechamento, com um Presidente responsável designado.
    - Contêm "Páginas" que registram eventos cronológicos.
- **Assembleias e Reuniões (Meetings)**:
    - **Atas (Minutes)**: Cada reunião gera uma ata que documenta as decisões.
    - **Presença e Votação**: O sistema controla a presença de membros e permite a designação de "Suplentes" (Substitutes) quando um titular não pode votar.
    - **Status de Atas**: Atas podem estar em rascunho, aprovadas ou pendentes.

## 4. Auditoria e Rastreabilidade

Dado o caráter oficial dos registros comunitários, a integridade dos dados é crítica.

- **Histórico de Modificações**: Quase todas as entidades principais (Ativos, Comuneros, Livros, Páginas, Transações) possuem uma tabela de `ModificationHistory`. Toda alteração deve ser registrada para fins de auditoria.
- **Exclusão Lógica (Soft Delete)**: Entidades importantes não são excluídas fisicamente do banco de dados, mas sim marcadas como deletadas (`SoftDeletableEntity`), permitindo recuperação e histórico.
- **Histórico de Login e Mensagens**: O acesso ao sistema e comunicações internas são auditados para garantir segurança.

## 5. Pendências e Conformidade

- **Pendências do Sistema**: O sistema identifica automaticamente pendências de documentos ou obrigações não cumpridas (`SystemPendenciesModel`).
- **Justificativas**: Membros podem fornecer justificativas para pendências, que passam por um processo de avaliação.

## 6. Módulo Judicial, Sanções e Faenas

O sistema gerencia ocorrências comunitárias, condenações, multas e penalidades como parte da justiça comunal.

- **Ocorrências (`OccurrenceModel`)**:
    - Representam denúncias, fatos geradores de condenações automáticas ou fatos não previstos diretamente pelo estatuto.
    - Devem registrar título, descrição e data da ocorrência.
    - Podem vincular denunciante (`ComplainantId`), denunciado (`DefendantId`) e testemunha/certificador (`CertifierId`), todos opcionais conforme o caso.
    - Devem permitir denúncia anônima por meio de `IsAnonymous`.
    - Podem registrar local da ocorrência e evidência fotográfica/documental em `ImagePath`.
    - Dados sensíveis de ocorrências devem respeitar as regras de acesso por perfil/permissão.
- **Multas e Penalidades Estatutárias**:
    - `FineModel` representa modelos de multa com título obrigatório e valor monetário fixo.
    - `PenaltyModel` representa modelos de penalidade com título obrigatório e descrição.
    - Esses cadastros funcionam como dados mestres reutilizáveis em condenações.
- **Condenações (`ConvictionModel`)**:
    - Uma condenação sanciona um usuário/comunero (`ConvictedId`) e deve estar vinculada a uma ocorrência (`OccurrenceId`).
    - Pode estar vinculada a uma assembleia/reunião específica (`ConvictionMeetingId`) quando a decisão ocorrer formalmente nesse contexto.
    - Pode informar o juiz/responsável pela decisão (`JudgeId`) quando aplicável.
    - A condenação deve classificar o tipo da sanção via `PenaltyTypeEnum`: monetária (`Monetary`), comunitária (`Community`) ou outras (`Others`).
    - Uma condenação pode associar múltiplas multas e múltiplas penalidades por meio das tabelas `conviction_fines` e `conviction_penalties`.
    - A descrição da condenação pode ser nula quando o fato gerador já estiver suficientemente descrito na ocorrência.
- **Pendências de Penalidade (`PenaltyPendencyModel`)**:
    - Cada pendência representa uma obrigação ativa resultante de uma condenação.
    - Toda pendência deve estar vinculada a uma condenação (`ConvictionId`).
    - O status deve ser controlado por `PenaltyStatusEnum`: `Pending`, `Paid`, `Fulfilled` ou `Cancelled`.
    - Obrigações podem ser recorrentes (`IsRecurring`) e, quando recorrentes, devem usar `PeriodicityEnum` para definir a periodicidade.
    - Podem registrar data inicial, data final, data de vencimento, descrição e local de cumprimento.
- **Faenas (`FaenaModel`)**:
    - Representam jornadas de trabalho comunitário, podendo ser usadas como atividade comunitária ordinária ou como cumprimento de sanções comunitárias.
    - Devem registrar data, diretor da faena (`FaenaDirectorId`), resumo, pauta/agenda, local, recorrência e periodicidade.
    - Devem possuir identificador único (`FaenaUuid`) para apoiar fluxos como confirmação por QR Code, seguindo o padrão usado em assembleias/reuniões.
    - A presença em faenas é registrada por `FaenaAttendeeModel`, vinculando usuário, faena e confirmação de presença (`PresenceConfirmation`).
- **Relacionamentos e Integridade**:
    - Exclusões devem seguir o padrão de exclusão lógica das entidades do domínio.
    - Relações com usuários em ocorrências, condenações e faenas devem evitar deleção em cascata de usuários.
    - Ao excluir uma ocorrência ou condenação, as entidades dependentes devem respeitar as regras de integridade configuradas para não deixar pendências órfãs.

## 7. Documentação Visual e Fluxogramas

As regras de negócio complexas (como fluxos de aprovação e validações multi-etapas) podem ser definidas visualmente.

- **Fonte da Verdade**: Diagramas localizados em `docs/diagrams/` são considerados especificações válidas para a geração de planos de implementação.
- **Sincronização**: Qualquer alteração no código que mude a lógica de um fluxograma deve ser refletida na atualização do arquivo `.excalidraw` ou `.mmd` correspondente.

## 8. Formulários e Rascunhos (Auto-Save)

Para melhorar a experiência do usuário e evitar perda de dados durante o preenchimento de formulários complexos (como o Empadronamento), o sistema deve implementar salvamento automático (Auto-Save).

- **Salvamento Automático (Debounce)**: O frontend deve enviar os dados do formulário periodicamente e de forma assíncrona após um período de inatividade na digitação (Debounce, ex: 3 segundos).
- **Armazenamento Otimizado**: Rascunhos (`FormDraft`) devem ser salvos no banco de dados relacional principal de forma a minimizar o custo computacional, usando uma coluna do tipo JSON (`Payload`) para armazenar os dados flexíveis de cada tipo de formulário.
- **Identificação do Rascunho**: Cada rascunho é identificado exclusivamente pela combinação de Usuário (`UserId`) e Tipo de Formulário (`FormType`, ex: 'empadronamento').
- **Comportamento (UPSERT)**: O backend deve processar requisições de salvamento (ex: `PUT /api/drafts`) realizando uma operação de UPSERT (inserir se não existir, atualizar se já existir).
