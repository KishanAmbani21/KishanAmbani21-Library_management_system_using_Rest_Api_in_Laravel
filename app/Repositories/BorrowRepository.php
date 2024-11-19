<?php

namespace App\Repositories;

use App\Models\Borrow;

class BorrowRepository extends BaseRepository
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Borrow $model)
    {
        $this->model = $model;
    }

    /**
     * getAllWithRelations
     *
     * @return void
     */
    public function getAllWithRelations()
    {
        return $this->model->with(['book', 'user'])->get();
    }

    /**
     * getReturnedBooks
     *
     * @return void
     */
    public function getAllReturnedBooks()
    {
        return $this->model->with(['user', 'book'])
            ->where('returned', true)
            ->get();
    }

    /**
     * getByUser
     *
     * @param  mixed $userId
     * @return void
     */
    public function getByUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['book'])
            ->get()
            ->pluck('book');
    }

    /**
     * getByBook
     *
     * @param  mixed $bookId
     * @return void
     */
    public function getByBook($bookId)
    {
        return $this->model->where('book_id', $bookId)
            ->with(['user'])
            ->get()
            ->pluck('user');
    }

    /**
     * countByUserId
     *
     * @param  mixed $userId
     */
    public function countByUserId($userId)
    {
        return $this->model->where('user_id', $userId)
            ->where('returned', false)
            ->count();
    }

    /**
     * findActiveBorrowByBook
     *
     * @param  mixed $bookId
     * @return void
     */
    public function findActiveBorrowByBook($bookId)
    {
        return $this->model->where('book_id', $bookId)
            ->whereNull('return_date')
            ->first();
    }

    /**
     * getByBookAndUser
     *
     * @param  mixed $bookId
     */
    public function getByBookAndUser($bookId)
    {
        return $this->model->where('book_id', $bookId)
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * search
     *
     * @param  mixed $query
     * @return void
     */
    public function search($query)
    {
        return $this->model->search($query);
    }
}
