<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * Handles user-related operations and business logic
 */
class UserService
{
    use JsonResponseTrait;

    protected $userRepository;

    /**
     * __construct
     *
     * @param  mixed $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * create
     *
     * @param  mixed $data
     * @return void
     */
    public function create(array $data)
    {
        try {
            $user = $this->userRepository->create($data);
            Log::channel('additions')->info('New user created', ['user_id' => $user->id, 'name'=> $user->name, 'email' => $user->email, 'roles'=> $user->roles]);
            return $this->successResponse($user, 'messages.user.created_success', 201);
        } catch (Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('messages.user.creation_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * login
     *
     * @param  mixed $credentials
     * @return void
     */
    public function login(array $credentials)
    {
        try {

            if (!Auth::attempt($credentials)) {
                return $this->errorResponse('messages.auth.credentials_incorrect', 401);
            }
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;
            return $this->successResponse(['token' => $token], 'messages.auth.login_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.auth.login_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        try {
            if (Auth::check()) {
                Auth::user()->token()->revoke();
                return $this->successResponse(null, 'messages.auth.logout_success', 200);
            }
            return $this->errorResponse('messages.auth.unauthenticated', 401);
        } catch (Exception $e) {
            return $this->errorResponse('messages.auth.logout_failed', 500, ['original_error' => $e->getMessage()]);
        }
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
        try {
            
            $user = $this->userRepository->findByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.not_found', 404);
            }
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $this->userRepository->update($uuid, $data);

            $updatedUserdata = $this->userRepository->findByUuid($uuid);

            Log::channel('updates')->info('User updated successfully', ['user_id' => $updatedUserdata->id,'updated_fields' => array_keys($data)]);
            return $this->successResponse($updatedUserdata, 'messages.user.update_success', 200);
        } catch (Exception $e) {
            Log::error('User update failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('messages.user.update_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * delete
     *
     * @param  mixed $uuid
     * @return JsonResponse
     */
    public function delete($uuid): JsonResponse
    {
        try {
            $user = $this->userRepository->findByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.not_found', 404);
            }
            $this->userRepository->delete($uuid);
            return $this->successResponse(null, 'messages.user.delete_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.user.delete_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getAllUsers
     *
     * @return void
     */
    public function getAllUsers()
    {
        try {
            $users = $this->userRepository->getAll();
            return $this->successResponse($users, 'messages.user.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.user.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getByUuid
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getByUuid($uuid)
    {
        try {
            $user = $this->userRepository->findByUuid($uuid);
            if (!$user) {
                return $this->errorResponse('messages.user.not_found', 404);
            }
            return $this->successResponse($user, 'messages.user.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.user.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * searchUsers
     *
     * @param  mixed $query
     * @return void
     */
    public function searchUsers(Request $request)
    {
        try {
            $response = null;
            $query = $request->input('query');
            if (!$query) {
                $response = $this->errorResponse('messages.user.query_required', 400);
            }
            $users = $this->userRepository->searchUsers($query)->get();
            if ($users->isEmpty()) {
                $response = $this->errorResponse('messages.user.not_found', 404);
            }
            $response = $this->successResponse($users, 'messages.user.search_success', 200);
        } catch (Exception $e) {
            $response = $this->errorResponse('messages.user.search_failed', 500, ['original_error' => $e->getMessage()]);
        }
        return $response;
    }
}
