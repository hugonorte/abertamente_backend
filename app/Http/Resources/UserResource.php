<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $email
 * @property mixed $role
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $this aqui é o objeto do modelo User
        return [
            // 1. Controle Total (Allow-list):
            // Só expomos o que está aqui. 'password' e 'remember_token'
            // são omitidos com segurança.
            'id' => $this->id,
            'first_name' => $this->first_name, // 2. Desacoplamento
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name, // 3. Transformação

            // 4. Lógica Condicional (se necessário)
            // 'email' só será incluído se o usuário logado for admin
            'email' => $this->when(
                $request->user() && $request->user()->hasRole(UserRole::Admin),
                $this->email
            ),

            // 5. Usando nosso Enum!
            // Podemos adicionar dados formatados que não existem na tabela.
            'role' => $this->role->value, // ex: 'user'
            'role_label' => $this->role->label(), // ex: 'Usuário'
        ];
    }
}
