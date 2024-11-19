<?php

namespace App\Repositories;

use App\Enums\BooksEnum;
use App\Models\Book;
use App\Traits\JsonResponseTrait;

class BookRepository extends BaseRepository
{
    use JsonResponseTrait;
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Book $model)
    {
        $this->model = $model;
    }

    /**
     * getBooksByStatus
     *
     * @param  mixed $status
     * @return void
     */
    public function getBooksByStatus(BooksEnum $status)
    {
        return $this->model->where('status', $status->value)->get();
    }

    /**
     * updateStatus
     *
     * @param  mixed $bookId
     * @param  mixed $status
     * @return void
     */
    public function updateStatus(int $bookId, int $status)
    {
        return $this->model->where('id', $bookId)->update(['status' => $status]);
    }

    /**
     * findById
     *
     * @param  mixed $id
     */
    public function findById($id)
    {
        $book = $this->model->find($id);
        if (!$book) {
            return $this->errorResponse('messages.borrow.book_not_found', 404);
        }
        return $book;
    }

    /**
     * search
     *
     * @param  mixed $query
     * @param  mixed $status
     * @return void
     */
    public function search($query, $status = null)
    {
        return $this->model->search($query, $status);
    }
}
