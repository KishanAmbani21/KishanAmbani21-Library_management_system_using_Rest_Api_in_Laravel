<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'string|max:50',
            'email' => [
                'email',
                'max:50',
                Rule::unique('users')->ignore($this->uuid, "uuid"),
            ],
            'roles' => 'in:2,3',
            'password' => 'min:8'
        ];
    }

    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 50 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 50 characters.',
            'email.unique' => 'The email has already been taken.',
            'roles.in' => 'The selected role is invalid. Please choose either 2 (admin) or 3 (user).',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
