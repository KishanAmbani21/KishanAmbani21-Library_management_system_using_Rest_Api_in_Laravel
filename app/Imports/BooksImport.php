<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class BooksImport implements ToModel, WithValidation
{
    /**
     * model
     *
     * @param  mixed $row
     * @return void
     */
    public function model(array $row)
    {
        return new Book([
            'title'            => $row[0],
            'author'           => $row[1],
            'isbn'             => $row[2],
            'status'           => $row[3],
            'publication_date' => $row[4],
        ]);
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '0' => 'required|string|max:50',
            '1' => 'required|string|max:50',
            '2' => 'required|string|size:13',
            '3' => 'required|in:1,2',
            '4' => 'required|date',
        ];
    }
}
