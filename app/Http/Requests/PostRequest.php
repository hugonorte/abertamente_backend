<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PostStatus;

class PostRequest extends FormRequest
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
        $postId = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:128', 'min:3', 'unique:posts,title,' . $postId],
            'slug' => ['string', 'max:255', 'min:3', 'unique:posts,slug,' . $postId],
            'tldr' => ['nullable', 'string', 'max:255', 'min:3'],
            'content' => ['required', 'string'],
            'image_path' => [$this->isMethod('post') ? 'required' : 'nullable', 'file', 'mimes:jpeg,jpg,png,avif,webp', 'max:2048'],
            'author_id' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::enum(PostStatus::class)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.unique' => 'Já existe um post com este título. Por favor, escolha outro.',
            'slug.unique' => 'Já existe um post com este slug (URL amigável). Por favor, altere o título ou o slug.',
        ];
    }
}
