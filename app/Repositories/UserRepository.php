<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * searchUsers
     *
     * @param  mixed $query
     * @return void
     */
    public function searchUsers($query)
    {
        return $this->model->search($query);
    }
}
