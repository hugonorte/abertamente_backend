<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $post_id
 */
class BibliographicReferenceRequest extends FormRequest
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
        // Inicia a regra unique
        $uniqueRule = Rule::unique('bibliographic_references')
            ->where('post_id', $this->post_id);

        // Adiciona o 'ignore' APENAS se for um método de atualização (PUT ou PATCH)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // 'bibliographic_reference' deve ser o nome do parâmetro na sua rota
            // Ex: /api/bibliographic_reference/{bibliographic_reference}
            $uniqueRule->ignore($this->route('bibliographic_reference'));
        }

        return [
            'post_id' => 'required|integer|exists:posts,id',
            'description' => [
                'required',
                'string',
                'min:10',
                $uniqueRule // Adiciona a regra (com ou sem ignore)
            ],
        ];
    }
}
