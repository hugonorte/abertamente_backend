<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed $post_id
 */
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
        $id = $this->route('footnote');
        $postId = $this->post_id;

        // Se for uma atualização e o post_id não for enviado, buscamos o post_id atual do registro
        // para garantir que a regra de unicidade continue vinculada ao post correto.
        if (($this->isMethod('PUT') || $this->isMethod('PATCH')) && !$postId && $id) {
            $footnote = \App\Models\Footnote::find($id);
            $postId = $footnote ? $footnote->post_id : null;
        }

        // Inicia a regra unique
        $uniqueRule = Rule::unique('footnotes')
            ->where('post_id', $postId);

        // Adiciona o 'ignore' APENAS se for um método de atualização (PUT ou PATCH)
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // 'bibliographic_reference' deve ser o nome do parâmetro na sua rota
            // Ex: /api/bibliographic_reference/{bibliographic_reference}
            $uniqueRule->ignore($id);
        }

        return [
            'post_id' => 'sometimes|required|integer|exists:posts,id',
            'description' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                $uniqueRule // Adiciona a regra (com ou sem ignore)
            ],
        ];
    }
}
