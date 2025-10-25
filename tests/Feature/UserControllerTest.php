<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function deve_criar_um_usuario_com_dados_validos(): void
    {
        $dados = [
            'first_name' => 'Hugo',
            'last_name' => 'Norte',
            'email' => 'hugo@example.com',
            'password' => 'senha123',
            'role' => UserRole::User->value,
        ];

        $response = $this->postJson('/api/user', $dados);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'first_name' => 'Hugo',
            'last_name' => 'Norte'
        ]);

        $response->assertJsonFragment([
            'first_name' => 'Hugo',
            'last_name' => 'Norte'
        ]);

        // Senha não deve estar visível na resposta
        $response->assertJsonMissing(['password' => 'senha123']);
    }

    #[Test]
    public function nao_deve_criar_usuario_com_dados_invalidos(): void
    {
        $dados = [
            'first_name' => 'Hugo',
            'password' => '1234'
        ];

        $response = $this->postJson('/api/user', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function nao_deve_permitir_emails_duplicados(): void
    {
        // Usuário inicial
        User::factory()->create(['email' => 'duplicado@example.com']);

        $dados = [
            'first_name' => 'Outro',
            'last_name' => 'Usuário',
            'email' => 'duplicado@example.com',
            'password' => 'senha123',
        ];

        $response = $this->postJson('/api/user', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function deve_listar_todos_os_usuarios_com_paginacao_e_resource(): void
    {
        // Arrange — cria 3 usuários no banco
        $users = User::factory()->count(3)->create();

        // Act — faz requisição GET para /api/user
        $response = $this->getJson('/api/user');

        // Assert — valida o status
        $response->assertStatus(200);

        // 1. Assert (Novo): Verifica a estrutura da paginação
        // Isso garante que o Resource e a paginação estão funcionando.
        $response->assertJsonStructure([
            'data' => [
                '*' => [ // Verifica a estrutura de CADA item dentro de 'data'
                    'id',
                    'first_name',
                    'last_name',
                    'full_name',
                    'role',
                    'role_label'
                    // Adicione/remova campos conforme definido no seu UserResource
                ]
            ],
            'links', // Verifica se a chave 'links' existe
            'meta'   // Verifica se a chave 'meta' existe
        ]);

        // 2. Assert (Novo): Verifica se há 3 itens DENTRO da chave 'data'
        $response->assertJsonCount(3, 'data');

        // 3. Assert (Atualizado): Verifica o conteúdo usando o caminho completo
        //    Usar assertJsonPath é mais preciso que assertJsonFragment aqui.
        /** @var User $primeiroUsuario */
        $primeiroUsuario  = $users->first();
        $response->assertJsonPath('data.0.id', $primeiroUsuario->id);
        $response->assertJsonPath(
            'data.0.full_name',
            $primeiroUsuario->first_name . ' ' . $primeiroUsuario->last_name
        );
    }

   #[Test]
   public function deve_exibir_um_usuario_existente(): void
   {
       // Arrange — cria um usuário no banco
       $user = User::factory()->create([
           'first_name' => 'Hugo',
           'last_name' => 'Norte',
           'email' => 'hugo@example.com',
       ]);

       // Act — faz a requisição GET para /api/user/{id}
       $response = $this->getJson("/api/user/$user->id");

       // Assert
       $response->assertStatus(200);

       $response->assertJsonPath('data.first_name', $user->first_name);
   }

    #[Test]
    public function deve_retornar_404_se_usuario_nao_existir(): void
    {
        // Act — requisita um ID inexistente
        $response = $this->getJson('/api/user/999');

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function deve_atualizar_um_usuario_existente(): void
    {
        // Arrange
        $adminUser = User::factory()->admin()->create();
        $user = User::factory()->create([
            'first_name' => 'Hugo',
            'last_name' => 'Norte',
            'email' => 'hugo@example.com',
        ]);

        $dadosAtualizados = [
            'first_name' => 'Hugo_atualizado',
            'last_name' => 'Norte_atualizado',
        ];

        // Act
        $response = $this->actingAs($adminUser)
            ->putJson("/api/user/$user->id", $dadosAtualizados);

        // Assert
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id, // Boa prática: garantir que é o user correto
            'first_name' => 'Hugo_atualizado',
            'last_name' => 'Norte_atualizado'
        ]);

        $response->assertJsonPath('data.first_name', 'Hugo_atualizado');
        $response->assertJsonPath('data.last_name', 'Norte_atualizado');
    }

    #[Test]
    public function nao_deve_permitir_atualizar_para_email_duplicado(): void
    {
        // Dois usuários no banco
        User::factory()->create(['email' => 'usuario1@example.com']);
        $user2 = User::factory()->create(['email' => 'usuario2@example.com']);

        $dados = [
            'first_name' => 'Hugo',
            'email' => 'usuario1@example.com', // email duplicado
            'password' => 'senha123',
        ];

        $response = $this->putJson("/api/user/$user2->id", $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function deve_excluir_um_usuario_existente_quando_autenticado_como_admin(): void
    {
        // Arrange
        $adminUser = User::factory()->admin()->create();
        $userToDelete = User::factory()->create();

        // Act
        $response = $this->actingAs($adminUser)
            ->deleteJson("/api/user/$userToDelete->id");

        // Assert
        $response->assertStatus(204);

        // Assert
        // Verifica que a linha ainda existe, mas foi marcada como "soft deleted"
        $this->assertSoftDeleted('users', [
            'id' => $userToDelete->id
        ]);

        // Opcional: Você também pode verificar que o $adminUser NÃO foi deletado
        $this->assertDatabaseHas('users', [
            'id' => $adminUser->id,
            'deleted_at' => null // Verifica se 'deleted_at' é NULO
        ]);
    }

    #[Test]
    public function deve_retornar_404_se_usuario_para_delete_nao_existir(): void
    {
        // Arrange
        $adminUser = User::factory()->admin()->create();

        // Act
        $response = $this->actingAs($adminUser)
            ->deleteJson("/api/user/9999");

        // Assert
        $response->assertStatus(404);
    }

    #[Test]
    public function test_conexao_ao_servidor_local(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
