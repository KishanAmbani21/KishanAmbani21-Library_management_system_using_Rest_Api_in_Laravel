<?php

namespace App\Repositories;

use App\Models\Borrow;
use App\Models\EmailNotification;
use Carbon\Carbon;

class NotificationRepository extends BaseRepository
{
    protected $borrow;
    /**
     * __construct
     *
     * @param  mixed $emailNotification
     * @return void
     */
    public function __construct(EmailNotification $model, Borrow $borrow)
    {
        $this->model = $model;
        $this->borrow = $borrow;
    }

    /**
     * create
     *
     * @param  mixed $data
     * @return void
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * getAll
     *
     * @return void
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * getOverdueBorrows
     *
     * @return void
     */
    public function getOverdueBorrows()
    {
        $today = Carbon::now();

        return $this->model->where('due_date', '<', $today)
            ->where('returned', false)
            ->with('user', 'book')
            ->get();
    }
}
