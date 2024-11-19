<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueBookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowing;
    protected $penalty;
    /**
     * Create a new notification instance.
     */
    public function __construct($borrowing,$penalty)
    {
        $this->borrowing = $borrowing;
        $this->penalty = $penalty;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Overdue Book Reminder')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have overdue books that need to be returned.')
            ->line('Book: ' . $this->borrowing->book->title)
            ->line('Due Date: ' . $this->borrowing->due_date)
            ->line('Penalty: ₹' . $this->penalty)
            ->action('Pay Penalty', url('/pay-penalty/' . $this->borrowing->id))
            ->line('Please return them as soon as possible to avoid further penalties.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'book_id' => $this->borrowing->book->id,
            'due_date' => $this->borrowing->due_date,
            'penalty' => $this->penalty,
        ];
    }
}
