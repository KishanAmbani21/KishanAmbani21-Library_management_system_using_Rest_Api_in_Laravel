<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBooksRequest extends FormRequest
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
            'title' => 'required|string|max:50',
            'author' => 'required|string|max:50',
            'isbn' => 'required|string|size:13|unique:books,isbn',
            'status' => 'required|in:1,2',
            'publication_date' => 'required|date|before_or_equal:today',
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
            'title.required' => 'The title is required.',
            'title.max' => 'The title must not exceed 50 characters.',
            'author.required' => 'The author is required.',
            'author.max' => 'The author must not exceed 50 characters.',
            'isbn.required' => 'The ISBN is required.',
            'isbn.size' => 'The ISBN must be exactly 13 characters.',
            'isbn.unique' => 'The ISBN must be unique.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either 1 (AVAILABLE) or 2 (NOT AVAILABLE).',
            'publication_date.required' => 'The publication date is required.',
            'publication_date.date' => 'The publication date must be a valid date.',
            'publication_date.before_or_equal' => 'The publication date cannot be in the future.',
        ];
    }
}
