<?php

namespace App\Repositories;

use App\Traits\JsonResponseTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseRepository
{
    use JsonResponseTrait;
    protected $model;

    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
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
     * findByUuid
     *
     * @param  mixed $uuid
     */
    public function findByUuid($uuid)
    {
        if (!Str::isUuid($uuid)) {
            throw new \InvalidArgumentException("Invalid UUID format provided.");
        }

        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * create
     *
     * @param  mixed $data
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * update
     *
     * @param  mixed $uuid
     * @param  mixed $data
     * @return void
     */
    public function update($uuid, array $data)
    {
        return $this->model->where('uuid', $uuid)->update($data);
    }

    /**
     * delete
     *
     * @param  mixed $uuid
     * @return void
     */
    public function delete($uuid)
    {
        return  $this->model->where('uuid', $uuid)->delete($uuid);
    }

    /**
     * calculatePenalty
     *
     * @param  mixed $dueDate
     * @return void
     */
    public function calculatePenalty($dueDate)
    {
        $now = Carbon::now();
        $dueDate = Carbon::parse($dueDate);
        if ($now->greaterThan($dueDate)) {
            $daysOverdue = $now->diffInDays($dueDate);

            return $daysOverdue * 10;
        }
        return 0;
    }
}
