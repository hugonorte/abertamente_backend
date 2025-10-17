<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $id
 */
class AuthorRequest extends FormRequest
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
        return [
            //['name', 'email', 'bio', 'main_title', 'preferred_social_network', 'preferred_social_network_username'];
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:authors,email',
            'bio' => 'nullable|string',
            'main_title' => 'nullable|string|max:255',
            'preferred_social_network' => 'nullable|string|max:255',
            'preferred_social_network_username' => 'nullable|string|max:255',
        ];
    }
}
