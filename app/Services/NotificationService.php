<?php

namespace App\Services;

use App\Notifications\OverdueBookNotification;
use App\Repositories\NotificationRepository;

class NotificationService
{
    protected $notificationRepository;

    /**
     * __construct
     *
     * @param  mixed $notificationRepository
     * @return void
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * sendOverdueNotifications
     *
     * @return void
     */
    public function sendOverdueNotifications()
    {
        $overdueBorrows = $this->notificationRepository->getOverdueBorrows();

        foreach ($overdueBorrows as $borrowing) {

            $penalty = $this->notificationRepository->calculatePenalty($borrowing->due_date);

            $borrowing->user->notify(new OverdueBookNotification($borrowing, $penalty));

            $this->notificationRepository->create([
                'user_id' => $borrowing->user->id,
                'book_id' => $borrowing->book->id,
                'due_date' => $borrowing->due_date,
                'sent_at' => now(),
                'penalty' => $penalty,
            ]);
        }
    }
}
