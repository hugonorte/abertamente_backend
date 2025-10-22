<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FootnoteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Inicia a regra unique
        $uniqueRule = Rule::unique('footnotes')
            ->where('post_id', $this->post_id);

        // Adiciona o 'ignore' APENAS se for um método de atualização (PUT ou PATCH)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // 'bibliographic_reference' deve ser o nome do parâmetro na sua rota
            // Ex: /api/bibliographic_reference/{bibliographic_reference}
            $uniqueRule->ignore($this->route('footnote'));
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
