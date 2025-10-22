<?php

namespace Tests\Feature;

use App\Models\Footnote;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FootnoteControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_deve_criar_footnote_com_dados_invalidos(): void
    {
        //Cria um post com dados válidos (porque o post_id é obrigatório e deve existir na tabela referencias_bibliográficas)
        $post = Post::factory()->create();

        $dados = [
            'post_id' => $post->getKey(),
            'description' => 123, // Inválido, deve ser string com pelo menos 10 caracteres
        ];

        $response = $this->postJson('/api/footnote', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
    }


    #[Test]
    public function deve_criar_um_ref_footnote_validos(): void
    {
        //Cria um post com dados válidos (porque o post_id é obrigatório e deve existir na tabela bibliographic_references)
        $post = Post::factory()->create();

        $dados = [
            'post_id' => $post->getKey(),
            'description' => "Esta é uma descrição válida para a referência bibliográfica.",
        ];

        $response = $this->postJson('/api/footnote', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('footnotes', [
            'description' => "Esta é uma descrição válida para a referência bibliográfica.",
        ]);
    }

    #[Test]
    public function nao_deve_permitir_footnote_duplicados_para_um_mesmo_post(): void
    {
        // Arrange
        $post = Post::factory()->create();

        Footnote::factory()->create([
            'post_id' => $post->getKey(),
            'description' => 'Referência original que deve ser salva.'
        ]);

        $dadosDuplicados = [
            'post_id' => $post->getKey(),
            'description' => 'Referência original que deve ser salva.'
        ];

        // Act
        $response = $this->postJson('/api/footnote', $dadosDuplicados);

        // Assert
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['description']);
    }


   #[Test]
   public function deve_listar_todos_os_footnotes(): void
   {
       // Arrange — cria 3 referências bibliográficas no banco
       $footnotes = Footnote::factory()->count(3)->create();

       // Act — faz requisição GET para /api/footnotes
       $response = $this->getJson('/api/footnote');

       // Assert — valida o status e o conteúdo
       $response->assertStatus(200);

       // Verifica que o JSON contém pelo menos uma das descrições criadas
       $response->assertJsonFragment([
           'description' => $footnotes->first()->description,
       ]);
   }

    #[Test]
    public function deve_exibir_um_footnote_existente(): void
    {
        // Arrange — cria uma categoria no banco
        $footnote = Footnote::factory()->create([
            'description' => 'Teste de Footnote',
        ]);

        // Act — faz a requisição GET para /api/category/{id}
        $response = $this->getJson('/api/footnote/'.$footnote->getKey());

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'description' => 'Teste de Footnote'
        ]);
    }
    #[Test]
    public function deve_retornar_404_se_ref_bibliografica_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/footnote/999');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_footnote_existente(): void
    {
        // Arrange — cria um usuário
        $footnote = Footnote::factory()->create([
            'description' => 'Teste de Footnote',
        ]);

        $dadosAtualizados = [
            'post_id' => $footnote->post_id,
            'description' => 'Teste de Footnote atualizada',
        ];

        // Act — requisição PUT
        $response = $this->putJson("/api/footnote/$footnote->id", $dadosAtualizados);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('footnotes', [
            'description' => 'Teste de Footnote atualizada',
        ]);

        $response->assertJsonFragment([
            'description' => 'Teste de Footnote atualizada',
        ]);
    }


   #[Test]
   public function deve_retornar_404_se_footnote_para_update_nao_existir(): void
   {
       // Arrange — cria um usuário
       $footnote = Footnote::factory()->create();

       $dados = [
           'post_id' => $footnote->post_id,
           'description' => 'Teste de Footnote atualizada',
       ];

       $response = $this->putJson('/api/footnote/999', $dados);

       $response->assertStatus(404);
   }

   #[Test]
   public function nao_deve_permitir_atualizar_footnote_para_um_post_e_descricao_que_ja_existem_para_evitar_dados_duplicados(): void
   {
       // Dois usuários no banco
       $footnote1 = Footnote::factory()->create(['description' => 'Teste de Footnote']);
       $footnote2 = Footnote::factory()->create(['description' => 'Outra referência bibliográfica']);

       $dados = [
           'post_id' => $footnote1->post_id,
           'description' => 'Teste de Footnote',
       ];

       $response = $this->putJson("/api/footnote/$footnote2->id", $dados);

       $response->assertStatus(422);
       $response->assertJsonValidationErrors(['description']);
   }

   #[Test]
   public function deve_excluir_um_footnote(): void
   {
       // Arrange — cria uma categoria
       $footnote = Footnote::factory()->create();

       // Act — requisição DELETE
       $response = $this->deleteJson("/api/footnote/$footnote->id");

       // Assert
       $response->assertStatus(200);
       $response->assertJsonFragment(['message' => 'Footnote excluído com sucesso']);

       // Verifica que não existe mais no banco
       $this->assertDatabaseMissing('footnotes', [
           'id' => $footnote->id,
       ]);
   }

   #[Test]
   public function deve_retornar_404_se_footnote_para_delete_nao_existir(): void
   {
       // Act — requisição DELETE para ID inexistente
       $response = $this->deleteJson('/api/footnote/9999');

       // Assert
       $response->assertStatus(404);
   }
}
