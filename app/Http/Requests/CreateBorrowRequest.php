<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBorrowRequest extends FormRequest
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
            'user_id' => 'prohibited',
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'date|before_or_equal:today',
            'due_date' => 'prohibited',
            'due_date_text' => 'nullable',
            'returned' => 'boolean',
            'return_date' => 'nullable|date',
            'total_penalty' => 'nullable|numeric|min:0',
            'penalty_paid' => 'nullable|date',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'user_id.prohibited' => 'The user ID cannot be provided for this request.',
            'book_id.required' => 'The book ID is required.',
            'book_id.exists' => 'The selected book does not exist in our records.',
            'borrow_date.date' => 'The borrow date must be a valid date.',
            'due_date.prohibited' => 'The due date cannot be manually set.',
            'returned.boolean' => 'The returned field must be true or false.',
            'return_date.date' => 'The return date must be a valid date.',
            'total_penalty.numeric' => 'The total penalty must be a number.',
            'total_penalty.min' => 'The total penalty must be at least 0.',
            'penalty_paid.date' => 'The penalty paid date must be a valid date.',
            'borrow_date.before_or_equal' => 'The publication date cannot be in the future.',
        ];
    }
}
