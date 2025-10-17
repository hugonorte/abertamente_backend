<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_deve_criar_usuario_com_dados_invalidos(): void
    {
        $dados = [
            'name' => 'Hugo',
            'password' => '123456'
        ];

        $response = $this->postJson('/api/author', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function deve_criar_um_author_com_dados_validos(): void
    {
        $dados = [
            'name' => 'Hugo Norte',
            'email' => 'hugo@example.com',
            'bio' => 'Cientista da Computação',
            'main_title' => 'Bacharel em Ciência da Computação',
            'preferred_social_network' => 'Twitter',
            'preferred_social_network_username' => '@hugonorte',
        ];

        $response = $this->postJson('/api/author', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('authors', [
            'name' => 'Hugo Norte',
            'email' => 'hugo@example.com',
            'bio' => 'Cientista da Computação',
        ]);

        $response->assertJsonFragment([
            'name' => 'Hugo Norte',
            'email' => 'hugo@example.com',
            'bio' => 'Cientista da Computação',
        ]);
    }

    #[Test]
    public function nao_deve_permitir_authors_com_emails_duplicados(): void
    {
        Author::factory()->create(['email' => 'duplicado@example.com']);

        $dados = [
            'name' => 'Hugo Norte',
            'email' => 'duplicado@example.com',
            'bio' => 'Cientista da Computação',
            'main_title' => 'Bacharel em Ciência da Computação',
            'preferred_social_network' => 'Twitter',
            'preferred_social_network_username' => '@hugonorte',
        ];

        $response = $this->postJson('/api/author', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function deve_listar_todos_os_autores(): void
    {
        // Arrange — cria 3 autores no banco
        $author = Author::factory()->count(3)->create();

        // Act — faz requisição GET para /api/autores
        $response = $this->getJson('/api/author');

        // Assert — valida o status e o conteúdo
        $response->assertStatus(200);

        // Verifica que o JSON contém pelo menos um dos autores criados
        $response->assertJsonFragment([
            'email' => $author->first()->email,
        ]);
    }

    #[Test]
    public function deve_exibir_um_autor_existente(): void
    {
        // Arrange — cria um usuário no banco
        $author = Author::factory()->create([
            'name' => 'Hugo Norte',
            'email' => 'hugo@example.com',
        ]);

        // Act — faz a requisição GET para /api/author/{id}
        $response = $this->getJson("/api/author/{$author->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Hugo Norte',
            'email' => 'hugo@example.com',
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_autor_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/author/999');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_autor_existente(): void
    {
        // Arrange — cria um usuário
        $author = Author::factory()->create([
            'name' => 'Hugo',
            'email' => 'hugo@example.com',
        ]);

        $dadosAtualizados = [
            'name' => 'Hugo Atualizado',
            'email' => 'hugoatualizado@example.com',
        ];

        // Act — requisição PUT
        $response = $this->putJson("/api/author/{$author->id}", $dadosAtualizados);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'name' => 'Hugo Atualizado',
            'email' => 'hugoatualizado@example.com',
        ]);

        $response->assertJsonFragment([
            'name' => 'Hugo Atualizado',
            'email' => 'hugoatualizado@example.com',
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_usuario_para_update_nao_existir(): void
    {
        $dados = [
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'bio' => 'Teste',
            'main_title' => 'Teste',
            'preferred_social_network' => 'Snapchat',
            'preferred_social_network_username' => '@teste',
        ];

        $response = $this->putJson('/api/author/999', $dados);

        $response->assertStatus(404);
    }

    #[Test]
    public function nao_deve_permitir_atualizar_para_email_duplicado(): void
    {
        // Dois usuários no banco
        $user1 = Author::factory()->create(['email' => 'usuario1@example.com']);
        $user2 = Author::factory()->create(['email' => 'usuario2@example.com']);

        $dados = [
            'name' => 'Hugo Norte',
            'email' => 'usuario1@example.com',
            'bio' => 'Cientista da Computação',
            'main_title' => 'Bacharel em Ciência da Computação',
            'preferred_social_network' => 'Twitter',
            'preferred_social_network_username' => '@hugonorte',
        ];

        $response = $this->putJson("/api/author/{$user2->id}", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function deve_excluir_um_usuario_existente(): void
    {
        // Arrange — cria um usuário
        $author = Author::factory()->create();

        // Act — requisição DELETE
        $response = $this->deleteJson("/api/author/{$author->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Usuário excluído com sucesso']);

        // Verifica que não existe mais no banco
        $this->assertSoftDeleted('authors', [
            'id' => $author->id,
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_usuario_para_delete_nao_existir(): void
    {
        // Act — requisição DELETE para ID inexistente
        $response = $this->deleteJson('/api/author/9999');

        // Assert
        $response->assertStatus(404);
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
