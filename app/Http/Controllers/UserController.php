<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Class UserController
 * Handles user-related HTTP requests and interactions with the UserService
 */
class UserController extends Controller
{
    use JsonResponseTrait;

    protected $userService;

    /**
     * UserController constructor
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * updateUser
     *
     * @param  mixed $request
     * @param  mixed $uuid
     * @return void
     */
    public function updateUser(UserUpdateRequest $request, $uuid, User $user)
    {
        if (!Gate::allows('crud', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->userService->update($uuid, $request->validated());
    }

    /**
     * deleteUser
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function deleteUser($uuid, User $user): JsonResponse
    {
        if (!Gate::allows('crud', $user)) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->userService->delete($uuid);
    }

    /**
     * Get all users
     *
     * @return JsonResponse
     */
    public function getAllUsers(User $model)
    {
        if (!Gate::allows('crud', auth()->user())) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->userService->getAllUsers();
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getUserByUuid($uuid)
    {
        $authUser = auth()->user();
        if (!Gate::allows('crud', $authUser) && $authUser->uuid !== $uuid) {
            return $this->errorResponse('messages.user.unauthorized', 403);
        }
        return $this->userService->getByUuid($uuid);
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
        return $this->userService->searchUsers($request);
    }
}
