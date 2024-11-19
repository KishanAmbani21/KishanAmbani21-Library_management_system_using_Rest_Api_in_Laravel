<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportBooksRequest extends FormRequest
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
            'data' => 'required|array',
            'data.*.title' => 'required|string|max:50',
            'data.*.author' => 'required|string|max:50',
            'data.*.isbn' => 'required|string|size:13|unique:books,isbn',
            'data.*.status' => 'required|in:1,2',
            'data.*.publication_date' => 'required|date|before_or_equal:today',
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
            'data.required' => 'Data is required.',
            'data.array' => 'Data must be an array.',
            'data.*.title.required' => 'Title is required.',
            'data.*.author.required' => 'Author is required.',
            'data.*.isbn.required' => 'ISBN is required.',
            'data.*.isbn.size' => 'ISBN must be 13 characters long.',
            'data.*.isbn.unique' => 'ISBN must be unique.',
            'data.*.status.required' => 'Status is required.',
            'data.*.status.in' => 'Status must be either 1 or 2.',
            'data.*.publication_date.required' => 'Publication date is required.',
            'data.*.publication_date.date' => 'Publication date must be a valid date.',
            'data.*.publication_date.before_or_equal' => 'The publication date cannot be in the future.',
        ];
    }
}
