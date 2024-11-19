<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;

/**
 * Class LogoutController
 * Handles user logout functionality.
 */
class LogoutController extends Controller
{
    protected $userService;

    /**
     * __construct
     *
     * @param  mixed $userService
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        return $this->userService->logout();
    }
}
