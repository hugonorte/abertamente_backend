<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'tldr' => ['nullable', 'string', 'max:255', 'min:3'],
            'content' => ['required', 'string'],
            'image_path' => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'mimes:jpeg,jpg,png,avif,webp', 'max:2048'],
            'author_id' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
        ];
    }
}
