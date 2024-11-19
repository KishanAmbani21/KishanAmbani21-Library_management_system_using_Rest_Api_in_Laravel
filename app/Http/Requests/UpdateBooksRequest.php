<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBooksRequest extends FormRequest
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
            'user_id' => 'exists:users,id',
            'title' => 'string|max:50',
            'author' => 'string|max:50',
            'isbn' => 'string|size:13',
            'status' => 'in:1,2',
            'publication_date' => 'date|before_or_equal:today',
        ];
    }

    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        return [
            'user_id.exists' => 'The selected user ID must exist in the users table.',
            'title.max' => 'The title must not exceed 50 characters.',
            'author.max' => 'The author must not exceed 50 characters.',
            'isbn.size' => 'The ISBN must be exactly 13 characters.',
            'isbn.unique' => 'The ISBN must be unique.',
            'status.in' => 'The status must be either 1 (AVAILABLE) or 2 (NOT AVAILABLE).',
            'publication_date.date' => 'The publication date must be a valid date.',
            'publication_date.before_or_equal' => 'The publication date cannot be in the future.',
        ];
    }
}
