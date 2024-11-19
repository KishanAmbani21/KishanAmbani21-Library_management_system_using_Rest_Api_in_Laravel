<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Services\UserService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * Class UserController
 * Handles user-related HTTP requests and interactions with the UserService
 */
class NotificationController extends Controller
{
    use JsonResponseTrait;

    protected $notificationService;

    /**
     * UserController constructor
     *
     * @param UserService $userService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * sendOverdueNotifications
     *
     * @return JsonResponse
     */
    public function sendOverdueNotifications(): JsonResponse
    {
        $this->notificationService->sendOverdueNotifications();

        return response()->json(['message' => 'Overdue notifications sent successfully.']);
    }
}
