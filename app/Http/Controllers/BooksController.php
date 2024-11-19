<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBooksRequest;
use App\Http\Requests\ImportBooksRequest;
use App\Http\Requests\UpdateBooksRequest;
use App\Models\User;
use App\Services\BookService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BooksController extends Controller
{
    use JsonResponseTrait;
    protected $bookService;

    /**
     * __construct
     *
     * @param  mixed $bookService
     * @return void
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * create
     *
     * @param  mixed $request
     * @return void
     */
    public function create(CreateBooksRequest $request)
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->bookService->create($request->validated());
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $uuid
     * @return void
     */
    public function update(UpdateBooksRequest $request, $uuid, User $user)
    {
        if (!Gate::allows('crud', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->bookService->update($uuid, $request->validated());
    }

    /**
     * delete
     *
     * @param  mixed $uuid
     * @return void
     */
    public function delete($uuid, User $user)
    {
        if (!Gate::allows('crud', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->bookService->delete($uuid);
    }

    /**
     * getBooks
     *
     * @return void
     */
    public function getBooks()
    {
        return $this->bookService->getBooks();
    }

    /**
     * getBookByUuid
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getBookByUuid($uuid)
    {
        return $this->bookService->getByUuid($uuid);
    }

    /**
     * getBooksByStatus
     *
     * @param  mixed $status
     * @return void
     */
    public function getBooksByStatus($status)
    {
        return $this->bookService->getBooksByStatus($status);
    }

    /**
     * search
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request): JsonResponse
    {
        return $this->bookService->searchBooks($request);
    }

    /**
     * import
     *
     * @param  mixed $request
     * @return void
     */
    public function import(ImportBooksRequest $request)
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        $userId = auth()->id();
        return $this->bookService->importBooks($request->input('data'), $userId);
    }

    /**
     * export
     *
     * @return void
     */
    public function export()
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->bookService->export();
    }
}
