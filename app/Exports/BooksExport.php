<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        return Book::withTrashed()->orderBy('id')->get();
    }


    public function headings(): array
    {
        return [
            'id',
            'uuid',
            'title',
            'author',
            'isbn',
            'status',
            'publication_date',
            'deleted_at',
            'created_at',
            'updated_at',
        ];
    }
}
