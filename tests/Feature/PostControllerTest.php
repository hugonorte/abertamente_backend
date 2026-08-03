<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Services\GithubDeploymentService;
use Mockery;
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
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/post', $dados);

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
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $mock = Mockery::mock(GithubDeploymentService::class);
        $mock->shouldReceive('triggerFrontendDeployment')->once()->andReturn(true);
        $this->instance(GithubDeploymentService::class, $mock);

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/post', $dados);

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
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => $author->id,
            'category_id' => $categoria->id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/post', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function deve_listar_todos_os_posts(): void
    {
        // Arrange — cria 3 autores no banco
        /** @var Post $post */
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

        // Act
        $response = $this->getJson("/api/posts/$post->id");

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
        $response = $this->getJson('/api/posts/invalido');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_post_existente(): void
    {
        // Arrange — cria um usuário
        /** @var Post $post */
        $post = Post::factory()->create([
            'title' => 'Teste de Título do Blog',
            'status' => 'draft',
        ]);
        $dadosAtualizados = [
            'title' => 'Teste de Título do Blog atualizado',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => $post->author_id,
            'category_id' => $post->category_id,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $mock = Mockery::mock(GithubDeploymentService::class);
        $mock->shouldReceive('triggerFrontendDeployment')->once()->andReturn(true);
        $this->instance(GithubDeploymentService::class, $mock);

        // Act — requisição PUT
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->putJson("/api/post/$post->id", $dadosAtualizados);

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
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => 1,
            'category_id' => 1,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->putJson('/api/post/999', $dados);

        $response->assertStatus(404);
    }

    #[Test]
    public function nao_deve_permitir_atualizar_para_titulo_duplicado(): void
    {
        // Dois usuários no banco
        Post::factory()->create(['title' => 'Teste de Título do Blog']);
        $post2 = Post::factory()->create(['title' => 'Outro Teste de Título do Blog']);

        $dados = [
            'title' => 'Teste de Título do Blog',
            'tldr' => 'Teste de TLDR Ok',
            'content' => 'Teste de Conteúdo',
            'image_path' => UploadedFile::fake()->image('teste.jpg'),
            'author_id' => 1,
            'category_id' => 1,
            'published_at' => '2001-01-01 00:00:00',
            'status' => 'published',
        ];

        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->putJson("/api/post/$post2->id", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function deve_excluir_um_post_existente(): void
    {
        // Arrange — cria uma categoria
        $post = Post::factory()->create();

        // Act — requisição DELETE
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->deleteJson("/api/post/$post->id");

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
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->deleteJson('/api/post/invalido');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_exibir_o_post_publicado_pelo_slug_para_o_frontend(): void
    {
        // Arrange
        $post = Post::factory()->create([
            'title' => 'Meu Novo Post Teste',
            'slug' => 'meu-novo-post-teste',
            'status' => 'published',
        ]);

        // Act
        $response = $this->getJson("/api/post/published/{$post->slug}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Meu Novo Post Teste',
        ]);
    }
}
