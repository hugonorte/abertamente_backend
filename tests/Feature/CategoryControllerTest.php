<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_deve_criar_categoria_com_dados_invalidos(): void
    {
        $dados = [
            'name' => 12,
        ];

        $response = $this->postJson('/api/category', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function deve_criar_um_categoria_com_dados_validos(): void
    {
        $dados = [
            'name' => 'Psicologia',
        ];

        $response = $this->postJson('/api/category', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'name' => 'Psicologia',
        ]);
    }



        #[Test]
        public function nao_deve_permitir_categorias_duplicadas(): void
        {
            category::factory()->create(['name' => 'Psicologia']);

            $dados = [
                'name' => 'Psicologia',
            ];

            $response = $this->postJson('/api/category', $dados);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['name']);
        }



    #[Test]
    public function deve_listar_todos_as_categorias(): void
    {
        // Arrange — cria 3 autores no banco
        $categoria = category::factory()->count(3)->create();

        // Act — faz requisição GET para /api/autores
        $response = $this->getJson('/api/category');

        // Assert — valida o status e o conteúdo
        $response->assertStatus(200);

        // Verifica que o JSON contém pelo menos um dos autores criados
        $response->assertJsonFragment([
            'name' => $categoria->first()->name,
        ]);
    }



   #[Test]
   public function deve_exibir_uma_categoria_existente(): void
   {
       // Arrange — cria uma categoria no banco
       $categoria = category::factory()->create([
           'name' => 'Psicologia',
       ]);

       // Act — faz a requisição GET para /api/category/{id}
       $response = $this->getJson("/api/category/{$categoria->id}");

       // Assert
       $response->assertStatus(200);
       $response->assertJsonFragment([
           'name' => 'Psicologia',
       ]);
   }



    #[Test]
    public function deve_retornar_404_se_categoria_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/category/999');

        // Assert
        $response->assertStatus(404);
    }


 #[Test]
 public function deve_atualizar_um_categoria_existente(): void
 {
     // Arrange — cria um usuário
     $categoria = category::factory()->create([
         'name' => 'Hugo',
     ]);

     $dadosAtualizados = [
         'name' => 'Hugo Atualizado',
     ];

     // Act — requisição PUT
     $response = $this->putJson("/api/category/{$categoria->id}", $dadosAtualizados);

     // Assert
     $response->assertStatus(200);

     $this->assertDatabaseHas('categories', [
         'name' => 'Hugo Atualizado',
     ]);

     $response->assertJsonFragment([
         'name' => 'Hugo Atualizado',
     ]);
 }


   #[Test]
   public function deve_retornar_404_se_usuario_para_update_nao_existir(): void
   {
       $dados = [
           'name' => 'Teste',
       ];

       $response = $this->putJson('/api/category/999', $dados);

       $response->assertStatus(404);
   }


    #[Test]
    public function nao_deve_permitir_atualizar_para_nome_duplicado(): void
    {
        // Dois usuários no banco
        $user1 = category::factory()->create(['name' => 'Psicologia']);
        $user2 = category::factory()->create(['name' => 'Medicina']);

        $dados = [
            'name' => 'Psicologia',
        ];

        $response = $this->putJson("/api/category/{$user2->id}", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    #[Test]
    public function deve_excluir_um_usuario_existente(): void
    {
        // Arrange — cria uma categoria
        $categoria = category::factory()->create();

        // Act — requisição DELETE
        $response = $this->deleteJson("/api/category/{$categoria->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Categoria excluída com sucesso']);

        // Verifica que não existe mais no banco
        $this->assertSoftDeleted('categories', [
            'id' => $categoria->id,
        ]);
    }


    #[Test]
    public function deve_retornar_404_se_categoria_para_delete_nao_existir(): void
    {
        // Act — requisição DELETE para ID inexistente
        $response = $this->deleteJson('/api/category/9999');

        // Assert
        $response->assertStatus(404);
    }
}
