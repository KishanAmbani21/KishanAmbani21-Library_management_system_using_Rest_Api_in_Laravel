<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBorrowRequest;
use App\Models\Borrow;
use App\Services\BorrowService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BorrowController extends Controller
{
    use JsonResponseTrait;
    protected $borrowService;

    /**
     * __construct
     *
     * @param  mixed $borrowService
     * @return void
     */
    public function __construct(BorrowService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    /**
     * create
     *
     * @param  mixed $request
     * @return void
     */
    public function create(CreateBorrowRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('messages.auth.required', 401);
        }
        if (!Gate::allows('createBorrow', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->create($request->validated());
    }

    /**
     * getBorrows
     *
     * @return void
     */
    public function getBorrows()
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->getBorrows();
    }

    /**
     * getBorrowByUuid
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getBorrowByUuid($uuid)
    {
        $authUser = auth()->user();
        $borrowRecord = Borrow::where('uuid', $uuid)
            ->where('user_id', $authUser->id)
            ->first();

        if (!Gate::allows('crud', [$authUser, $borrowRecord]) && $borrowRecord->user_id !== $authUser->id) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->getBorrowsByUuid($uuid);
    }

    /**
     * search
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->searchBorrows($request);
    }

    /**
     * returnBook
     *
     * @param  mixed $id
     * @return void
     */
    public function returnBook($id)
    {
        $user = Auth::user();
        if (!Gate::allows('createBorrow', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->returnBook($id);
    }

    /**
     * listReturnedBooks
     *
     * @return void
     */
    public function listReturnedBooks()
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return  $this->borrowService->getReturnedBooks();
    }

    /**
     * getBorrowsByUser
     *
     * @param  mixed $userId
     * @return void
     */
    public function getBorrowsByUser($userId)
    {
        $authUser = auth()->user();

        if (Gate::allows('crud', $authUser) || $authUser->id == $userId) {
            return $this->borrowService->getBorrowsByUser($userId);
        }
        return $this->errorResponse('messages.user.unauthorized', 403);
    }

    /**
     * getBorrowsByBook
     *
     * @param  mixed $bookId
     * @return void
     */
    public function getBorrowsByBook($bookId)
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->borrowService->getBorrowsByBook($bookId);
    }
}
