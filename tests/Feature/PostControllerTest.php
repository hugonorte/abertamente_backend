<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function nao_deve_criar_post_com_dados_invalidos(): void
    {
        //Cria um author com dados válidos (porque o author_id é obrigatório e deve existir na tabela posts)
        $author = Author::factory()->create();
        //Cria uma categoria com dados válidos (porque o categoria_id é obrigatório e deve existir na tabela posts)
        $categoria = category::factory()->create();

        $dados = [
            'title' => 12,
            'tldr' => 'Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/post', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function deve_criar_um_post_com_dados_validos(): void
    {
        //Cria um author com dados válidos (porque o author_id é obrigatório e deve existir na tabela posts)
        $author = Author::factory()->create();
        //Cria uma categoria com dados válidos (porque o categoria_id é obrigatório e deve existir na tabela posts)
        $categoria = category::factory()->create();

        $dados = [
            'title' => 'Teste de Post',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/post', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Teste de Post',
            'tldr' => 'Teste de TLDR Ok',
        ]);
    }

    #[Test]
    public function nao_deve_permitir_posts_duplicados(): void
    {
        //Cria um author com dados válidos (porque o author_id é obrigatório e deve existir na tabela posts)
        $author = Author::factory()->create();
        //Cria uma categoria com dados válidos (porque o categoria_id é obrigatório e deve existir na tabela posts)
        $categoria = category::factory()->create();

        post::factory()->create(['title' => 'Psicologia duplicado']);

        $dados = [
            'title' => 'Psicologia duplicado',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/post', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function deve_listar_todos_os_posts(): void
    {
        // Arrange — cria 3 autores no banco
        $post = Post::factory()->count(3)->create();

        // Act — faz requisição GET para /api/posts
        $response = $this->getJson('/api/post');

        // Assert — valida o status e o conteúdo
        $response->assertStatus(200);

        // Verifica que o JSON contém pelo menos um dos autores criados
        $response->assertJsonFragment([
            'title' => $post->first()->title,
        ]);
    }

    #[Test]
    public function deve_exibir_um_post_existente(): void
    {
        // Arrange — cria uma categoria no banco
        $post = Post::factory()->create([
            'title' => 'Teste de Título do Blog',
        ]);

        // Act — faz a requisição GET para /api/category/{id}
        $response = $this->getJson("/api/post/$post->id");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Teste de Título do Blog',
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_post_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/post/999');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_post_existente(): void
    {
        // Arrange — cria um usuário
        $post = Post::factory()->create([
            'title' => 'Teste de Título do Blog',
        ]);
        $dadosAtualizados = [
            'title' => 'Teste de Título do Blog atualizado',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => $post->author_id,
            'category_id' => $post->category_id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        // Act — requisição PUT
        $response = $this->putJson("/api/post/$post->id", $dadosAtualizados);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', [
            'title' => 'Teste de Título do Blog atualizado',
        ]);

        $response->assertJsonFragment([
            'title' => 'Teste de Título do Blog atualizado',
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_post_para_update_nao_existir(): void
    {
        $dados = [
            'title' => 'Teste de Título do Blog',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => 1,
            'category_id' => 1,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        $response = $this->putJson('/api/post/999', $dados);

        $response->assertStatus(404);
    }

    #[Test]
    public function nao_deve_permitir_atualizar_para_titulo_duplicado(): void
    {
        // Dois usuários no banco
        $post1 = Post::factory()->create(['title' => 'Teste de Título do Blog']);
        $post2 = Post::factory()->create(['title' => 'Outro Teste de Título do Blog']);

        $dados = [
            'title' => 'Teste de Título do Blog',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => '/images/teste.jpg',
            'author_id' => 1,
            'category_id' => 1,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'active',
        ];

        $response = $this->putJson("/api/post/$post2->id", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function deve_excluir_um_post_existente(): void
    {
        // Arrange — cria uma categoria
        $post = Post::factory()->create();

        // Act — requisição DELETE
        $response = $this->deleteJson("/api/post/$post->id");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Post excluído com sucesso']);

        // Verifica que não existe mais no banco
        $this->assertSoftDeleted('posts', [
            'id' => $post->id,
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_post_para_delete_nao_existir(): void
    {
        // Act — requisição DELETE para ID inexistente
        $response = $this->deleteJson('/api/post/9999');

        // Assert
        $response->assertStatus(404);
    }
}
