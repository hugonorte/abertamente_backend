<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\BibliographicReference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BibliographicReferenceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_deve_criar_ref_bibliografica_com_dados_invalidos(): void
    {
        //Cria um post com dados válidos (porque o post_id é obrigatório e deve existir na tabela referencias_bibliográficas)
        $post = Post::factory()->create();

        $dados = [
            'post_id' => $post->getKey(),
            'description' => 123, // Inválido, deve ser string com pelo menos 10 caracteres
        ];

        $response = $this->postJson('/api/bibliographic_reference', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
    }

    #[Test]
    public function deve_criar_um_ref_bibliografica_com_dados_validos(): void
    {
        //Cria um post com dados válidos (porque o post_id é obrigatório e deve existir na tabela bibliographic_references)
        $post = Post::factory()->create();

        $dados = [
            'post_id' => $post->getKey(),
            'description' => "Esta é uma descrição válida para a referência bibliográfica.",
        ];

        $response = $this->postJson('/api/bibliographic_reference', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bibliographic_references', [
            'description' => "Esta é uma descrição válida para a referência bibliográfica.",
        ]);
    }

    #[Test]
    public function nao_deve_permitir_referencias_bibliograficas_duplicados_para_um_mesmo_post(): void
    {
        // Arrange
        $post = Post::factory()->create();

        BibliographicReference::factory()->create([
            'post_id' => $post->getKey(),
            'description' => 'Referência original que deve ser salva.'
        ]);

        $dadosDuplicados = [
            'post_id' => $post->getKey(),
            'description' => 'Referência original que deve ser salva.'
        ];

        // Act
        $response = $this->postJson('/api/bibliographic_reference', $dadosDuplicados);

        // Assert
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['description']);
    }

    #[Test]
    public function deve_listar_todos_as_referencias_bibliograficas(): void
    {
        // Arrange — cria 3 referências bibliográficas no banco
        /** @var Collection<int, BibliographicReference> $bibliographicReferences */
        $bibliographicReferences = BibliographicReference::factory()->count(3)->create();

        // Act — faz requisição GET para /api/bibliographic_references
        $response = $this->getJson('/api/bibliographic_reference');

        // Assert — valida o status e o conteúdo
        $response->assertStatus(200);

        // Verifica que o JSON contém pelo menos uma das descrições criadas
        $response->assertJsonFragment([
            'description' => $bibliographicReferences->first()->description,
        ]);
    }

    #[Test]
    public function deve_exibir_um_ref_bibliografica_existente(): void
    {
        // Arrange — cria uma categoria no banco
        $bibliographicReference = BibliographicReference::factory()->create([
            'description' => 'Teste de Referência Bibliográfica',
        ]);

        // Act — faz a requisição GET para /api/category/{id}
        $response = $this->getJson('/api/bibliographic_reference/'.$bibliographicReference->getKey());

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'description' => 'Teste de Referência Bibliográfica'
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_ref_bibliografica_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/bibliographic_reference/999');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_ref_bibliografica_existente(): void
    {
        // Arrange — cria um usuário
        $bibliographicReference = BibliographicReference::factory()->create([
            'description' => 'Teste de Referência Bibliográfica',
        ]);

        $dadosAtualizados = [
            'post_id' => $bibliographicReference->post_id,
            'description' => 'Teste de Referência Bibliográfica atualizada',
        ];

        // Act — requisição PUT
        $response = $this->putJson("/api/bibliographic_reference/$bibliographicReference->id", $dadosAtualizados);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('bibliographic_references', [
            'description' => 'Teste de Referência Bibliográfica atualizada',
        ]);

        $response->assertJsonFragment([
            'description' => 'Teste de Referência Bibliográfica atualizada',
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_ref_bibliografica_para_update_nao_existir(): void
    {
        // Arrange — cria um usuário
        $bibliographicReference = BibliographicReference::factory()->create();

        $dados = [
            'post_id' => $bibliographicReference->post_id,
            'description' => 'Teste de Referência Bibliográfica atualizada',
        ];

        $response = $this->putJson('/api/bibliographic_reference/999', $dados);

        $response->assertStatus(404);
    }

    #[Test]
    public function nao_deve_permitir_atualizar_referencia_bibliografica_para_um_post_e_descricao_que_ja_existem_para_evitar_dados_duplicados(): void
    {
        // Dois usuários no banco
        $bibliographicReference1 = BibliographicReference::factory()->create(['description' => 'Teste de Referência Bibliográfica']);
        $bibliographicReference2 = BibliographicReference::factory()->create(['description' => 'Outra referência bibliográfica']);

        $dados = [
            'post_id' => $bibliographicReference1->post_id,
            'description' => 'Teste de Referência Bibliográfica',
        ];

        $response = $this->putJson("/api/bibliographic_reference/$bibliographicReference2->id", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
    }


    #[Test]
    public function deve_excluir_um_ref_bibliografica_existente(): void
    {
        // Arrange — cria uma categoria
        $bibliographicReference = BibliographicReference::factory()->create();

        // Act — requisição DELETE
        $response = $this->deleteJson("/api/bibliographic_reference/$bibliographicReference->id");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Referência bibliográfica excluída com sucesso']);

        // Verifica que não existe mais no banco
        $this->assertDatabaseMissing('bibliographic_references', [
            'id' => $bibliographicReference->id,
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_ref_bibliografica_para_delete_nao_existir(): void
    {
        // Act — requisição DELETE para ID inexistente
        $response = $this->deleteJson('/api/bibliographic_reference/9999');

        // Assert
        $response->assertStatus(404);
    }
}
