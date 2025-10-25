<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 2. Tenta pegar o usuário da rota (ex: /api/user/{user})
        // Se for um POST (store), $user será null.
        // Se for um PUT/PATCH (update), $user será o modelo User.
        $user = $this->route('user');

        // 3. Pega o ID do usuário, se ele existir (para a regra 'ignore')
        $userId = $user ? $user->id : null;

        // 4. Define a regra de obrigatoriedade:
        // 'required' para POST (criação)
        // 'sometimes' para PUT/PATCH (opcional na atualização)
        $requirementRule = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'first_name' => [$requirementRule, 'string', 'max:255'],
            'last_name' => [$requirementRule, 'string', 'max:255'],
            'email' => [
                $requirementRule,
                'email',
                'max:255',
                // Garante que o email seja único na tabela 'users', ignorando o ID do usuário atual (se existir)
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $requirementRule, // 'required' no POST, 'sometimes' no PUT
                'string',
                'min:6',
            ],
            'role' => [
                'sometimes',
                'string',
                Rule::enum(UserRole::class) // Garante que o valor seja um dos valores de App\Enums\UserRole;
            ],
        ];
    }
}
